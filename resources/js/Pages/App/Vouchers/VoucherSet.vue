<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head, Link, usePage} from '@inertiajs/vue3';
import {onMounted, ref} from "vue";
import dayjs from "dayjs";
import relativeTime from "dayjs/plugin/relativeTime";
import utc from "dayjs/plugin/utc"
import VouchersComponent from "@/Components/Admin/Vouchers/VouchersComponent.vue";

dayjs.extend(relativeTime);
dayjs.extend(utc);

const $props = defineProps({
    voucherSetId: {
        type: String,
        required: false,
    },
});

const vouchers = ref({})
const voucherSet = ref({})

onMounted(() => {
    getVoucherSet()
    getVouchers()
});

function getVouchers() {
    axios.get('/my-team-vouchers?cached=false&where=voucher_set_id,' + $props.voucherSetId).then(response => {
        vouchers.value = response.data.data
    }).catch(error => {
        console.log(error)
    })
}

function getVoucherSet() {
    axios.get('/my-team-voucher-sets/' + $props.voucherSetId + '?cached=false&relations=createdByTeam,allocatedToServiceTeam,voucherSetMerchantTeams.merchantTeam').then(response => {
        voucherSet.value = response.data.data
    }).catch(error => {
        console.log(error)
    })
}

</script>

<template>
    <Head title="Voucher Set"/>

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-normal text-xl text-gray-800 leading-tight">Voucher Set</h2>
        </template>

        <div class="card">
            <h2>
                {{ voucherSet.id }}
            </h2>
            <div v-if="voucherSet.is_test" class="font-bold text-red-500 text-sm">
                Test voucher set
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                Voucher set details
            </div>

            <div class="grid grid-cols-4 gap-y-12 text-center mt-8">
                <div>
                    <div class="font-bold text-3xl">
                        ${{ voucherSet.total_set_value / 100 }}
                    </div>
                    Total set value
                </div>
                <div>
                    <div class="font-bold text-3xl">
                        ${{ voucherSet.total_set_value_remaining / 100 }}
                    </div>
                    Total remaining value
                </div>
                <div>
                    <div class="font-bold text-3xl">
                        {{ Math.round(((voucherSet.total_set_value - voucherSet.total_set_value_remaining) / voucherSet.total_set_value) * 10000) / 100 }}%
                    </div>
                    Redeemed percentage
                </div>
                <div>
                    <div class="font-bold text-3xl">
                        {{ voucherSet.num_vouchers }}
                    </div>
                    # Vouchers
                </div>
                <div>
                    <div class="font-bold text-3xl">
                        {{ voucherSet.num_voucher_redemptions }}
                    </div>
                    # Redemptions
                </div>

                <div v-if="voucherSet.last_redemption_at">
                    <div>
                        Last redeemed
                    </div>
                    <div class="font-bold text-3xl">
                        {{ dayjs.utc(voucherSet.last_redemption_at).fromNow() }}
                    </div>
                    <div class="text-xs">
                        ({{ dayjs(voucherSet.last_redemption_at) }})
                    </div>
                </div>

                <div v-if="voucherSet.expires_at">
                    <div>
                        Expires
                    </div>
                    <div class="font-bold text-3xl">
                        {{ dayjs.utc(voucherSet.expires_at).fromNow() }}
                    </div>
                    <div class="text-xs">
                        ({{ dayjs(voucherSet.expires_at) }})
                    </div>
                </div>

            </div>
        </div>

        <div class="card">
            <div class="card-header">
                Created by team
            </div>

            <div v-if="voucherSet.created_by_team">
                {{ voucherSet.created_by_team.name }}
            </div>
            <div v-if="voucherSet.created_at" class="text-xs mt-2">
                Created at: {{ dayjs.utc(voucherSet.created_at).fromNow() }} ({{ dayjs(voucherSet.created_at) }})
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                Allocated to team
            </div>

            <div v-if="voucherSet.allocated_to_service_team">
                {{ voucherSet.allocated_to_service_team.name }}
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                Redeemable merchants
            </div>

            <div v-if="voucherSet.voucher_set_merchant_teams">
                <ul v-for="voucherSetMerchant in voucherSet.voucher_set_merchant_teams" class="list-disc ml-4">
                    <li>{{ voucherSetMerchant.merchant_team.name }}</li>
                </ul>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                Vouchers
            </div>

            <div v-if="vouchers.data && vouchers.data.length">
                <Link :href="route('voucher', voucher.id)" v-for="voucher in vouchers.data" class="hover:no-underline hover:opacity-75">
                    <div class="border-b flex justify-between items-center py-2 sm:p-2">
                        <div v-if="voucher.voucher_short_code">
                            #{{ voucher.voucher_short_code }}
                        </div>
                        <div v-else>
                            #{{ voucher.id }}
                        </div>

                        <div>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
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
