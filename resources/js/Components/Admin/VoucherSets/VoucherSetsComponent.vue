<script setup>
import {Link} from '@inertiajs/vue3';
import {onMounted, ref} from "vue";
import PaginatorComponent from "@/Components/Admin/PaginatorComponent.vue";
import dayjs from "dayjs";
import relativeTime from "dayjs/plugin/relativeTime";
import utc from "dayjs/plugin/utc"
import SecondaryButton from "@/Components/SecondaryButton.vue";

dayjs.extend(relativeTime);
dayjs.extend(utc);

const $props = defineProps({
    teamId: {
        required: false,
        default: null
    },
    filterVoucherSets: {
        required: false,
        default: null
    }
})

const limit = ref(50)
const voucherSets = ref({})

onMounted(() => {
    if ($props.teamId) {
        limit.value = 10
    }

    getVoucherSets()
})

function getVoucherSets(page = 1) {
    let urlBit = ''
    if ($props.teamId && $props.filterVoucherSets) {
        urlBit = '&where[]=' + $props.filterVoucherSets + ',' + $props.teamId
    }

    axios.get('/admin/voucher-sets?cached=false&page=' + page + '&limit=' + limit.value + urlBit + '&orderBy=created_at,desc&relations=createdByTeam,allocatedToServiceTeam,currencyCountry').then(response => {
        voucherSets.value = response.data.data
    }).catch(error => {
        console.log(error)
    })
}

</script>

<template>
    <div v-if="voucherSets.data && voucherSets.data.length">
        <table class="w-full text-xs">
            <thead class="bg-gray-200 font-bold border-b">
            <tr>
                <td class="p-2">ID</td>
                <td class="">Name</td>
                <td>Test?</td>
                <td>Created By</td>
                <td>Allocated To</td>
                <td>Approved</td>
                <td class="text-right">Total Value</td>
                <td class="text-right">Value Remainaing</td>
                <td>

                </td>
            </tr>
            </thead>
            <tbody>
            <tr v-for="voucherSet in voucherSets.data" class="hover:bg-gray-100 border-b">
                <td class="py-1">
                    <Link :href="'/admin/voucher-set/' + voucherSet.id">
                        #{{ voucherSet.id }}
                    </Link>
                </td>
                <td>
                    {{ voucherSet.name ?? '(Voucher set name not generated yet)' }}
                </td>
                <td>
                    {{voucherSet.is_test ? 'Yes' : ''}}
                </td>
                <td>
                    <Link :href="'/admin/team/' + voucherSet.created_by_team_id">
                        {{voucherSet.created_by_team?.name}}
                    </Link>
                </td>
                <td>
                    <Link :href="'/admin/team/' + voucherSet.allocated_to_service_team_id">
                        {{voucherSet.allocated_to_service_team?.name}}
                    </Link>
                </td>
                <td>
                    {{ voucherSet.merchant_approval_request_id ? 'Yes' : '--' }}
                </td>
                <td class="text-right">

                    <div v-if="voucherSet.merchant_approval_request_id">
                        {{ Number(voucherSet.total_set_value / 100 ).toFixed(2) }}
                        {{ voucherSet.currency_country?.currency_code}}
                    </div>
                    <div v-else>
                        --
                    </div>


                </td>
                <td class="text-right">
                    <div v-if="voucherSet.merchant_approval_request_id">
                        {{ Number(voucherSet.total_set_value_remaining / 100 ).toFixed(2) }}
                        {{ voucherSet.total_set_value_remaining?.currency_code}}
                    </div>
                    <div v-else>
                        --
                    </div>

                </td>
                <td class="flex justify-end">
                    <Link class="secondaryButton" :href="'/admin/voucher-set/' + voucherSet.id">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                        </svg>
                    </Link>

                </td>
            </tr>
            </tbody>
        </table>
<!--        <Link :href="route('admin.voucher-set', voucherSet.id)" v-for="voucherSet in voucherSets.data" class="hover:no-underline hover:opacity-75">-->
<!--            <div class="border-b flex justify-between items-center py-2 sm:p-2">-->
<!--                <div class="text-xs">-->
<!--                    <div class="font-bold text-sm">-->
<!--                        #{{ voucherSet.id }}-->
<!--                    </div>-->
<!--                    <div v-if="voucherSet.is_test" class="text-red-500 font-bold">-->
<!--                        Test voucher set-->
<!--                    </div>-->
<!--                    <div v-if="voucherSet.created_by_team">-->
<!--                        Created by: {{ voucherSet.created_by_team.name }}-->
<!--                    </div>-->
<!--                    <div v-if="voucherSet.allocated_to_service_team">-->
<!--                        Allocated to: {{ voucherSet.allocated_to_service_team.name }}-->
<!--                    </div>-->
<!--                    <div>-->
<!--                        Total set value: ${{ voucherSet.total_set_value / 100 }}-->
<!--                    </div>-->
<!--                    <div>-->
<!--                        Total remaining value: ${{ voucherSet.total_set_value_remaining / 100 }}-->
<!--                    </div>-->
<!--                    <div v-if="voucherSet.created_by_team">-->
<!--                        Created at: {{ dayjs.utc(voucherSet.created_at).fromNow() }} ({{ dayjs(voucherSet.created_at) }})-->
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
                    @setDataPage="getVoucherSets"
                    :pagination-data="voucherSets"></PaginatorComponent>
            </div>
        </div>

    </div>
</template>
