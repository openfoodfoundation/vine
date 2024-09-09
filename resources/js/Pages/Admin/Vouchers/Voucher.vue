<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import AdminTopNavigation from "@/Components/Admin/AdminTopNavigation.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import {onMounted, ref} from "vue";
import PaginatorComponent from "@/Components/Admin/PaginatorComponent.vue";
import AdminUserDetailsComponent from "@/Components/Admin/AdminUserDetailsComponent.vue";

const $props = defineProps({
    id: {
        required: true,
    }
})


const voucher = ref({})

onMounted(() => {
    getVoucher()
})

function getVoucher() {
    axios.get('/admin/vouchers/' + $props.id + '?cached=false&relations=createdByTeam,allocatedToServiceTeam').then(response => {
        voucher.value = response.data.data
    }).catch(error => {
        console.log(error)
    })
}

</script>

<template>
    <Head title="Voucher set" />

    <AuthenticatedLayout>
        <template #header>
            <AdminTopNavigation></AdminTopNavigation>
        </template>

        <div class="card">
            <div class="flex justify-between items-center">
                <h2>Voucher #{{ $props.id }}</h2>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                Voucher set
            </div>

            <div v-if="voucher.voucher_set_id">
                <Link :href="route('admin.voucher-set', {id:voucher.voucher_set_id})">{{ voucher.voucher_set_id }}</Link>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                Voucher details
            </div>

            <div v-if="voucher.voucher_short_code">
                Short code: {{ voucher.voucher_short_code }}
            </div>

        </div>
    </AuthenticatedLayout>
</template>
