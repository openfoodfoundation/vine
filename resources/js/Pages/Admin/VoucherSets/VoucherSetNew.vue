<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head} from '@inertiajs/vue3';
import AdminTopNavigation from "@/Components/Admin/AdminTopNavigation.vue";
import VoucherSetNewComponent from "@/Components/Admin/VoucherSets/VoucherSetNewComponent.vue";
</script>

<template>
    <Head title="Vouchers"/>

    <AuthenticatedLayout>
        <template #header>
            <AdminTopNavigation/>
        </template>

        <div class=" card">
            <div class="card-header">
                Voucher Set New
            </div>
            <VoucherSetNewComponent/>
        </div>
    </AuthenticatedLayout>
</template>
