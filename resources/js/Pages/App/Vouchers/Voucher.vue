<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head, Link} from '@inertiajs/vue3';
import {nextTick, onMounted, ref, watch} from "vue";
import dayjs from "dayjs";
import relativeTime from "dayjs/plugin/relativeTime";
import localizedFormat from "dayjs/plugin/localizedFormat";
import utc from "dayjs/plugin/utc"
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import swal from "sweetalert2";

dayjs.extend(relativeTime);
dayjs.extend(localizedFormat)
dayjs.extend(utc);

const $props = defineProps({
    voucherId: {
        type: String,
        required: false,
    },
});

const beneficiaryEmail = ref('');
const distributionSectionRef = ref(null);
const emailIsValid = ref(false);
const showDistributionSection = ref(false);
const voucher = ref({})

onMounted(() => {
    getVoucher()
});

function cancelDistribution() {
    showDistributionSection.value = false;
    beneficiaryEmail.value = '';
}


function confirmVoucherDistribution() {
    if (emailIsValid) {

        swal.fire({
            title: "Are you sure?",
            icon: "warning",
            text: "You cannot resend this voucher to a different email address after this. You can resend it to the same one, but not a different one. PLEASE double-check you have the details correct.",
            showConfirmButton: true,
            showCancelButton: true,
            confirmButtonText: "I do, send it!",
            cancelButtonText: "Go back"
        }).then(result => {

            if (result.isConfirmed) {

                createVoucherDistribution();

            }

        });
    }
}

function createVoucherDistribution() {

    let payload = {
        voucher_id: voucher.value.id,
        beneficiary_email: beneficiaryEmail.value,
    };

    if (voucher.value.voucher_beneficiary_distributions?.length) {
        payload = {
            resend_beneficiary_distribution_id: voucher.value.voucher_beneficiary_distributions[0].id,
        }
    }

    axios.post('/voucher-beneficiary-distributions', payload).then(response => {

        swal.fire({
            title: "Nice!",
            icon: "success",
            text: response.data.data.message,
            showConfirmButton: false,
            timer: 600
        });

        cancelDistribution();
        getVoucher();

    }).catch(error => {

        swal.fire({
            title: "Oops!",
            icon: "error",
            text: error.response.data.meta.message
        });

        console.log(error);

    })
}

function getVoucher() {
    axios.get('/my-team-vouchers/' + $props.voucherId + '?cached=false&relations=createdByTeam,allocatedToServiceTeam,voucherBeneficiaryDistributions,voucherRedemptions.redeemedByUser,voucherRedemptions.redeemedByTeam,voucherSet').then(response => {
        voucher.value = response.data.data
    }).catch(error => {
        console.log(error)
    })
}


function openDistributionSection() {
    showDistributionSection.value = true;

    nextTick(() => {
        if (distributionSectionRef.value) {
            distributionSectionRef.value.scrollIntoView({behavior: 'smooth'});
        }
    })
}

function validateEmail() {
    const parts = beneficiaryEmail.value.toString().split('@');

    // Check that there are exactly two parts, the local and domain parts
    if (parts.length !== 2) {
        return false;
    }

    const localPart = parts[0];
    const domainPart = parts[1];

    // Ensure the local part has some characters and that the domain contains a period
    if (localPart.length === 0 || domainPart.length < 3 || !domainPart.includes('.')) {
        return false;
    }

    const domainParts = domainPart.split('.');

    // Ensure that the period is not at the start or end of the domain part
    if (domainParts[0].length === 0 || domainParts[domainParts.length - 1].length === 0) {
        return false;
    }

    return true;
}

watch(beneficiaryEmail, () => {
    emailIsValid.value = validateEmail()
})


</script>

