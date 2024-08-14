<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head, Link} from '@inertiajs/vue3';
import AdminTopNavigation from "@/Components/Admin/AdminTopNavigation.vue";
</script>

<template>
    <Head title="Users"/>

    <AuthenticatedLayout>
        <template #header>
            <AdminTopNavigation></AdminTopNavigation>
        </template>


        <div class=" card">
            // Users list component, ordered by id desc
        </div>
    </AuthenticatedLayout>
</template>
