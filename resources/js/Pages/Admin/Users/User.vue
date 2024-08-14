<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head, Link} from '@inertiajs/vue3';
import AdminTopNavigation from "@/Components/Admin/AdminTopNavigation.vue";

const $props = defineProps({
    id: {
        required: true,
        type: Number,
    }
});

</script>

<template>
    <Head title="Users"/>

    <AuthenticatedLayout>
        <template #header>
            <AdminTopNavigation></AdminTopNavigation>
        </template>


        <div class=" card">
            // User #{{ $props.id }} component


            <div class="card">
                User details
                - (current team)
                -
            </div>


            <div class="card">
                User teams
                - (cShow all teams) - link to team in admin panel
                -if current, show as currenytly logg into
            </div>

            <div class="card">
                User PAT list
                - PAT capabilities
                - Team
            </div>

            <div class="card">
                Create PAT (no edit)
                - name
                - seletc abilities (ENUM - in scope doc for PAT Abilities
            </div>



        </div>
    </AuthenticatedLayout>
</template>
