<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head} from '@inertiajs/vue3';
import AdminTopNavigation from "@/Components/Admin/AdminTopNavigation.vue";
import VoucherSetsComponent from "@/Components/Admin/VoucherSets/VoucherSetsComponent.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";

</script>

<template>
    <Head title="Voucher sets"/>

    <AuthenticatedLayout>
        <template #header>
            <AdminTopNavigation></AdminTopNavigation>
        </template>

        <div class="card">
            <div class="card-header flex justify-between items-center">
                <div>
                    Voucher Sets
                </div>

                <div>
                    <a :href="route('admin.voucher-sets.new')">
                        <PrimaryButton>
                            Create voucher set
                        </PrimaryButton>
                    </a>
                </div>
            </div>


            <VoucherSetsComponent></VoucherSetsComponent>
        </div>
    </AuthenticatedLayout>
</template>
