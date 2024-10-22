<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head} from '@inertiajs/vue3';
import MyTeamVoucherSetMerchantApprovalsList
    from "@/Components/App/MyTeamVoucherSetMerchantApprovals/MyTeamVoucherSetMerchantApprovalsList.vue";
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

        <div class="my-8">
            <MyTeamVoucherSetMerchantApprovalsList></MyTeamVoucherSetMerchantApprovalsList>
        </div>

    </AuthenticatedLayout>
</template>
