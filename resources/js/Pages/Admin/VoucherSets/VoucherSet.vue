<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head, Link} from '@inertiajs/vue3';
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
import Swal from "sweetalert2";
import AdminTeamMerchantTeamSelectComponent
    from "@/Components/Admin/TeamMerchantTeams/AdminTeamMerchantTeamSelectComponent.vue";
import AdminSearchComponent from "@/Components/Admin/Search/AdminSearchComponent.vue";

dayjs.extend(relativeTime);
dayjs.extend(utc);

const $props = defineProps({
    id: {
        required: true,
    }
})

const addingNewMerchant = ref(false)
const voucherSet = ref({})

onMounted(() => {
    getVoucherSet()
});

function addNewMerchant() {
    addingNewMerchant.value = true
}

function cancelAddingNewMerchant() {
    addingNewMerchant.value = false
}

function merchantTeamSelected(merchantTeam) {

    Swal.fire({
        icon: 'warning',
        title: 'Add ' + merchantTeam.name + ' as merchant for this voucher set?',
        html: '<div>Note that the selected team must be a merchant of the voucher set\'s service team.</div>',
        showCancelButton: true,
        confirmButtonText: 'Add please!'
    }).then(result => {
        if (result.isConfirmed) {

            let payload = {
                voucher_set_id: voucherSet.value.id,
                merchant_team_id: merchantTeam.id
            }

            axios.post('/admin/voucher-set-merchant-teams', payload).then(response => {
                getVoucherSet();
            }).catch(error => {
                Swal.fire({
                    icon: "error",
                    title: "Oops..",
                    text: error.response.data.meta.message
                })
            })
        }
    });

}

function getVoucherSet() {
    axios.get('/admin/voucher-sets/' + $props.id + '?cached=false&relations=createdByTeam,allocatedToServiceTeam,voucherSetMerchantTeams.merchantTeam,voucherSetMerchantTeamApprovalRequests.merchantUser,voucherSetMerchantTeamApprovalRequests.merchantTeam').then(response => {
        voucherSet.value = response.data.data
    }).catch(error => {
        console.log(error)
    })
}

function forceRemoveMerchant(merchantTeam) {

    Swal.fire({
        icon: 'warning',
        title: 'Force-remove Merchant?',
        html: '<div>Be careful here. This removes this merchant from involvement in this voucher set, and deletes all previous merchant approval requests, regardless of previous state.</div>' +
            '<div class="mt-4">Any emails sent to this merchant previously requesting involvement will not work, as the approval request will have been revoked. This merchant team will not be able to redeem vouchers for this set; they may continue to redeem vouchers for other sets they are involved in.</div>',
        showCancelButton: true,
        confirmButtonText: 'Remove please!'
    }).then(result => {
        if (result.isConfirmed) {
            axios.delete('/admin/voucher-set-merchant-teams/' + merchantTeam.id).then(response => {
                getVoucherSet();
            }).catch(error => {
                console.log(error)
            })
        }
    });
}

</script>

<template>
    <Head title="Voucher set"/>

    <AuthenticatedLayout>
        <template #header>
            <AdminTopNavigation></AdminTopNavigation>
        </template>

        <div class="card">
            <h2>
                {{ voucherSet.name }}
            </h2>
            <div class="text-xs">
                # {{ $props.id }}
            </div>
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
                        {{ voucherSet.num_vouchers_fully_redeemed }}
                    </div>
                    # Vouchers Fully Redeemed
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

                <div>
                    <div class="font-bold text-3xl">
                        {{ voucherSet.num_voucher_redemptions }}
                    </div>
                    # Redemptions
                </div>
                <div>
                    <div class="font-bold text-3xl">
                        {{ voucherSet.num_vouchers_partially_redeemed }}
                    </div>
                    # Vouchers Partially Redeemed
                </div>

                <div>
                    <div class="font-bold text-3xl">
                        {{ voucherSet.num_vouchers_unredeemed }}
                    </div>
                    # Vouchers Unredeemed
                </div>

            </div>
        </div>

        <div class="card">
            <div class="card-header">
                Created by team
            </div>

            <div v-if="voucherSet.created_by_team">
                <Link :href="route('admin.team', {id:voucherSet.created_by_team_id})">{{
                        voucherSet.created_by_team.name
                    }}
                </Link>
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
                <Link :href="route('admin.team', {id:voucherSet.allocated_to_service_team_id})">
                    {{ voucherSet.allocated_to_service_team.name }}
                </Link>
            </div>
        </div>


        <div class="card">
            <div class="card-header flex justify-between items-center">
                <div>
                    Merchant teams
                </div>

                <div class="flex justify-end">
                    <div v-if="!addingNewMerchant">
                        <PrimaryButton @click="addNewMerchant()" class="ms-4">
                            Add Merchant Team
                        </PrimaryButton>
                    </div>
                    <div v-else>
                        <PrimaryButton @click="cancelAddingNewMerchant()" class="ms-4">
                            Cancel
                        </PrimaryButton>
                    </div>
                </div>
            </div>

            <div v-if="addingNewMerchant">
                <div class="py-2 text-xs">Select merchant team...</div>
                <AdminSearchComponent :filter-to-data-sets="'teams'"
                                      @itemWasSelected="merchantTeamSelected"></AdminSearchComponent>
            </div>

            <div v-if="voucherSet.voucher_set_merchant_teams">
                <div v-for="merchantTeam in voucherSet.voucher_set_merchant_teams">
                    <div class="border-b py-2 flex justify-between items-center">
                        <Link :href="route('admin.team', {id:merchantTeam.merchant_team_id})">
                            {{ (merchantTeam.merchant_team?.name) ?? 'Merchant team' }}
                        </Link>

                        <div class="flex justify-end gap-4 items-center">
                            <div v-if="merchantTeam.voucher_set_merchant_team_approval_request_id">
                                <div class="text-xs">

                                    Approved.


                                </div>
                            </div>

                            <DangerButton @click="forceRemoveMerchant(merchantTeam)">
                                Force Remove?
                            </DangerButton>
                        </div>
                    </div>

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
                We only need one user from any merchant team member (per team) to approve that team's participation as
                merchants.
            </div>

            <div v-if="voucherSet.voucher_set_merchant_team_approval_requests?.length">
                <div v-for="approvalRequest in voucherSet.voucher_set_merchant_team_approval_requests">

                    <div class="flex justify-between items-center py-4">

                        <div>
                            <div>
                                <Link class="font-bold" :href="'/admin/team/' +approvalRequest.merchant_team_id ">
                                    {{ approvalRequest.merchant_team?.name }}
                                </Link>
                                |
                                <Link :href="'/admin/user/' + approvalRequest.merchant_user_id">
                                    {{ approvalRequest.merchant_user?.name }}
                                </Link>
                            </div>
                            <div class="text-xs">
                                {{ approvalRequest.merchant_user?.email }}
                            </div>
                        </div>
                        <div>

                            <div class="flex items-center gap-x-2">
                                <div class="text-xs text-right">
                                    <div>
                                        Created: {{ dayjs.utc(approvalRequest.created_at).fromNow() }}
                                    </div>
                                    <div v-if="approvalRequest.approval_status_last_updated_at">
                                        Last actioned:
                                        {{ dayjs.utc(approvalRequest.approval_status_last_updated_at).fromNow() }}
                                    </div>
                                </div>
                                <div class="flex justify-end">

                                    <SecondaryButton class="opacity-40"
                                                     v-if="approvalRequest.approval_status === 'ready'">
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
