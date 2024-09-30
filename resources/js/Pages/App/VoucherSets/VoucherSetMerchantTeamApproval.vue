<script setup>
import {Head, usePage} from '@inertiajs/vue3';
import {onMounted, ref} from "vue";
import Swal from "sweetalert2";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import dayjs from "dayjs";
import relativeTime from "dayjs/plugin/relativeTime";
import utc from "dayjs/plugin/utc"

dayjs.extend(relativeTime);
dayjs.extend(utc);

const $props = defineProps({
    approvalRequestId: {
        type: String,
        required: true,
    },
    approve: {
        type: Boolean,
        default: true,
    },
})

const voucherSetMerchantTeamApprovalRequest = ref({})
const approved = ref(true)
const userIsLoggedIntoCorrectTeam = ref(false)

onMounted(() => {


    getVoucherSetMerchantTeamApprovalRequest()

    approved.value = !!$props.approve;
})

function checkUserIsLoggedIntoTheCorrectTeam() {
    if (usePage().props.auth.user.current_team_id === voucherSetMerchantTeamApprovalRequest.value.merchant_team_id) {
        userIsLoggedIntoCorrectTeam.value = true;
    }
}

function getVoucherSetMerchantTeamApprovalRequest() {
    axios.get('/my-team-vsmtar/' + $props.approvalRequestId + '?cached=false&relations=voucherSet.createdByTeam,voucherSet.allocatedToServiceTeam,merchantTeam').then(response => {
        voucherSetMerchantTeamApprovalRequest.value = response.data.data;

        checkUserIsLoggedIntoTheCorrectTeam();
    }).catch(error => {
        console.log(error)
    })
}

function save() {
    let string = 'Approve'
    if (!approved.value) {
        string = 'Reject'
    }
    Swal.fire({
        title: approved.value ? "Are you sure approving?" : "Are you sure rejecting?",
        text: 'You are selecting "' + string + '" - any previous approvals or rejections will be overwritten. Sure?',
        icon: 'warning',
        confirmButtonColor: "#3085d6",
        confirmButtonText: approved.value ? 'Approve involvement' : 'Reject involvement',
        allowOutsideClick: false,
        showCancelButton: true,
    }).then((result) => {
        if (result.isConfirmed) {
            let payload = {
                approval_status: approved.value ? 'approved' : 'rejected'
            }
            axios.put('/my-team-vsmtar/' + $props.approvalRequestId, payload).then(response => {
                Swal.fire({
                    title: approved.value ? 'Approved!' : 'Rejected!',
                    text: 'Thank you for processing.',
                    icon: 'success',
                    confirmButtonColor: '#3085d6',
                    allowOutsideClick: false,
                    confirmButtonText: 'Go to dashboard',
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = route('dashboard')
                    }
                })
            }).catch(error => {
                console.log(error)
            })
        }
    });
}

</script>

<template>
    <Head title="Dashboard"/>

    <AuthenticatedLayout>
        <template #header>
            Voucher set approval
        </template>

        <div class="card" v-if="!userIsLoggedIntoCorrectTeam && voucherSetMerchantTeamApprovalRequest.merchant_team">
            This request is related to a different merchant team. Please log into team "{{ voucherSetMerchantTeamApprovalRequest.merchant_team.name }}".
        </div>
        <div class="card" v-else>
            <div class="card-header">
                Voucher Set
            </div>
            <div class="pt-4">
                <div>
                    <div class="font-bold">
                        Voucher set details
                    </div>
                    <div class="pt-4">
                        ID: {{ voucherSetMerchantTeamApprovalRequest.voucher_set_id }}
                    </div>
                    <div v-if="voucherSetMerchantTeamApprovalRequest.voucher_set && voucherSetMerchantTeamApprovalRequest.voucher_set.created_by_team">
                        Created by team: {{ voucherSetMerchantTeamApprovalRequest.voucher_set.created_by_team.name }}
                    </div>
                    <div v-if="voucherSetMerchantTeamApprovalRequest.voucher_set && voucherSetMerchantTeamApprovalRequest.voucher_set.allocated_to_service_team">
                        Service team name: {{ voucherSetMerchantTeamApprovalRequest.voucher_set.allocated_to_service_team.name }}
                    </div>
                    <div v-if="voucherSetMerchantTeamApprovalRequest.voucher_set" class="pb-4">
                        Voucher set total value: ${{ (voucherSetMerchantTeamApprovalRequest.voucher_set.total_set_value / 100).toFixed(2) }}
                    </div>
                </div>
                <div class="py-8" v-if="voucherSetMerchantTeamApprovalRequest.merchant_team">
                    You have been requested to approve your team's ({{ voucherSetMerchantTeamApprovalRequest.merchant_team.name }}) involvement with this voucher set, which will be redeeming vouchers within the Vine platform. Approving this
                    request means that your organisation will apply
                    discounts
                    as per voucher redemptions made at your shop or premises using the Vine system.

                    Vouchers up to the value of the voucher set above may be redeemed at your premises or store.

                    Please select your choice below.
                </div>

                <div v-if="voucherSetMerchantTeamApprovalRequest.approval_status !== 'ready'" class="py-4">
                    <div>
                        You have already {{ voucherSetMerchantTeamApprovalRequest.approval_status }} {{ dayjs.utc(voucherSetMerchantTeamApprovalRequest.approval_status_last_updated_at).fromNow() }} so no further action is needed.
                    </div>
                    <div class="text-xs capitalize">
                        {{ voucherSetMerchantTeamApprovalRequest.approval_status }} at ({{ dayjs(voucherSetMerchantTeamApprovalRequest.approval_status_last_updated_at) }})
                    </div>
                </div>


                <div>
                    <SecondaryButton @click="approved = false" class="mr-2" :class="{'opacity-50': approved}">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 mr-2 text-red-500">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 0 0 5.636 5.636m12.728 12.728A9 9 0 0 1 5.636 5.636m12.728 12.728L5.636 5.636"/>
                        </svg>
                        Reject
                    </SecondaryButton>
                    <SecondaryButton @click="approved = true" class="ml-2" :class="{'opacity-50': !approved}">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 mr-2 text-green-500">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                        </svg>
                        Approve
                    </SecondaryButton>
                </div>

                <div>
                    <PrimaryButton @click="save()" class="mt-4">
                        Selected:
                        <span v-if="approved" class="px-2 text-green-500">APPROVED</span>
                        <span v-else class="px-2 text-red-500">REJECTED</span>
                        > Click Here To Save
                    </PrimaryButton>
                </div>
            </div>

        </div>
    </AuthenticatedLayout>
</template>
