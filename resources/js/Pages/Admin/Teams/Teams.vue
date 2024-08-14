<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import AdminTopNavigation from "@/Components/Admin/AdminTopNavigation.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
</script>

<template>
    <Head title="Teams" />

    <AuthenticatedLayout>
        <template #header>
            <AdminTopNavigation></AdminTopNavigation>
        </template>

        <div class="card">
            <PrimaryButton>
                <Link :href="route('admin.teams.new')">
                    Create New Team
                </Link>
            </PrimaryButton>
        </div>

        <div class=" card">
            // Teams list
        </div>
    </AuthenticatedLayout>
</template>
