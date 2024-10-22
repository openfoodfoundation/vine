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

    axios.get('/admin/vouchers?cached=false&page=' + page + '&limit=' + limit.value + urlBit + '&orderBy=created_at,desc&relations=voucherSet.currencyCountry,createdByTeam,allocatedToServiceTeam').then(response => {
        vouchers.value = response.data.data
    }).catch(error => {
        console.log(error)
    })
}

</script>

<template>
    <div v-if="vouchers.data && vouchers.data.length">
        <table class="w-full text-xs">
            <thead class="bg-gray-200 font-bold border-b">
            <tr>
                <td class="p-2">ID</td>
                <td>Test?</td>
                <td>Created By</td>
                <td>Allocated To</td>

                <td class="text-right">Total Value</td>
                <td class="text-right">Value Remaining</td>
                <td>

                </td>
            </tr>
            </thead>
            <tbody>
            <tr v-for="voucher in vouchers.data" class="hover:bg-gray-100 border-b">
                <td class="py-1">
                    <Link :href="'/admin/voucher/' + voucher.id">
                        #{{ voucher.id }}
                    </Link>
                </td>
                <td>
                    {{voucher.is_test ? 'Yes' : ''}}
                </td>
                <td>
                    <Link :href="'/admin/team/' + voucher.created_by_team_id">
                        {{voucher.created_by_team?.name}}
                    </Link>
                </td>
                <td>
                    <Link :href="'/admin/team/' + voucher.allocated_to_service_team_id">
                        {{voucher.allocated_to_service_team?.name}}
                    </Link>
                </td>

                <td class="text-right">

                    <div >
                        {{ Number(voucher.voucher_value_original / 100 ).toFixed(2) }}
                        {{ voucher.voucher_set.currency_country?.currency_code}}
                    </div>



                </td>
                <td class="text-right">
                    <div>
                        {{ Number(voucher.voucher_value_remaining / 100 ).toFixed(2) }}
                        {{ voucher.voucher_set.currency_country?.currency_code}}
                    </div>


                </td>
                <td class="flex justify-end">
                    <Link class="secondaryButton" :href="'/admin/voucher/' + voucher.id">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                        </svg>
                    </Link>

                </td>
            </tr>
            </tbody>
        </table>
<!--        <Link :href="route('admin.voucher', voucher.id)" v-for="voucher in vouchers.data" class="hover:no-underline hover:opacity-75">-->
<!--            <div class="border-b flex justify-between items-center py-2 sm:p-2">-->
<!--                <div class="text-xs">-->
<!--                    <div v-if="voucher.voucher_short_code" class="font-bold text-sm">-->
<!--                        #{{ voucher.voucher_short_code }}-->
<!--                    </div>-->
<!--                    <div v-else class="font-bold text-sm">-->
<!--                        #{{ voucher.id }}-->
<!--                    </div>-->
<!--                    <div v-if="voucher.is_test" class="text-red-500">-->
<!--                        Test voucher-->
<!--                    </div>-->
<!--                    <div v-if="voucher.created_by_team">-->
<!--                        Created by: {{ voucher.created_by_team.name }}-->
<!--                    </div>-->
<!--                    <div v-if="voucher.allocated_to_service_team">-->
<!--                        Allocated to: {{ voucher.allocated_to_service_team.name }}-->
<!--                    </div>-->
<!--                    <div>-->
<!--                        Original value: ${{ (voucher.voucher_value_original / 100).toFixed(2) }}-->
<!--                    </div>-->
<!--                    <div>-->
<!--                        Remaining value: ${{ (voucher.voucher_value_remaining / 100).toFixed(2) }}-->
<!--                    </div>-->
<!--                    <div v-if="voucher.created_at">-->
<!--                        Created at: {{ dayjs.utc(voucher.created_at).fromNow() }} ({{ dayjs(voucher.created_at) }})-->
<!--                    </div>-->
<!--                </div>-->

<!--                <div>-->
<!--                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">-->
<!--                        <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/>-->
<!--                    </svg>-->
<!--                </div>-->
<!--            </div>-->
<!--        </Link>-->

        <div class="flex justify-end items-center mt-4">
            <div class="w-full lg:w-1/3">
                <PaginatorComponent
                    @setDataPage="getVouchers"
                    :pagination-data="vouchers"></PaginatorComponent>
            </div>
        </div>
    </div>
</template>
