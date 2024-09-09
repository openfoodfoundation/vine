<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head} from '@inertiajs/vue3';
import AdminTopNavigation from "@/Components/Admin/AdminTopNavigation.vue";
import VouchersComponent from "@/Components/Admin/Vouchers/VouchersComponent.vue";

</script>

<template>
    <Head title="Vouchers"/>

    <AuthenticatedLayout>
        <template #header>
            <AdminTopNavigation></AdminTopNavigation>
        </template>

        <div class=" card">
            <VouchersComponent></VouchersComponent>
        </div>
    </AuthenticatedLayout>
</template>
