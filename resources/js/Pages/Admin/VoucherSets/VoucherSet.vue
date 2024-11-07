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
import SecondaryButton from "@/Components/SecondaryButton.vue";
import DangerButton from "@/Components/DangerButton.vue";

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
    axios.get('/admin/voucher-sets/' + $props.id + '?cached=false&relations=createdByTeam,allocatedToServiceTeam,voucherSetMerchantTeams.merchantTeam,voucherSetMerchantTeamApprovalRequests.merchantUser,voucherSetMerchantTeamApprovalRequests.merchantTeam').then(response => {
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
                Voucher set details
                <span v-if="!voucherSet.voucher_generation_finished_at" class="text-red-500 text-xs font-bold">Vouchers not generated yet.</span>
            </div>

            <div class="grid grid-cols-4 gap-y-12 text-center mt-8">
                <div>
                    <div class="font-bold text-3xl">
                        ${{ voucherSet.total_set_value / 100 }}
                    </div>
                    Total set value
                </div>
                <div>
                    <div class="font-bold text-3xl">
                        ${{ voucherSet.total_set_value_remaining / 100 }}
                    </div>
                    Total remaining value
                </div>
                <div>
                    <div class="font-bold text-3xl">
                        {{ voucherSet.num_vouchers }}
                    </div>
                    # Vouchers
                </div>
                <div>
                    <div class="font-bold text-3xl">
                        {{ voucherSet.num_voucher_redemptions }}
                    </div>
                    # Redemptions
                </div>


                <div v-if="voucherSet.last_redemption_at">
                    <div>
                        Last redeemed
                    </div>
                    <div class="font-bold text-3xl">
                        {{ dayjs.utc(voucherSet.last_redemption_at).fromNow() }}
                    </div>
                    <div class="text-xs">
                        ({{ dayjs(voucherSet.last_redemption_at) }})
                    </div>

                </div>

                <div v-if="voucherSet.expires_at">
                    <div>
                        Expires
                    </div>
                    <div class="font-bold text-3xl">
                        {{ dayjs.utc(voucherSet.expires_at).fromNow() }}
                    </div>
                    <div class="text-xs">
                        ({{ dayjs(voucherSet.expires_at) }})
                    </div>

                </div>

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

            <div v-if="voucherSet.allocated_to_service_team">
                <Link :href="route('admin.team', {id:voucherSet.allocated_to_service_team_id})">{{ voucherSet.allocated_to_service_team.name }}</Link>
            </div>
        </div>


        <div class="card">
            <div class="card-header">
                Merchant teams
            </div>

            <div v-if="voucherSet.voucher_set_merchant_teams">
                <div v-for="merchantTeam in voucherSet.voucher_set_merchant_teams">
                    <Link :href="route('admin.team', {id:merchantTeam.merchant_team_id})">{{ (merchantTeam.merchant_team?.name)?? 'Merchant team' }}</Link>
                </div>

            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <div>
                    Merchant team approval requests
                </div>

            </div>

            <div class="text-xs">
                We only need one user from any merchant team member (per team) to approve that team's participation as merchants.
            </div>

            <div v-if="voucherSet.voucher_set_merchant_team_approval_requests?.length">
                <div v-for="approvalRequest in voucherSet.voucher_set_merchant_team_approval_requests">

                    <div class="flex justify-between items-center py-4">

                        <div>
                            <div>
                                <Link class="font-bold" :href="'/admin/team/' +approvalRequest.merchant_team_id ">
                                    {{approvalRequest.merchant_team?.name}}
                                </Link>
                                 |
                                <Link :href="'/admin/user/' + approvalRequest.merchant_user_id">
                                    {{ approvalRequest.merchant_user?.name }}
                                </Link>
                            </div>
                            <div class="text-xs">
                                {{approvalRequest.merchant_user?.email}}
                            </div>
                        </div>
                        <div class="flex items-center gap-x-2">

                            <div class="text-xs text-right">
                                <div>
                                    Created: {{ dayjs.utc(approvalRequest.created_at).fromNow() }}
                                </div>
                                <div v-if="approvalRequest.approval_status_last_updated_at">
                                    Last actioned: {{ dayjs.utc(approvalRequest.approval_status_last_updated_at).fromNow() }}
                                </div>
                            </div>
                            <div class="flex justify-end">
                                <SecondaryButton v-if="approvalRequest.approval_status === 'ready'">
                                    Ready
                                </SecondaryButton>

                                <PrimaryButton v-if="approvalRequest.approval_status === 'approved'">
                                    Approved
                                </PrimaryButton>

                                <DangerButton v-if="approvalRequest.approval_status === 'rejected'">
                                    Rejected
                                </DangerButton>
                            </div>

                        </div>
                    </div>

                </div>
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
