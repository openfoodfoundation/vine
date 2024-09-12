<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head, Link} from '@inertiajs/vue3';
import {onMounted, ref} from "vue";
import dayjs from "dayjs";
import relativeTime from "dayjs/plugin/relativeTime";
import utc from "dayjs/plugin/utc"

dayjs.extend(relativeTime);
dayjs.extend(utc);

const $props = defineProps({
    voucherId: {
        type: String,
        required: false,
    },
});

const voucher = ref({})

onMounted(() => {
    getVoucher()
});

function getVoucher() {
    axios.get('/my-team-vouchers/' + $props.voucherId + '?cached=false&relations=createdByTeam,allocatedToServiceTeam,voucherRedemptions.redeemedByUser,voucherRedemptions.redeemedByTeam,voucherSet').then(response => {
        voucher.value = response.data.data
    }).catch(error => {
        console.log(error)
    })
}

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
                <div class="mt-4" v-if="voucher.voucher_short_code">
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
                Voucher details
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
                        Redeemed by: <span class="font-bold">{{ redemption.redeemed_by_user.name }} ({{ redemption.redeemed_by_team.name }})</span>
                    </div>
                    <div v-if="redemption.created_at">
                        Redeemed at: <span class="font-bold">{{ dayjs.utc(redemption.created_at).fromNow() }} ({{ dayjs(redemption.created_at) }})</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="pb-32"></div>
    </AuthenticatedLayout>
</template>
