<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import AdminTopNavigation from "@/Components/Admin/AdminTopNavigation.vue";
import VoucherRedemptionsComponent from "@/Components/Admin/Vouchers/VoucherRedemptionsComponent.vue";

</script>

<template>
    <Head title="Voucher redemptions" />

    <AuthenticatedLayout>
        <template #header>
            <AdminTopNavigation></AdminTopNavigation>
        </template>

        <div class="card">
            <div class="card-header">
                Voucher Redemptions
            </div>
            <VoucherRedemptionsComponent></VoucherRedemptionsComponent>
        </div>
    </AuthenticatedLayout>
</template>
