<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head} from '@inertiajs/vue3';
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
    axios.get('/my-team-vouchers/' + $props.voucherId + '?cached=false&relations=createdByTeam,allocatedToServiceTeam,voucherRedemptions.redeemedByUser,voucherRedemptions.redeemedByTeam').then(response => {
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
            <h2 class="font-normal text-xl text-gray-800 leading-tight">Voucher</h2>
        </template>

        <div class="card">
            <h2 v-if="voucher.voucher_short_code">
                #{{ voucher.voucher_short_code }}
            </h2>
            <div class="text-sm opacity-25">
                {{ voucher.id }}
            </div>
            <div v-if="voucher.is_test" class="font-bold text-red-500 text-sm">
                Test voucher
            </div>

            <div v-if="voucher.allocated_to_service_team_id" class="mt-2 text-sm">
                <div>
                    Allocated to: <span class="font-bold">{{ voucher.allocated_to_service_team.name }}</span>
                </div>
            </div>
            <div v-if="voucher.created_by_team" class="text-sm">
                <div>
                    Created by: <span class="font-bold">{{ voucher.created_by_team.name }}</span>
                </div>
            </div>
            <div class="text-sm">
                Created at: <span class="font-bold">{{ dayjs.utc(voucher.created_at).fromNow() }} ({{ dayjs(voucher.created_at) }})</span>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                Voucher details
            </div>

            <div>
                Original value: <span class="font-bold">${{ voucher.voucher_value_original / 100 }}</span>
            </div>
            <div>
                Remaining value: <span class="font-bold">${{ voucher.voucher_value_remaining / 100 }}</span>
            </div>
            <div>
                Redemptions: <span class="font-bold">{{ voucher.num_voucher_redemptions }}</span>
            </div>
            <div v-if="voucher.last_redemption_at">
                Last redeemed at: <span class="font-bold">{{ dayjs.utc(voucher.last_redemption_at).fromNow() }} ({{ dayjs(voucher.last_redemption_at) }})</span>
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
