<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head, Link} from '@inertiajs/vue3';
import AdminTopNavigation from "@/Components/Admin/AdminTopNavigation.vue";
import {onMounted, ref} from "vue";
import PaginatorComponent from "@/Components/Admin/PaginatorComponent.vue";
import dayjs from "dayjs";
import relativeTime from "dayjs/plugin/relativeTime";
import utc from "dayjs/plugin/utc"

dayjs.extend(relativeTime);
dayjs.extend(utc);

const $props = defineProps({
    id: {
        required: true,
    }
})

const voucher = ref({})

onMounted(() => {
    getVoucher()
})

function getVoucher() {
    axios.get('/admin/vouchers/' + $props.id + '?cached=false&relations=createdByTeam,allocatedToServiceTeam,voucherRedemptions.redeemedByUser,voucherRedemptions.redeemedByTeam').then(response => {
        voucher.value = response.data.data
    }).catch(error => {
        console.log(error)
    })
}

</script>

<template>
    <Head title="Voucher set"/>

    <AuthenticatedLayout>
        <template #header>
            <AdminTopNavigation></AdminTopNavigation>
        </template>

        <div class="card">
            <div class="opacity-25 text-sm">{{ $props.id }}</div>
            <h2 v-if="voucher.voucher_short_code">
                {{ voucher.voucher_short_code }}
            </h2>
            <div v-if="voucher.is_test" class="font-bold text-red-500 text-sm">
                Test voucher
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                Voucher set
            </div>

            <div v-if="voucher.voucher_set_id">
                <Link :href="route('admin.voucher-set', {id:voucher.voucher_set_id})">{{ voucher.voucher_set_id }}</Link>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                Created by team
            </div>

            <div v-if="voucher.created_by_team">
                <Link :href="route('admin.team', {id:voucher.created_by_team_id})">{{ voucher.created_by_team.name }}</Link>
            </div>
            <div v-if="voucher.created_at" class="text-xs mt-2">
                Created at: {{ dayjs.utc(voucher.created_at).fromNow() }} ({{ dayjs(voucher.created_at) }})
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                Allocated to team
            </div>

            <div v-if="voucher.allocated_to_service_team_id">
                <Link :href="route('admin.team', {id:voucher.allocated_to_service_team_id})">{{ voucher.allocated_to_service_team.name }}</Link>
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
                Last redeemed at: {{ dayjs.utc(voucher.last_redemption_at).fromNow() }} ({{ dayjs(voucher.last_redemption_at) }})
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
