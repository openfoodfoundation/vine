<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import AdminTopNavigation from "@/Components/Admin/AdminTopNavigation.vue";

</script>

<template>
    <Head title="Create Team" />

    <AuthenticatedLayout>
        <template #header>
            <AdminTopNavigation></AdminTopNavigation>
        </template>

        <div class="card">
            Create team component
        </div>

    </AuthenticatedLayout>
</template>
