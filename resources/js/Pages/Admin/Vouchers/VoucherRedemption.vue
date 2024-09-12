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

const voucherRedemption = ref({})

onMounted(() => {
    getVoucherRedemption()
})

function getVoucherRedemption() {
    axios.get('/admin/voucher-redemptions/' + $props.id + '?cached=false&relations=redeemedByUser,redeemedByTeam,voucher').then(response => {
        voucherRedemption.value = response.data.data
    }).catch(error => {
        console.log(error)
    })
}

</script>

<template>
    <Head title="Voucher redemption"/>

    <AuthenticatedLayout>
        <template #header>
            <AdminTopNavigation></AdminTopNavigation>
        </template>


        <div class="grid grid-cols-2 gap-8 container mx-auto mt-8">
            <div class="card">
                <div class="card-header">
                    Voucher Redemption Details
                </div>
                <h2 class="opacity-25">
                    ID: {{ $props.id }}
                </h2>
                <div v-if="voucherRedemption.is_test" class="font-bold text-red-500 text-sm">
                    Test voucher redemption
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    Voucher and voucher set
                </div>

                <div v-if="voucherRedemption.voucher_id">
                    <Link :href="route('admin.voucher', voucherRedemption.voucher_id)">{{ voucherRedemption.voucher_id }}</Link>
                </div>
                <div v-if="voucherRedemption.voucher_set_id">
                    <Link :href="route('admin.voucher-set', voucherRedemption.voucher_set_id)">{{ voucherRedemption.voucher_set_id }}</Link>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                Voucher redemption details
            </div>

            <div class="grid grid-cols-4 gap-y-12 text-center mt-8">
                <div>
                    <div class="font-bold text-3xl">
                        ${{ voucherRedemption.redeemed_amount / 100 }}
                    </div>
                    Redeemed value
                </div>

                <div v-if="voucherRedemption.voucher">
                    <div class="font-bold text-3xl">
                        ${{ voucherRedemption.voucher.voucher_value_remaining / 100 }}
                    </div>
                    Voucher remaining value
                </div>

                <div>
                    <div>
                        Redeemed at
                    </div>
                    <div class="font-bold text-3xl">
                        {{ dayjs.utc(voucherRedemption.created_at).fromNow() }}
                    </div>
                    <div class="text-xs">
                        ({{ dayjs(voucherRedemption.created_at) }})
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                Redeemed by
            </div>

            <div v-if="voucherRedemption.redeemed_by_user">
                <Link :href="route('admin.user', voucherRedemption.redeemed_by_user_id)">{{ voucherRedemption.redeemed_by_user.name }}</Link>
            </div>
            <div v-if="voucherRedemption.redeemed_by_team">
                <Link :href="route('admin.team', voucherRedemption.redeemed_by_team_id)">{{ voucherRedemption.redeemed_by_team.name }}</Link>
            </div>
        </div>

        <div class="pb-32"></div>
    </AuthenticatedLayout>
</template>
