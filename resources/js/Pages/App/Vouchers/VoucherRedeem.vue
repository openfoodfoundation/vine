<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head} from '@inertiajs/vue3';
import {onMounted, ref, watch} from "vue";
import Swal from "sweetalert2";
import dayjs from "dayjs";
import relativeTime from "dayjs/plugin/relativeTime";
import utc from "dayjs/plugin/utc";

dayjs.extend(relativeTime);
dayjs.extend(utc);

const $props = defineProps({
    voucher: {
        type: Object,
        required: true,
    },
});


const redeeming = ref(false)
const redeemingPartial = ref(false)
const redeemingPartialDollarAmount = ref(0)
const redeemingPartialDollarAmountIsValid = ref(false)

onMounted(() => {

});

function beginRedeemingPartial() {
    redeemingPartialDollarAmount.value = (parseInt($props.voucher.voucher_value_remaining) / 100).toFixed(2)
    redeeming.value = true
    redeemingPartial.value = true
}

function cancelRedeemingPartial() {
    redeeming.value = false
    redeemingPartial.value = false
}


function redeemAll() {
    redeeming.value = true
    redeemingPartial.value = false

    Swal.fire({
        title: 'Redeem all $' + ($props.voucher.voucher_value_remaining / 100).toFixed(2) + '?',
        html: '<p>This will fully redeem this voucher. Note: Please only confirm your action with a SINGLE click - do not double click.</p>',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Redeem!',
    }).then(result => {
        if (result.value) {
            redeemVoucher($props.voucher.voucher_value_remaining.toFixed(0))
        }
    });
}

function redeemPartial() {
    redeeming.value = true
    Swal.fire({
        title: 'Redeem $' + redeemingPartialDollarAmount.value + '?',
        html: '<p>This will partially redeem this voucher. NOTE: Please only confirm your action with a SINGLE click - do not double click.</p>',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Redeem!',
    }).then(result => {
        if (result.value) {
            redeemVoucher((redeemingPartialDollarAmount.value * 100).toFixed(0))
        } else {
            redeeming.value = false;
        }
    });

}

function redeemVoucher(amount) {

    redeeming.value = true;

    let payload = {
        voucher_id: $props.voucher.id,
        voucher_set_id: $props.voucher.voucher_set_id,
        amount: amount
    }

    axios.post('/voucher-redemptions', payload).then(response => {

        Swal.fire({
            icon: 'success',
            title: 'Redeemed.',
            text: response.data.meta.message,
        });

        redeemingPartial.value = false


        setTimeout(fn => {
            window.location.reload();
        }, 1000)

    }).catch(error => {
        Swal.fire({
            icon: 'error',
            title: 'Oops!',
            text: error.response.data.meta.message,
        });
        redeeming.value = false;
    })
}

watch(redeemingPartialDollarAmount, (val) => {
    redeemingPartialDollarAmountIsValid.value = (val > 0) &&
        (
            parseInt((val * 100).toFixed(0)) <= parseInt($props.voucher.voucher_value_remaining.toFixed(0))
        )
})
</script>

<template>
    <Head title="Voucher redeem"/>

    <AuthenticatedLayout>

        <div class="card">

            <div class="title text-2xl">
                Redeem Voucher <span class="uppercase">{{ voucher.voucher_short_code }}</span>
            </div>

            <div class="my-4">

                <div class="title text-green text-xl text-green-500" v-if="voucher.voucher_value_remaining > 0">Voucher
                    is Valid
                </div>
                <div class="title" v-else>Voucher is Fully Redeemed!</div>

                <div class="title text-red text-lg text-red-500" v-if="voucher.is_test">
                    This is a test voucher.
                </div>

                <div>
                    <div class="text-2xl font-bold mt-12">
                        ${{ (voucher.voucher_value_remaining / 100).toFixed(2) }} remaining
                    </div>

                    <div>
                        of ${{ (voucher.voucher_value_original / 100).toFixed(2) }} original value
                    </div>
                </div>

                <div v-if="voucher.voucher_value_remaining > 0" class="mt-12">

                    <div v-if="redeemingPartial">

                        <div class="my-4">

                            How much should be redeemed?

                            <input inputmode="decimal" pattern="[0-9]*" type="text" step="0.01"
                                   v-model.number="redeemingPartialDollarAmount"
                                   class="w-full text-center text-xl rounded p-8 border-2 focus:outline-none" min="0.01"
                                   :class="{'border-green-500': redeemingPartialDollarAmountIsValid, 'border-red-500': !redeemingPartialDollarAmountIsValid}"
                            >

                            <div v-if="!redeemingPartialDollarAmountIsValid" class="my-2 text-red-500">
                                Invalid redemption amount.
                            </div>

                        </div>

                        <div class="flex justify-between items-center" v-if="redeemingPartialDollarAmountIsValid">
                            <div class="w-1/2 pr-2">
                                <button class="w-full p-8 font-bold text-2xl rounded border bg-gray-300"
                                        @click="cancelRedeemingPartial()">
                                    Cancel
                                </button>
                            </div>
                            <div class="w-1/2 pl-2">
                                <button class="w-full p-8 font-bold text-2xl rounded border bg-gray-300"
                                        @click="redeemPartial()">
                                    Redeem
                                </button>
                            </div>
                        </div>

                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2" v-else>
                        <div class="">
                            <button class="w-full p-8 font-bold text-2xl rounded border bg-gray-300"
                                    :disabled="redeeming"
                                    @click.once="beginRedeemingPartial()">
                                Redeem PART
                            </button>
                        </div>
                        <div class="">
                            <button class="w-full p-8 font-bold text-2xl rounded border bg-gray-300"
                                    :disabled="redeeming"
                                    @click.once="redeemAll()">
                                Redeem ALL
                            </button>
                        </div>
                    </div>
                </div>

                <div class="mt-8" v-if="!voucher.voucher_redemptions">
                    <button class="w-full p-2 rounded border">
                        See Redemptions
                    </button>
                </div>

                <div class="mt-12 text-left" v-if="voucher.voucher_redemptions">
                    <div class="title">
                        Redemptions ({{ voucher.voucher_redemptions.length }})
                    </div>

                    <div>
                        <div v-for="redemption in voucher.voucher_redemptions"
                             class="flex justify-between items-center py-2 border-b">
                            <div>
                                <div class="text-lg">
                                    ${{ (redemption.redeemed_amount / 100).toFixed(2) }}
                                </div>
                            </div>

                            <div class="text-center">
                                {{ dayjs.utc(redemption.created_at).fromNow() }}
                                <div class="text-xs">
                                    ({{ dayjs(redemption.created_at) }})
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </AuthenticatedLayout>
</template>
