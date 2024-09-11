<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head} from '@inertiajs/vue3';
import AdminTopNavigation from "@/Components/Admin/AdminTopNavigation.vue";
import AdminTeamCreateComponent from "@/Components/Admin/Teams/AdminTeamCreateComponent.vue";
import AdminTeamVoucherTemplateEditComponent from "@/Components/Admin/Teams/AdminTeamVoucherTemplateEditComponent.vue";


</script>

<template>
    <Head title="Create Team"/>

    <AuthenticatedLayout>
        <template #header>
            <AdminTopNavigation></AdminTopNavigation>
        </template>

        <AdminTeamVoucherTemplateEditComponent></AdminTeamVoucherTemplateEditComponent>

    </AuthenticatedLayout>
</template>
