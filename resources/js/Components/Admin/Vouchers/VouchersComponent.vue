<script setup>
import {Link} from '@inertiajs/vue3';
import {onMounted, ref} from "vue";
import PaginatorComponent from "@/Components/Admin/PaginatorComponent.vue";
import dayjs from "dayjs";
import relativeTime from "dayjs/plugin/relativeTime";
import utc from "dayjs/plugin/utc"

dayjs.extend(relativeTime);
dayjs.extend(utc);

const $props = defineProps({
    teamId: {
        required: false,
        default: null
    },
    filterVouchers: {
        required: false,
        default: null
    },
    voucherSetId: {
        required: false,
        default: null
    }
})

const limit = ref(50)
const vouchers = ref({})

onMounted(() => {
    if ($props.teamId || $props.voucherSetId) {
        limit.value = 10
    }

    getVouchers()
})

function getVouchers(page = 1) {
    let urlBit = ''
    if ($props.teamId && $props.filterVouchers) {
        urlBit = '&where[]=' + $props.filterVouchers + ',' + $props.teamId
    } else if ($props.voucherSetId) {
        urlBit = '&where[]=voucher_set_id,' + $props.voucherSetId
    }

    axios.get('/admin/vouchers?cached=false&page=' + page + '&limit=' + limit.value + urlBit + '&orderBy=created_at,desc&relations=createdByTeam,allocatedToServiceTeam').then(response => {
        vouchers.value = response.data.data
    }).catch(error => {
        console.log(error)
    })
}

</script>

<template>
    <div v-if="vouchers.data && vouchers.data.length">

        <Link :href="route('admin.voucher', voucher.id)" v-for="voucher in vouchers.data" class="hover:no-underline hover:opacity-75">
            <div class="border-b flex justify-between items-center py-2 sm:p-2">
                <div class="text-xs">
                    <div v-if="voucher.voucher_short_code" class="font-bold text-sm">
                        #{{ voucher.voucher_short_code }}
                    </div>
                    <div v-else class="font-bold text-sm">
                        #{{ voucher.id }}
                    </div>
                    <div v-if="voucher.is_test" class="text-red-500">
                        Test voucher
                    </div>
                    <div v-if="voucher.created_by_team">
                        Created by: {{ voucher.created_by_team.name }}
                    </div>
                    <div v-if="voucher.allocated_to_service_team">
                        Allocated to: {{ voucher.allocated_to_service_team.name }}
                    </div>
                    <div>
                        Original value: ${{ voucher.voucher_value_original / 100 }}
                    </div>
                    <div>
                        Remaining value: ${{ voucher.voucher_value_remaining / 100 }}
                    </div>
                    <div v-if="voucher.created_at">
                        Created at: {{ dayjs.utc(voucher.created_at).fromNow() }} ({{ dayjs(voucher.created_at) }})
                    </div>
                </div>

                <div>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/>
                    </svg>
                </div>
            </div>
        </Link>

        <div class="flex justify-end items-center mt-4">
            <div class="w-full lg:w-1/3">
                <PaginatorComponent
                    @setDataPage="getVouchers"
                    :pagination-data="vouchers"></PaginatorComponent>
            </div>
        </div>
    </div>
</template>
