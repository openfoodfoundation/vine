<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import AdminTopNavigation from "@/Components/Admin/AdminTopNavigation.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";

const $props = defineProps({
    id: {
        required: true,
        type: Number,
    }
});


</script>

<template>
    <Head title="Team" />

    <AuthenticatedLayout>
        <template #header>
            <AdminTopNavigation></AdminTopNavigation>
        </template>



        <div class="card">
            Team details & aggregates
        </div>

        <div class="card">
            Team members (linking) - paginated
        </div>

        <div class="card">
            Add user to team
        </div>

        <div class="card">
            Voucher sets created by team  - paginated
        </div>

        <div class="card">
            Voucher sets allocated to team  - paginated
        </div>

    </AuthenticatedLayout>
</template>
