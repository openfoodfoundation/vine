<script setup>
import {Link, usePage} from '@inertiajs/vue3';
import {onMounted, ref} from "vue";
import dayjs from "dayjs";
import relativeTime from "dayjs/plugin/relativeTime";
import utc from "dayjs/plugin/utc"
import PaginatorComponent from "@/Components/Admin/PaginatorComponent.vue";

dayjs.extend(relativeTime);
dayjs.extend(utc);

const $props = defineProps({
    filterVouchers: {
        required: false,
        default: null
    },
});

const myTeamVouchers = ref({})
const limit = ref(5)

onMounted(() => {
    getMyTeamVouchers()
});

function getMyTeamVouchers(page = 1) {
    let urlBit = ''
    if ($props.filterVouchers) {
        urlBit = '&filter-vouchers=' + $props.filterVouchers
    }

    axios.get('/my-team-vouchers?cached=false' + urlBit + '&page=' + page + '&limit=' + limit.value + '&relations=createdByTeam,allocatedToServiceTeam').then(response => {
        myTeamVouchers.value = response.data.data
    }).catch(error => {
        console.log(error)
    })
}

</script>

<template>
    <div v-if="myTeamVouchers.data && myTeamVouchers.data.length">

        <Link :href="route('voucher', voucher.id)" v-for="voucher in myTeamVouchers.data" class="hover:no-underline hover:opacity-75">
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
                    <div v-if="voucher.created_by_team && voucher.created_by_team_id !== usePage().props.auth.user.current_team_id">
                        Created by: {{ voucher.created_by_team.name }}
                    </div>
                    <div v-if="voucher.allocated_to_service_team && voucher.allocated_to_service_team !== usePage().props.auth.user.current_team_id">
                        Allocated to: {{ voucher.allocated_to_service_team.name }}
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
                    @setDataPage="getMyTeamVouchers"
                    :pagination-data="myTeamVouchers"></PaginatorComponent>
            </div>
        </div>
    </div>
</template>
