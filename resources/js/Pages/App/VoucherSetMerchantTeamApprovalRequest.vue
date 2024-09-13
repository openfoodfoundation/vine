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
    let textBit = 'Approve'

    if ($props.status === 'rejected') {
        textBit = 'Reject'
    }

    Swal.fire({
        title: "Are you sure?",
        text: "This action cannot be undone. Please confirm if you wish to proceed.",
        icon: "warning",
        confirmButtonColor: "#3085d6",
        confirmButtonText: textBit + " voucher set",
        showCancelButton: true,
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

    <GuestLayout>

    </GuestLayout>
</template>
