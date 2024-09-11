<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head} from '@inertiajs/vue3';
import AdminTopNavigation from "@/Components/Admin/AdminTopNavigation.vue";
import AdminTeamCreateComponent from "@/Components/Admin/Teams/AdminTeamCreateComponent.vue";
import AdminTeamVoucherTemplateEditComponent from "@/Components/Admin/Teams/AdminTeamVoucherTemplateEditComponent.vue";

const $props = defineProps({
    id: {
        type: Number,
        required: true
    }
});
</script>

<template>
    <Head title="Edit Voucher Template"/>

    <AuthenticatedLayout>
        <template #header>
            <AdminTopNavigation></AdminTopNavigation>
        </template>

        <AdminTeamVoucherTemplateEditComponent :id="$props.id"></AdminTeamVoucherTemplateEditComponent>

    </AuthenticatedLayout>
</template>
