<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head, Link} from '@inertiajs/vue3';
import AdminTopNavigation from "@/Components/Admin/AdminTopNavigation.vue";
import {onMounted, ref} from "vue";
import dayjs from "dayjs";
import relativeTime from "dayjs/plugin/relativeTime";
import utc from "dayjs/plugin/utc"


dayjs.extend(relativeTime);
dayjs.extend(utc);

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
    axios.get('/admin/vouchers/' + $props.id + '?cached=false&relations=voucherSet.voucherSetMerchantTeams.merchantTeam,createdByTeam,allocatedToServiceTeam,voucherRedemptions.redeemedByUser,voucherRedemptions.redeemedByTeam').then(response => {
        voucher.value = response.data.data
    }).catch(error => {
        console.log(error)
    })
}

</script>

<template>
    <Head title="Voucher set"/>

    <AuthenticatedLayout>
        <template #header>
            <AdminTopNavigation></AdminTopNavigation>
        </template>


        <div class="grid grid-cols-2 gap-8 container mx-auto mt-8">
            <div class="card">
                <div class="card-header">
                    Voucher Details
                </div>
                <h2 class="opacity-25">
                    ID: {{ $props.id }}
                </h2>
                <div class="mt-4" v-if="voucher.voucher_short_code">
                    <h2>
                        Short Code: {{ voucher.voucher_short_code }}
                    </h2>
                    <div class="text-xs text-gray-500">
                        Short codes are used in unattended (online) redemptions
                    </div>
                </div>
                <div v-if="voucher.is_test" class="font-bold text-red-500 text-sm">
                    Test voucher
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
        </div>

        <div class="card">
            <div class="card-header">
                Voucher details
            </div>


            <div class="grid grid-cols-4 gap-y-12 text-center mt-8">
                <div>
                    <div class="font-bold text-3xl">
                        ${{ voucher.voucher_value_original / 100 }}
                    </div>
                    Original value
                </div>
                <div>
                    <div class="font-bold text-3xl">
                        ${{ voucher.voucher_value_remaining / 100 }}
                    </div>
                    Remaining value
                </div>
                <div>
                    <div class="font-bold text-3xl">
                        {{ voucher.num_voucher_redemptions ?? '0' }}
                    </div>
                    # Redemptions
                </div>


                <div v-if="voucher.last_redemption_at">
                    <div>
                        Last redeemed
                    </div>
                    <div class="font-bold text-3xl">
                        {{ dayjs.utc(voucher.last_redemption_at).fromNow() }}
                    </div>
                    <div class="text-xs">
                        ({{ dayjs(voucher.last_redemption_at) }})
                    </div>

                </div>

                <div v-if="voucher.voucher_set?.expires_at">
                    <div>
                        Expires
                    </div>
                    <div class="font-bold text-3xl">
                        {{ dayjs.utc(voucher.voucher_set.expires_at).fromNow() }}
                    </div>
                    <div class="text-xs">
                        ({{ dayjs(voucher.voucher_set.expires_at) }})
                    </div>

                </div>

            </div>

        </div>

        <div class="grid grid-cols-2 gap-8 container mx-auto">
            <div class="card">
                <div class="card-header">
                    Created by team
                </div>

                <div v-if="voucher.created_by_team">
                    <Link :href="route('admin.team', {id:voucher.created_by_team_id})">{{ voucher.created_by_team?.name }}</Link>
                </div>
                <div v-if="voucher.created_at" class="text-xs mt-2">
                    Created at: {{ dayjs.utc(voucher.created_at).fromNow() }} ({{ dayjs(voucher.created_at) }})
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    Allocated to team
                </div>

                <div v-if="voucher.allocated_to_service_team">
                    <Link :href="route('admin.team', {id:voucher.allocated_to_service_team_id})">{{ voucher.allocated_to_service_team?.name }}</Link>
                </div>
            </div>

        </div>

        <div class="card">
            <div class="card-header">
                Merchants Who May Redeem Vouchers
            </div>

            <div v-if="voucher.voucher_set?.voucher_set_merchant_teams && voucher.voucher_set?.voucher_set_merchant_teams.length">

               <div v-for="merchantTeam in voucher.voucher_set?.voucher_set_merchant_teams" >
                   <Link :href="route('admin.team', merchantTeam.merchant_team_id)" class="">
                       {{ merchantTeam.merchant_team?.name }}
                   </Link>
               </div>

            </div>

        </div>

        <div class="card">
            <div class="card-header">
                Voucher redemptions
            </div>

            <div v-if="voucher.voucher_redemptions && voucher.voucher_redemptions.length" class="text-sm">
                <Link :href="route('admin.voucher-redemption', redemption.id)" v-for="redemption in voucher.voucher_redemptions" class="hover:no-underline hover:opacity-75">
                    <div class="border-b flex justify-between items-center py-2 sm:p-2">
                        <div>
                            <div class="text-xs opacity-25">
                                #{{ redemption.id }}
                            </div>
                            <div>
                                Redeemed amount: <span class="font-bold">${{ redemption.redeemed_amount / 100 }}</span>
                            </div>
                            <div v-if="redemption.redeemed_by_user && redemption.redeemed_by_team">
                                Redeemed by: <span class="font-bold">{{ redemption.redeemed_by_user.name }} ({{ redemption.redeemed_by_team.name }})</span>
                            </div>
                            <div v-if="redemption.created_at">
                                Redeemed at: <span class="font-bold">{{ dayjs.utc(redemption.created_at).fromNow() }} ({{ dayjs(redemption.created_at) }})</span>
                            </div>
                        </div>
                        <div>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/>
                            </svg>
                        </div>
                    </div>
                </Link>
            </div>
        </div>

        <div class="pb-32"></div>
    </AuthenticatedLayout>
</template>
