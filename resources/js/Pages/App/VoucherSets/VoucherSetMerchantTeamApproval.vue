<script setup>
import {Head} from '@inertiajs/vue3';
import GuestLayout from "@/Layouts/GuestLayout.vue";
import {onMounted, ref} from "vue";
import Swal from "sweetalert2";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";

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

onMounted(() => {
    getVoucherSetMerchantTeamApprovalRequest()

    approved.value = !!$props.approve;
})

function getVoucherSetMerchantTeamApprovalRequest() {
    axios.get('/vsmtar/' + $props.approvalRequestId).then(response => {
        voucherSetMerchantTeamApprovalRequest.value = response.data.data
    }).catch(error => {
        console.log(error)
    })
}

function save() {
    Swal.fire({
        title: approved.value ? "Are you sure approving?" : "Are you sure rejecting?",
        text: "This action cannot be undone. Please confirm if you wish to proceed.",
        icon: "warning",
        confirmButtonColor: "#3085d6",
        confirmButtonText: approved.value ? "Approve voucher set" : "Reject voucher set",
        allowOutsideClick: false,
        showCancelButton: true,
    }).then((result) => {
        if (result.isConfirmed) {
            let payload = {
                approval_status: approved.value ? 'approved' : 'rejected'
            }
            axios.put('/vsmtar/' + $props.approvalRequestId, payload).then(response => {
                Swal.fire({
                    title: approved.value ? "Voucher set approved!" : "Voucher set rejected!",
                    text: "Thank you for processing.",
                    icon: "success",
                    confirmButtonColor: "#3085d6",
                    allowOutsideClick: false,
                    confirmButtonText: "Go to dashboard",
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

        <div class="card">
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
        </div>
    </AuthenticatedLayout>
</template>
