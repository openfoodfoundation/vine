<script setup>
import {Head, usePage} from '@inertiajs/vue3';
import GuestLayout from "@/Layouts/GuestLayout.vue";
import {onMounted, ref} from "vue";
import Swal from "sweetalert2";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";

const $props = defineProps({
    approvalRequestId: {
        type: Number,
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

function checkUserIsLoggedIntoTheCorrectTeam()
{
    if(usePage().props.auth.user.current_team_id === voucherSetMerchantTeamApprovalRequest.value.merchant_team_id)
    {
        userIsLoggedIntoCorrectTeam.value = true;
    }
    console.log(usePage().props.auth.user.current_team_id);
    console.log(voucherSetMerchantTeamApprovalRequest.value.merchant_team_id);
    console.log((usePage().props.auth.user.current_team_id === voucherSetMerchantTeamApprovalRequest.value.merchant_team_id))
}

function getVoucherSetMerchantTeamApprovalRequest() {
    axios.get('/my-team-vsmtar/' + $props.approvalRequestId + '?cached=false&relations=voucherSet').then(response => {


        checkUserIsLoggedIntoTheCorrectTeam();

        voucherSetMerchantTeamApprovalRequest.value = response.data.data;
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

        <div class="card" v-if="userIsLoggedIntoCorrectTeam">
            This request is related to a different merchant team. Please log into team "[INTENDED VOucher Set Merchant team name]".
        </div>
        <div class="card" v-else>
            <div class="card-header">
                Voucher Set: [XXXX]
            </div>
            <div>
                <div>
                    <div class="font-bold">
                        Voucher set details
                    </div>
                    ID:
                    created by team:
                    Service team name:
                    Voucher set total value: $xyz
                </div>
                <div>
                    You have been requested to approve your team's ([TEAMNAME]]) involvement with this voucher set, which will be redeeming vouchers within the Vine platform. Approving this request means that your organisation will apply discounts as per voucher redemptions made at your shop or premises using the Vine system.

                    Vouchers up to the value of the voucher set above may be redeemed at your premises or store.

                    Please select your choice below.
                </div>
                <div class="grid grid-cols-3 gap-4 text-center font-bold text-xl m-20">
                    <div @click="approved = false" class="py-12 border-2 rounded-xl cursor-pointer" :class="{'bg-red-500 border-red-500 text-white': !approved}">Reject</div>
                    <div></div>
                    <div @click="approved = true" class="py-12 border-2 rounded-xl cursor-pointer" :class="{'bg-green-500 border-green-500 text-white': approved}">Approve</div>
                </div>
                <div class="grid grid-cols-3 gap-4 text-center font-bold text-xl m-20">
                    <div></div>
                    <div @click="save()" class="py-8 border-2 rounded-xl bg-black text-white cursor-pointer">
                        <div>Selected:
                            <span v-if="approved">APPROVED</span>
                            <span v-else>REJECTED</span>
                        </div>
                        <div class="mt-2">Save</div>
                    </div>
                    <div></div>
                </div>

                <PrimaryButton>Primary</PrimaryButton>
                <SecondaryButton>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 mr-2 text-red-500">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                    </svg>

                    Secondary</SecondaryButton>
            </div>

        </div>
    </AuthenticatedLayout>
</template>
