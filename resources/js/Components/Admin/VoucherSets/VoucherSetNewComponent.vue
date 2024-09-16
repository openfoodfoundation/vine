<script setup>

import {onMounted, ref, watch} from "vue";
import swal from "sweetalert2";
import {usePage} from "@inertiajs/vue3";
import SecondaryButton from "@/Components/SecondaryButton.vue";

const $props = usePage().props;

const stage = ref(0);

const tomorrowsDate = new Date()

const allocatedToServiceTeam = ref('');

const teamServiceTeams = ref([]);

const denominations = ref([
    {value: 500, number: 1},
])

const voucherSet = ref({
    is_test: 0,
    allocated_to_service_team_id: '',
    funded_by_team_id: '',
    total_set_value: 0,
    denomination_json: {},
    expires_at: '',
});

function allocationRemaining() {
    return voucherSet.value.total_set_value - totalDenominations();
}

function denominationAdd() {
    denominations.value.push({value: 100, number: 1})
}


function denominationDelete(index) {
    if (index > -1) {
        denominations.value.splice(index, 1);
    }
}


function totalDenominations() {
    let totalAssigned = 0;

    denominations.value.forEach(denomination => {
        totalAssigned += (denomination.number * denomination.value);
    });

    return totalAssigned;
}

function getServiceTeams() {
    axios.get('/admin/team-service-teams?relations=serviceTeam&where[]=team_id,' + $props.auth.user.current_team_id).then(response => {
        teamServiceTeams.value = response.data.data.data;
    }).catch(error => {
        swal.fire({
            icon: 'error',
            title: 'Oops!',
            text: error.response.data.meta.message,
        });
    });

}

function stageNext() {
    stage.value++
    stage.value = Math.min(stage.value, 6)
}

function stagePrevious() {
    stage.value--
    stage.value = Math.max(stage.value, 0)
}

onMounted(() => {
    getServiceTeams();
})

watch(denominations, () => {
    allocationRemaining();
}, {deep: true})

</script>

