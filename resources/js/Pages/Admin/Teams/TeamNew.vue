<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head} from '@inertiajs/vue3';
import AdminTopNavigation from "@/Components/Admin/AdminTopNavigation.vue";
import AdminTeamCreateComponent from "@/Components/Admin/AdminTeamCreateComponent.vue";

function teamCreated(team) {
    window.location.href = route('admin.team', team.id)
}

</script>

<template>
    <Head title="Create Team"/>

    <AuthenticatedLayout>
        <template #header>
            <AdminTopNavigation></AdminTopNavigation>
        </template>

        <div class="card">
            <AdminTeamCreateComponent @teamCreated="teamCreated" />
        </div>

    </AuthenticatedLayout>
</template>