<template>
    <Head title="Voucher"/>

    <AuthenticatedLayout>
        <template #header>
            Voucher
        </template>

        <div class="grid grid-cols-2 gap-8 container mx-auto mt-8">
            <div class="card">
                <div class="card-header">
                    Voucher Details

                </div>
                <h2 class="opacity-25">
                    ID: {{ voucher.id }}
                </h2>
                <div v-if="voucher.voucher_short_code" class="mt-4">
                    <h2>
                        Short Code: {{ voucher.voucher_short_code }}
                    </h2>
                </div>
                <div v-if="voucher.is_test" class="font-bold text-red-500 text-sm">
                    Test voucher
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    Voucher set
                </div>

                <div v-if="voucher.voucher_set_id">
                    <Link :href="route('voucher-set', {id:voucher.voucher_set_id})">{{ voucher.voucher_set_id }}</Link>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                Usage overview
            </div>

            <div class="grid grid-cols-4 gap-y-12 text-center mt-8">
                <div>
                    <div class="font-bold text-3xl">
                        ${{ voucher.voucher_value_original / 100 }}
                    </div>
                    Original value
                </div>
                <div>
                    <div class="font-bold text-3xl">
                        ${{ voucher.voucher_value_remaining / 100 }}
                    </div>
                    Remaining value
                </div>
                <div>
                    <div class="font-bold text-3xl">
                        {{ voucher.num_voucher_redemptions ?? '0' }}
                    </div>
                    # Redemptions
                </div>


                <div v-if="voucher.last_redemption_at">
                    <div>
                        Last redeemed
                    </div>
                    <div class="font-bold text-3xl">
                        {{ dayjs.utc(voucher.last_redemption_at).fromNow() }}
                    </div>
                    <div class="text-xs">
                        ({{ dayjs(voucher.last_redemption_at) }})
                    </div>
                </div>

                <div v-if="voucher.voucher_set?.expires_at">
                    <div>
                        Expires
                    </div>
                    <div class="font-bold text-3xl">
                        {{ dayjs.utc(voucher.voucher_set.expires_at).fromNow() }}
                    </div>
                    <div class="text-xs">
                        ({{ dayjs(voucher.voucher_set.expires_at) }})
                    </div>
                </div>

            </div>

        </div>

        <div class="grid grid-cols-2 gap-8 container mx-auto">
            <div class="card">
                <div class="card-header">
                    Created by team
                </div>

                <div v-if="voucher.created_by_team">
                    {{ voucher.created_by_team.name }}
                </div>
                <div v-if="voucher.created_at" class="text-xs mt-2">
                    Created at: {{ dayjs.utc(voucher.created_at).fromNow() }} ({{ dayjs(voucher.created_at) }})
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    Allocated to team
                </div>

                <div v-if="voucher.allocated_to_service_team">
                    {{ voucher.allocated_to_service_team.name }}
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                Voucher redemptions
            </div>

            <div v-if="voucher.voucher_redemptions && voucher.voucher_redemptions.length" class="text-sm">
                <div v-for="redemption in voucher.voucher_redemptions" class="border-b py-2 sm:p-2">
                    <div>
                        Redeemed amount: <span class="font-bold">${{ redemption.redeemed_amount / 100 }}</span>
                    </div>
                    <div v-if="redemption.redeemed_by_user && redemption.redeemed_by_team">
                        Redeemed by: <span class="font-bold">{{
                            redemption.redeemed_by_user.name
                        }} ({{ redemption.redeemed_by_team.name }})</span>
                    </div>
                    <div v-if="redemption.created_at">
                        Redeemed at: <span class="font-bold">{{
                            dayjs.utc(redemption.created_at).fromNow()
                        }} ({{ dayjs(redemption.created_at) }})</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header flex justify-between">
                Beneficiary Distributions


                <div>
                    <PrimaryButton v-if="voucher.voucher_beneficiary_distributions?.length"
                                   @click="createVoucherDistribution">
                        Resend
                    </PrimaryButton>

                    <PrimaryButton v-else @click="openDistributionSection">
                        Send to beneficiary
                    </PrimaryButton>
                </div>

            </div>

            <div class="mt-4">
                <div v-if="voucher.voucher_beneficiary_distributions?.length">

                    <div v-for="(distribution, i) in voucher.voucher_beneficiary_distributions" class="py-1 border-b border-gray-200 flex justify-between">
                        <div class="font-medium ">
                            {{ i === 0 ? 'Distributed' : 'Re-distributed' }} {{ dayjs.utc(distribution.created_at).fromNow() }}
                        </div>

                        <div class=" italic text-gray-500">
                            {{ dayjs(distribution.created_at).format('llll') }}
                        </div>
                    </div>
                </div>

                <div v-else>
                    This voucher has not been distributed yet!
                </div>
            </div>
        </div>


        <div v-if="showDistributionSection" ref="distributionSectionRef" class="card">
            <div class="card-header">
                Distribute to beneficiary
            </div>

            <div class="mt-8">
                <label for="beneficiary-email">
                    Please enter the beneficiary's email
                </label>
                <div class="mt-2 flex justify-between">
                    <div class="w-full">
                        <input id="beneficiary-email" v-model="beneficiaryEmail" class="md:w-1/3"
                               placeholder="beneficiary@example.com" type="email">
                        <div v-if="!emailIsValid" class="mt-1 ml-1 text-xs italic text-red-500">
                            Invalid email &hyphen; please check
                        </div>
                        <div v-else class="mt-1 ml-1 text-xs italic text-gray-500">
                            Looks good!
                        </div>
                    </div>

                    <div class="flex space-x-4">

                        <SecondaryButton @click="cancelDistribution">
                            Cancel
                        </SecondaryButton>

                        <PrimaryButton :disabled="!emailIsValid"
                                       class="disabled:cursor-not-allowed disabled:opacity-25"
                                       @click="confirmVoucherDistribution">
                            Send
                        </PrimaryButton>
                    </div>
                </div>
            </div>

        </div>

        <div class="pb-32"></div>
    </AuthenticatedLayout>
</template>
