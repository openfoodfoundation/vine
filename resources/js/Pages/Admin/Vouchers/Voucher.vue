<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import AdminTopNavigation from "@/Components/Admin/AdminTopNavigation.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import {onMounted, ref} from "vue";
import PaginatorComponent from "@/Components/Admin/PaginatorComponent.vue";

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

        <div class=" card">
            Voucher #{{ $props.id }}
<!--            <div v-if="voucherSets.data && voucherSets.data.length">-->
<!--                <Link :href="route('admin.voucher-set', voucherSet.id)" v-for="voucherSet in voucherSets.data" class="hover:no-underline hover:opacity-75">-->
<!--                    <div class="border-b flex justify-between items-center py-2 sm:p-2">-->
<!--                        <div>-->

<!--                            <div class="font-bold">-->
<!--                                 <span class="text-xs opacity-25">-->
<!--                                  #{{ team.id }}-->
<!--                                </span>-->
<!--                                {{ team.name }}-->
<!--                            </div>-->
<!--                        </div>-->
<!--                        <div >-->
<!--                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">-->
<!--                                <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />-->
<!--                            </svg>-->
<!--                        </div>-->
<!--                    </div>-->
<!--                </Link>-->
<!--            </div>-->

<!--            <div class="flex justify-end items-center mt-4">-->
<!--                <div class="w-full lg:w-1/3">-->
<!--                    <PaginatorComponent-->
<!--                        @setDataPage="setDataPage"-->
<!--                        :pagination-data="teams"></PaginatorComponent>-->
<!--                </div>-->
<!--            </div>-->
        </div>
    </AuthenticatedLayout>
</template>
