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
    axios.get('/my-team-vouchers/' + $props.voucherId + '?cached=false&relations=createdByTeam,allocatedToServiceTeam,voucherRedemptions').then(response => {
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
            <div v-if="voucher.is_test" class="font-bold text-red-500 text-sm">
                Test voucher
            </div>
            <div v-if="voucher.created_by_team">
                <div>Created by: {{ voucher.created_by_team.name }}</div>
            </div>
            <div v-if="voucher.allocated_to_service_team_id">
                <div>Allocated to: {{ voucher.allocated_to_service_team.name }}</div>
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
                Last redeemed at: {{ dayjs.utc(voucher.last_redemption_at).fromNow() }} ({{ dayjs(voucher.last_redemption_at) }})
            </div>
        </div>

        <div class="pb-32">

        </div>
    </AuthenticatedLayout>
</template>
