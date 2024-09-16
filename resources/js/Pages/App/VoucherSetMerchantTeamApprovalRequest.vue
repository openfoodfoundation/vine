<script setup>
import {Head} from '@inertiajs/vue3';
import GuestLayout from "@/Layouts/GuestLayout.vue";
import {onMounted} from "vue";
import Swal from "sweetalert2";

const $props = defineProps({
    id: {
        type: Number,
        required: true,
    },
    status: {
        type: String,
        required: true,
    },
})

onMounted(() => {
    showMessage()
})

function showMessage() {

    Swal.fire({
        title: $props.status === 'approved' ? "Are you sure approving?" : "Are you sure rejecting?",
        text: "This action cannot be undone. Please confirm if you wish to proceed.",
        icon: "warning",
        confirmButtonColor: "#3085d6",
        confirmButtonText: $props.status === 'approved' ? "Approve voucher set" : "Reject voucher set",
        showDenyButton: true,
        denyButtonText: `Cancel`,
        allowOutsideClick: false,
        showCancelButton: false,
    }).then((result) => {
        if (result.isConfirmed) {
            let payload = {
                approval_status: $props.status
            }
            axios.put('/voucher-set-approval/' + $props.id, payload).then(response => {
                Swal.fire({
                    title: $props.status === 'approved' ? "Voucher set approved!" : "Voucher set rejected!",
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
        } else if (result.isDenied) {
            Swal.fire({
                title: $props.status === 'approved' ? "Voucher set has not been approved" : "Voucher set has not been rejected",
                text: "Please go back to the email to approve or reject the voucher set.",
                icon: "warning",
                confirmButtonColor: "#3085d6",
                allowOutsideClick: false,
                confirmButtonText: "Go to dashboard",
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = route('dashboard')
                }
            })
        }
    });
}


</script>

<template>
    <Head title="Dashboard"/>

    <GuestLayout>

    </GuestLayout>
</template>
