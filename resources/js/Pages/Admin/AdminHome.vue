<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import AdminTopNavigation from "@/Components/Admin/AdminTopNavigation.vue";
import SystemStatisticsComponent from "@/Components/Admin/SystemStatistics/SystemStatisticsComponent.vue";
import AuditItemsComponent from "@/Components/AuditItemsComponent.vue";
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>

            <AdminTopNavigation></AdminTopNavigation>

        </template>

        <system-statistics-component></system-statistics-component>
        <AuditItemsComponent :is-admin="true"></AuditItemsComponent>
    </AuthenticatedLayout>
</template>
