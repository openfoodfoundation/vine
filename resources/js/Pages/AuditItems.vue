<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head, usePage} from '@inertiajs/vue3';
import AuditItemsComponent from "@/Components/AuditItemsComponent.vue";
import Swal from "sweetalert2";
import {onMounted, ref} from "vue";


</script>

<template>
    <Head title="Audit Trail"/>

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-normal text-xl text-gray-800 leading-tight">Audit Trail</h2>
        </template>


        <AuditItemsComponent/>


    </AuthenticatedLayout>
</template>
