<script setup>
import {Link} from '@inertiajs/vue3';
import {onMounted, ref} from "vue";
import PaginatorComponent from "@/Components/Admin/PaginatorComponent.vue";
import dayjs from "dayjs";
import relativeTime from "dayjs/plugin/relativeTime";
import utc from "dayjs/plugin/utc"

dayjs.extend(relativeTime);
dayjs.extend(utc);

const limit = ref(50)
const voucherRedemptions = ref({})

onMounted(() => {
    getVoucherRedemptions()
})

function getVoucherRedemptions(page = 1) {
    axios.get('/admin/voucher-redemptions?cached=false&page=' + page + '&limit=' + limit.value + '&orderBy=created_at,desc&relations=redeemedByUser,redeemedByTeam').then(response => {
        voucherRedemptions.value = response.data.data
    }).catch(error => {
        console.log(error)
    })
}

</script>

<template>
    <div v-if="voucherRedemptions.data && voucherRedemptions.data.length">

        <Link :href="route('admin.voucher-redemption', voucherRedemption.id)" v-for="voucherRedemption in voucherRedemptions.data" class="hover:no-underline hover:opacity-75">
            <div class="border-b flex justify-between items-center py-2 sm:p-2">
                <div class="text-xs">
                    <div class="font-bold text-sm">
                        #{{ voucherRedemption.id }}
                    </div>
                    <div v-if="voucherRedemption.is_test" class="text-red-500 font-bold">
                        Test voucher redemption
                    </div>
                    <div>
                        Voucher: #{{ voucherRedemption.voucher_id }}
                    </div>
                    <div>
                        Voucher set: #{{ voucherRedemption.voucher_set_id }}
                    </div>
                    <div v-if="voucherRedemption.redeemed_by_user && voucherRedemption.redeemed_by_team">
                        Redeemed by: {{ voucherRedemption.redeemed_by_user.name }} ({{ voucherRedemption.redeemed_by_team.name }})
                    </div>
                    <div>
                        Redeemed amount: ${{ voucherRedemption.redeemed_amount / 100 }}
                    </div>
                    <div v-if="voucherRedemption.created_by_team">
                        Created at: {{ dayjs.utc(voucherRedemption.created_at).fromNow() }} ({{ dayjs(voucherRedemption.created_at) }})
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
                    @setDataPage="getVoucherRedemptions"
                    :pagination-data="voucherRedemptions"></PaginatorComponent>
            </div>
        </div>

    </div>
</template>
