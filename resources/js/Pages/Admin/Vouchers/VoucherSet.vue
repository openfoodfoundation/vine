<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import AdminTopNavigation from "@/Components/Admin/AdminTopNavigation.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import {onMounted, ref} from "vue";
import PaginatorComponent from "@/Components/Admin/PaginatorComponent.vue";
import dayjs from "dayjs";
import relativeTime from "dayjs/plugin/relativeTime";
import utc from "dayjs/plugin/utc"
import VouchersComponent from "@/Components/Admin/Vouchers/VouchersComponent.vue";

dayjs.extend(relativeTime);
dayjs.extend(utc);

const $props = defineProps({
    id: {
        required: true,
    }
})

const voucherSet = ref({})

onMounted(() => {
    getVoucherSet()
})

function getVoucherSet() {
    axios.get('/admin/voucher-sets/' + $props.id + '?cached=false&relations=createdByTeam,allocatedToServiceTeam').then(response => {
        voucherSet.value = response.data.data
    }).catch(error => {
        console.log(error)
    })
}

</script>

<template>
    <Head title="Voucher set" />

    <AuthenticatedLayout>
        <template #header>
            <AdminTopNavigation></AdminTopNavigation>
        </template>

        <div class="card">
            <h2>
                {{ $props.id }}
            </h2>
            <div v-if="voucherSet.is_test" class="font-bold text-red-500 text-sm">
                Test voucher set
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                Created by team
            </div>

            <div v-if="voucherSet.created_by_team">
                <Link :href="route('admin.team', {id:voucherSet.created_by_team_id})">{{ voucherSet.created_by_team.name }}</Link>
            </div>
            <div v-if="voucherSet.created_at" class="text-xs mt-2">
                Created at: {{ dayjs.utc(voucherSet.created_at).fromNow() }} ({{ dayjs(voucherSet.created_at) }})
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                Allocated to team
            </div>

            <div v-if="voucherSet.allocated_to_service_team_id">
                <Link :href="route('admin.team', {id:voucherSet.allocated_to_service_team_id})">{{ voucherSet.allocated_to_service_team.name }}</Link>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                Voucher set details
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
            <div v-if="voucherSet.last_redemption_at">
                Last redeemed at: {{ dayjs.utc(voucherSet.last_redemption_at).fromNow() }} ({{ dayjs(voucherSet.last_redemption_at) }})
            </div>
            <div v-if="voucherSet.expires_at">
                Expires at: {{ dayjs.utc(voucherSet.expires_at).fromNow() }} ({{ dayjs(voucherSet.expires_at) }})
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                Vouchers
            </div>

            <VouchersComponent :voucher-set-id="$props.id"></VouchersComponent>
        </div>

        <div class="pb-32"></div>
    </AuthenticatedLayout>
</template>
