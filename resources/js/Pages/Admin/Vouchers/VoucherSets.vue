<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head} from '@inertiajs/vue3';
import AdminTopNavigation from "@/Components/Admin/AdminTopNavigation.vue";
import VoucherSetsComponent from "@/Components/Admin/Vouchers/VoucherSetsComponent.vue";

</script>

<template>
    <Head title="Voucher sets"/>

    <AuthenticatedLayout>
        <template #header>
            <AdminTopNavigation></AdminTopNavigation>
        </template>

        <div class=" card">
            <VoucherSetsComponent></VoucherSetsComponent>
        </div>
    </AuthenticatedLayout>
</template>
