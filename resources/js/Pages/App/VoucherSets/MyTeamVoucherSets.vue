<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head} from '@inertiajs/vue3';
import {onMounted, ref} from "vue";
import MyTeamTopNavigation from "@/Components/App/Search/SearchComponent.vue";
import MyTeamVouchersComponent from "@/Components/App/MyTeamVouchersComponent.vue";
import MyTeamVoucherSetsComponent from "@/Components/App/MyTeamVoucherSetsComponent.vue";
import SearchComponent from "@/Components/App/Search/SearchComponent.vue";


onMounted(() => {

})


</script>

<template>
    <Head title="Dashboard"/>

    <AuthenticatedLayout>
        <template #header>
            My Team Voucher Sets
        </template>

        <div class="card">
            <div class="card-header">
                Voucher sets Created by my team
            </div>

            <MyTeamVoucherSetsComponent filter-voucher-sets="-created"></MyTeamVoucherSetsComponent>
        </div>

        <div class="card">
            <div class="card-header">
                Voucher sets allocated to my team
            </div>

            <MyTeamVoucherSetsComponent filter-voucher-sets="-allocated"></MyTeamVoucherSetsComponent>
        </div>

        <div class="pb-32">

        </div>

    </AuthenticatedLayout>
</template>