<template>
    <div>
        <div class="title text-4xl mt-4">
            Create A Voucher Set
        </div>
        <div class="">

            <div v-if="stage === 0">
                <div class="my-8">
                    This process will bring you through creating a voucher set.
                </div>


                <div class="flex justify-start mt-8">
                    <SecondaryButton @click="stageNext()">
                        Get started
                    </SecondaryButton>
                </div>


            </div>

            <div v-if="stage === 1">
                <div class="my-8">
                    Is this a test voucher set? A test voucher set is for when you want to test the process, but the
                    vouchers are not real.
                </div>


                <div class="my-8">
                    <label class="mb-2" for="voucherSet.is_test">
                        <input id="voucherSet.is_test" v-model="voucherSet.is_test" class="mr-2" type="checkbox"> YES -
                        this is a test voucher set.
                    </label>
                </div>

                <div class="flex justify-start gap-x-4 mt-8">
                    <SecondaryButton @click="stagePrevious()">
                        <svg class="size-6" fill="none" stroke="currentColor" stroke-width="1.5"
                             viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path d="M15.75 19.5 8.25 12l7.5-7.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        Back
                    </SecondaryButton>

                    <SecondaryButton @click="stageNext()">
                        Next
                        <svg class="size-6" fill="none" stroke="currentColor" stroke-width="1.5"
                             viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path d="m8.25 4.5 7.5 7.5-7.5 7.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>

                    </SecondaryButton>
                </div>

            </div>

            <div v-if="stage === 2">
                <div class="my-8">
                    Who are you assigning this voucher set to?
                </div>


                <div class="mt-8">
                    <select v-model="allocatedToServiceTeam">
                        <option :value="''">Please pick a team</option>
                        <option v-for="teamServiceTeam in teamServiceTeams" :value="teamServiceTeam.service_team">
                            {{ teamServiceTeam.service_team?.name }}
                        </option>
                    </select>
                </div>


                <div class="flex justify-start gap-x-4 mt-8">
                    <SecondaryButton @click="stagePrevious()">
                        <svg class="size-6" fill="none" stroke="currentColor" stroke-width="1.5"
                             viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path d="M15.75 19.5 8.25 12l7.5-7.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        Back
                    </SecondaryButton>

                    <SecondaryButton v-if="allocatedToServiceTeam.id" @click="stageNext()">
                        Next
                        <svg class="size-6" fill="none" stroke="currentColor" stroke-width="1.5"
                             viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path d="m8.25 4.5 7.5 7.5-7.5 7.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>

                    </SecondaryButton>
                </div>

            </div>


            <div v-if="stage === 3">
                <div class="my-8">
                    What will the value of the set be, in <span
                    class="font-bold">{{ $props.auth.teamCountry?.currency_code }}</span>?
                </div>


                <div class="my-8">
                    <label for="voucherSet.total_set_value">
                        <input id="voucherSet.total_set_value" v-model="voucherSet.total_set_value" type="number">
                    </label>
                </div>


                <div class="flex justify-start gap-x-4 mt-8">
                    <SecondaryButton @click="stagePrevious()">
                        <svg class="size-6" fill="none" stroke="currentColor" stroke-width="1.5"
                             viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path d="M15.75 19.5 8.25 12l7.5-7.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        Back
                    </SecondaryButton>

                    <SecondaryButton v-if="voucherSet.total_set_value > 0" @click="stageNext()">
                        Next
                        <svg class="size-6" fill="none" stroke="currentColor" stroke-width="1.5"
                             viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path d="m8.25 4.5 7.5 7.5-7.5 7.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>

                    </SecondaryButton>

                    <div v-else class="text-sm text-red-500 italic">
                        *Value must be a number greater than zero
                    </div>
                </div>

            </div>


            <div v-if="stage === 4">
                <div class="flex justify-start items-center">
                    <div class="mt-4">
                        <div>
                            Available to assign: {{ voucherSet.total_set_value }}
                            {{ $props.auth.teamCountry?.currency_code }}
                        </div>
                        <div :class="{'text-red': allocationRemaining() < 0}">
                            Remaining: {{ allocationRemaining() }}
                        </div>

                        <div>
                            <div v-for="(denomination, index) in denominations"
                                 class="mb-2 flex justify-between items-center border-b py-4">


                                <div>
                                    <div class="text-xs">
                                        Create
                                    </div>
                                    <div>
                                        <input v-model="denomination.number" class="border rounded p-1"
                                               type="number">
                                    </div>
                                </div>
                                <div class="pl-4">
                                    <div class="text-xs">
                                        of {{ $props.auth.teamCountry?.currency_code }}
                                    </div>

                                    <div>
                                        <input v-model="denomination.value" class="border rounded p-1"
                                               step="1"
                                               type="number">

                                    </div>
                                </div>

                                <div class="pt-6">
                                    <button class="p-2 text-purple text-sm" @click="denominationDelete(index)">
                                        <i class="fa fa-times"></i> Remove
                                    </button>

                                </div>
                            </div>


                            <div class="pb-12">
                                <SecondaryButton @click="denominationAdd()">
                                    Add row
                                    <i class="fa fa-plus"></i>
                                </SecondaryButton>
                            </div>
                        </div>


                    </div>

                    <div class="text-center mx-24 p-8 rounded-lg border-4 border-gray-200">
                        <div class="text-2xl">
                            Total assigned:
                        </div>
                        <div
                            :class="{'text-green-500': allocationRemaining() >= 0, 'text-red-500': allocationRemaining() < 0}"
                            class="text-3xl mt-4">
                            {{ totalDenominations() }}

                            <div class="text-xs">
                                out of {{ voucherSet.total_set_value }}
                            </div>

                        </div>
                        <div v-if="allocationRemaining() < 0" class="text-xs text-red-500 mt-4">
                            - over budget -
                        </div>
                        <div v-else-if="allocationRemaining() > 0" class="text-xs text-green-500 mt-4">
                            {{ allocationRemaining() }} remaining
                        </div>
                        <div v-else class="text-xs text-gray-500 mt-4">
                            Voucher fully allocated
                        </div>

                    </div>
                </div>

                <div class="flex justify-start gap-x-4 mt-8">
                    <SecondaryButton @click="stagePrevious()">
                        <svg class="size-6" fill="none" stroke="currentColor" stroke-width="1.5"
                             viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path d="M15.75 19.5 8.25 12l7.5-7.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        Back
                    </SecondaryButton>

                    <SecondaryButton v-if="allocationRemaining() >= 0" @click="stageNext()">
                        Next
                        <svg class="size-6" fill="none" stroke="currentColor" stroke-width="1.5"
                             viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path d="m8.25 4.5 7.5 7.5-7.5 7.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </SecondaryButton>
                </div>


            </div>


            <div v-if="stage === 5">
                <div class="my-8">
                    When will these vouchers expire?

                    <div class="mt-4">

                        <label class="italic">
                            Select expiry date
                            <input v-model="voucherSet.expires_at" class="rounded" type="date" :min="tomorrowsDate">
                        </label>


                    </div>

                    <div class="mt-4">
                        <div v-if="voucherSet.expires_at" class="text-sm text-orange-500">
                            Selected expiry date: {{ voucherSet.expires_at }}
                        </div>
                        <div v-else class="text-xs italic">
                            Not selecting an expiry date will mean vouchers stay valid forever.
                        </div>
                    </div>

                    <div class="flex justify-start gap-x-4 mt-8">
                        <SecondaryButton @click="stagePrevious()">
                            <svg class="size-6" fill="none" stroke="currentColor" stroke-width="1.5"
                                 viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path d="M15.75 19.5 8.25 12l7.5-7.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            Back
                        </SecondaryButton>

                        <SecondaryButton v-if="allocationRemaining() >= 0" @click="stageNext()">
                            Next
                            <svg class="size-6" fill="none" stroke="currentColor" stroke-width="1.5"
                                 viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path d="m8.25 4.5 7.5 7.5-7.5 7.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </SecondaryButton>
                    </div>

                </div>

            </div>

            <div v-if="stage === 6">

            </div>

            <div v-if="stage === 7">

            </div>
        </div>
    </div>
</template>


