<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head} from '@inertiajs/vue3';
import MyTeamTopNavigation from "@/Components/App/Search/SearchComponent.vue";
</script>

<template>
    <Head title="Dashboard"/>

    <AuthenticatedLayout>
        <template #header>
            Dashboard
        </template>

        <div class="card">

            You're logged in!

        </div>
    </AuthenticatedLayout>
</template>
