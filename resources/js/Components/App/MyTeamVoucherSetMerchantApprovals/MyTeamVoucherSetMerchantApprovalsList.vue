<script setup>

import {onMounted, ref} from "vue";
import PaginatorComponent from "@/Components/Admin/PaginatorComponent.vue";
import {Link} from "@inertiajs/vue3";
import PrimaryButton from "@/Components/PrimaryButton.vue";


const myMerchantVoucherSetApprovals = ref({})


onMounted(() => {
    getData()
});


function getData(page = 1) {
    axios.get('/my-team-vsmtar?cached=false&relations=voucherSet.allocatedToServiceTeam,voucherSet.voucherSetMerchantTeamApprovalActionedRecord.merchantUser&orderBy=id,desc&page=' + page).then(response => {
        myMerchantVoucherSetApprovals.value = response.data.data

    }).catch(error => {
        console.log(error)
    })
}


</script>

<template>
    <div v-if="myMerchantVoucherSetApprovals.data?.length" class="card">
        <div class="card-header">
            My Merchant Team Voucher Set Approvals
        </div>

        <div class="card-body">

            <div v-for="approvalRequest in myMerchantVoucherSetApprovals.data">
                <Link class="hover:no-underline hover:bg-gray-50" :href="'/my-voucher-set-merchant-team-approval-request/' + approvalRequest.id">
                    <div class="border-b py-2 flex justify-between items-center">
                        <div>

                            <div class="capitalize">
                                {{approvalRequest.voucher_set?.voucher_set_type}}

                            </div>
                            <div class="text-xs text-gray-500">
                                <div>
                                    Voucher Set #{{approvalRequest.voucher_set_id}}
                                </div>
                                #{{approvalRequest.id}}
                            </div>
                        </div>

                        <div class="flex justify-end items-center">

                            <div class="pr-2" v-if="!approvalRequest.voucher_set?.voucher_set_merchant_team_approval_actioned_record">
                                <div v-if="approvalRequest.approval_status === 'ready'">
                                    <PrimaryButton>
                                        Please action!
                                    </PrimaryButton>
                                </div>
                                <div class="text-lg text-green-200 font-bold" v-else-if="approvalRequest.approval_status === 'approved'">
                                    Approved
                                </div>
                                <div class="text-lg text-red-200 font-bold" v-else-if="approvalRequest.approval_status === 'approved'">
                                    Rejected
                                </div>
                            </div>
                            <div v-else class="text-xs pr-4">
                                Voucher set
                                <span class="capitalize">
                                    {{approvalRequest.voucher_set.voucher_set_merchant_team_approval_actioned_record.approval_status}}
                                </span>
                                by
                                <span>
                                     {{approvalRequest.voucher_set.voucher_set_merchant_team_approval_actioned_record?.merchant_user?.name}}
                                </span>
                            </div>

                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                            </svg>

                        </div>
                    </div>
                </Link>
            </div>

            <div class="flex justify-end items-center mt-4">
                <div class="w-full lg:w-1/3">
                    <PaginatorComponent
                        @setDataPage="getData"
                        :pagination-data="myMerchantVoucherSetApprovals"></PaginatorComponent>
                </div>
            </div>
        </div>
    </div>
</template>
