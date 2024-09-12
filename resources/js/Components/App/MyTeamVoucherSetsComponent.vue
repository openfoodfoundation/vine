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
    filterVoucherSets: {
        required: false,
        default: null
    },
});

const myTeamVoucherSets = ref({})
const limit = ref(5)

onMounted(() => {
    getMyTeamVoucherSets()
});

function getMyTeamVoucherSets(page = 1) {
    let url = '/my-team-voucher-sets'

    if ($props.filterVoucherSets) {
        url = url + $props.filterVoucherSets
    }

    axios.get(url  + '?cached=false&page=' + page + '&limit=' + limit.value + '&relations=createdByTeam,allocatedToServiceTeam').then(response => {
        myTeamVoucherSets.value = response.data.data
    }).catch(error => {
        console.log(error)
    })
}

</script>

<template>
    <div v-if="myTeamVoucherSets.data && myTeamVoucherSets.data.length">

        <Link :href="route('voucher-set', voucherSet.id)" v-for="voucherSet in myTeamVoucherSets.data" class="hover:no-underline hover:opacity-75">
            <div class="border-b flex justify-between items-center py-2 sm:p-2">
                <div class="text-xs">
                    <div class="font-bold text-sm">
                        #{{ voucherSet.id }}
                    </div>
                    <div v-if="voucherSet.is_test" class="text-red-500">
                        Test voucher set
                    </div>
                    <div v-if="voucherSet.created_by_team && voucherSet.created_by_team_id !== usePage().props.auth.user.current_team_id">
                        Created by: {{ voucherSet.created_by_team.name }}
                    </div>
                    <div v-if="voucherSet.allocated_to_service_team && voucherSet.allocated_to_service_team_id !== usePage().props.auth.user.current_team_id">
                        Allocated to: {{ voucherSet.allocated_to_service_team.name }}
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
                    @setDataPage="getMyTeamVoucherSets"
                    :pagination-data="myTeamVoucherSets"></PaginatorComponent>
            </div>
        </div>
    </div>
</template>
