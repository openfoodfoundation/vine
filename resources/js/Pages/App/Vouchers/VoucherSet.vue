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
    voucherSetId: {
        type: String,
        required: false,
    },
});

const voucherSet = ref({})

onMounted(() => {
    getVoucherSet()
});

function getVoucherSet() {
    axios.get('/my-team-voucher-sets/' + $props.voucherSetId + '?cached=false&relations=createdByTeam,allocatedToServiceTeam,voucherSetMerchantTeams.merchantTeam').then(response => {
        voucherSet.value = response.data.data
    }).catch(error => {
        console.log(error)
    })
}

</script>

<template>
    <Head title="Voucher Set"/>

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-normal text-xl text-gray-800 leading-tight">Voucher Set</h2>
        </template>

        <div class="card">
            <h2 class="opacity-25">
                #{{ voucherSet.id }}
            </h2>
            <div v-if="voucherSet.is_test" class="font-bold text-red-500 text-sm">
                Test voucher set
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                Voucher set details
            </div>

            <div v-if="voucherSet.created_by_team">
                <div>
                    Created by: <span class="font-bold">{{ voucherSet.created_by_team.name }}</span>
                </div>
            </div>
            <div v-if="voucherSet.allocated_to_service_team_id">
                <div>
                    Allocated to: <span class="font-bold">{{ voucherSet.allocated_to_service_team.name }}</span>
                </div>
            </div>
            <div>
                Total set value: <span class="font-bold">${{ voucherSet.total_set_value / 100 }}</span>
            </div>
            <div>
                Total remaining value: <span class="font-bold">${{ voucherSet.total_set_value_remaining / 100 }}</span>
            </div>
            <div>
                Vouchers: <span class="font-bold">{{ voucherSet.num_vouchers }}</span>
            </div>
            <div>
                Redemptions: <span class="font-bold">{{ voucherSet.num_voucher_redemptions }}</span>
            </div>
            <div>
                Redeemed percentage: <span class="font-bold">{{ Math.round(((voucherSet.total_set_value - voucherSet.total_set_value_remaining) / voucherSet.total_set_value) * 10000) / 100 }}%</span>
            </div>
            <div v-if="voucherSet.last_redemption_at">
                Last redeemed at: {{ dayjs.utc(voucherSet.last_redemption_at).fromNow() }} ({{ dayjs(voucherSet.last_redemption_at) }})
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                Redeemable merchants
            </div>

            <div v-if="voucherSet.voucher_set_merchant_teams">
                <ul v-for="voucherSetMerchant in voucherSet.voucher_set_merchant_teams" class="list-disc ml-4">
                    <li>{{ voucherSetMerchant.merchant_team.name }}</li>
                </ul>
            </div>
        </div>

        <div class="pb-32"></div>
    </AuthenticatedLayout>
</template>
