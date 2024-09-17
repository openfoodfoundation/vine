<script setup>

import {onMounted, ref, watch} from "vue";
import swal from "sweetalert2";
import {usePage} from "@inertiajs/vue3";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";

const $props = usePage().props;

const stage = ref(0);

const merchantTeamSearchQuery = ref('');
const serviceTeamSearchQuery = ref('');

const selectedMerchantTeams = ref([]);
const selectedServiceTeam = ref('');

const allTeamMerchantTeams = ref([]);
const allTeamServiceTeams = ref([]);

const filteredTeamMerchantTeams = ref([]);
const filteredTeamServiceTeams = ref([]);

const denominations = ref([
    {value: 500, number: 1},
])

const voucherSet = ref({
    is_test: 0,
    allocated_to_service_team_id: '',
    merchant_team_ids: [],
    funded_by_team_id: '',
    total_set_value: 0,
    denomination_json: {},
    expires_at: '',
    voucher_set_type: '',
});

function allocationRemaining() {
    return voucherSet.value.total_set_value - totalDenominations();
}

function createVoucherSet() {

    axios.post('/admin/voucher-sets', voucherSet.value).then(response => {

        swal.fire({
            title: "Nice!",
            icon: "success",
            text: response.data.data.message,
            showConfirmButton: false,
            timer: 600
        });

    }).catch(error => {

        swal.fire({
            title: "Oops!",
            icon: "error",
            text: error.response.data.meta.message
        });

        console.log(error);

    })
}

function denominationAdd() {
    denominations.value.push({value: 100, number: 1})
}

function denominationDelete(index) {
    if (index > -1) {
        denominations.value.splice(index, 1);
    }
}

function filterMerchantTeams() {
    filteredTeamMerchantTeams.value = allTeamMerchantTeams.value.filter(team => team.merchant_team?.name.toLowerCase().includes(merchantTeamSearchQuery.value));
}

function filterServiceTeams() {
    filteredTeamServiceTeams.value = allTeamServiceTeams.value.filter(team => team.service_team?.name.toLowerCase().includes(serviceTeamSearchQuery.value));
}

function getMerchantTeams() {
    axios.get('/admin/team-merchant-teams?relations=merchantTeam&where[]=team_id,' + $props.auth.user.current_team_id).then(response => {
        allTeamMerchantTeams.value = response.data.data.data;
    }).catch(error => {
        swal.fire({
            icon: 'error',
            title: 'Oops!',
            text: error.response.data.meta.message,
        });
    });
}

function getServiceTeams() {
    axios.get('/admin/team-service-teams?relations=serviceTeam&where[]=team_id,' + $props.auth.user.current_team_id).then(response => {
        allTeamServiceTeams.value = response.data.data.data;
    }).catch(error => {
        swal.fire({
            icon: 'error',
            title: 'Oops!',
            text: error.response.data.meta.message,
        });
    });
}

function selectMerchantTeam(team) {

    selectedMerchantTeams.value.push(team.id)
}

function selectServiceTeam(team) {
    selectedServiceTeam.value = team;
    voucherSet.value.allocated_to_service_team_id = team.id;
    filteredTeamServiceTeams.value = [];
    serviceTeamSearchQuery.value = '';
}

function stageNext() {
    stage.value++
    stage.value = Math.min(stage.value, 7)
}

function stagePrevious() {
    stage.value--
    stage.value = Math.max(stage.value, 0)
}

function totalDenominations() {
    let totalAssigned = 0;

    denominations.value.forEach(denomination => {
        totalAssigned += (denomination.number * denomination.value);
    });

    return totalAssigned;
}

onMounted(() => {
    getServiceTeams();
    getMerchantTeams();
})

watch(denominations, () => {
    allocationRemaining();
}, {deep: true})

watch(merchantTeamSearchQuery, () => {
    filterMerchantTeams();
})

watch(serviceTeamSearchQuery, () => {
    filterServiceTeams();
})


</script>

<template>
    <div>

        <div class="">

            <div v-if="stage === 0">
                <div class="title text-4xl my-4">
                    Create A Voucher Set
                </div>

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
                <div class="title text-4xl my-4">
                    Is this a test voucher set?
                </div>

                <div class="my-8">
                    A test voucher set is for when you want to test the process, but the
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

                <div class="title text-4xl my-4">
                    The service team
                </div>

                <div class="my-8">
                    Which service team are you allocating this voucher set to?
                </div>


                <div v-if="!voucherSet.allocated_to_service_team_id">
                    <label for="service-team-search">
                        Search for a service team
                        <TextInput id="service-team-search" v-model="serviceTeamSearchQuery"
                                   class="block w-1/2 md:w-1/3"/>
                    </label>

                    <div class="my-4 flex flex-wrap gap-2">
                        <PrimaryButton v-for="teamServiceTeam in filteredTeamServiceTeams"
                                       @click="selectServiceTeam(teamServiceTeam.service_team)">
                            {{ teamServiceTeam.service_team.name }}
                        </PrimaryButton>
                    </div>
                </div>

                <div v-else>
                    Selected service team: {{ selectedServiceTeam.name }}
                </div>


                <div class="flex justify-start gap-x-4 mt-8">
                    <SecondaryButton @click="stagePrevious()">
                        <svg class="size-6" fill="none" stroke="currentColor" stroke-width="1.5"
                             viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path d="M15.75 19.5 8.25 12l7.5-7.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        Back
                    </SecondaryButton>

                    <SecondaryButton v-if="voucherSet.allocated_to_service_team_id" @click="stageNext()">
                        Next
                        <svg class="size-6" fill="none" stroke="currentColor" stroke-width="1.5"
                             viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path d="m8.25 4.5 7.5 7.5-7.5 7.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>

                    </SecondaryButton>
                </div>

            </div>








            <div v-if="stage === 3">

                <div class="title text-4xl my-4">
                    The merchant team(s)
                </div>

                <div class="my-8">
                    Which merchant team(s) are you assigning this voucher set to?
                </div>


                <div>
                    <label for="service-team-search">
                        Search for a merchant team
                        <TextInput id="merchant-team-search" v-model="merchantTeamSearchQuery"
                                   class="block w-1/2 md:w-1/3"/>
                    </label>

                    <div class="my-4 flex flex-wrap gap-2">
                        <PrimaryButton v-for="teamMerchantTeam in filteredTeamMerchantTeams"
                                       @click="selectMerchantTeam(teamMerchantTeam.merchant_team)">
                            {{ teamMerchantTeam.merchant_team.name }}
                        </PrimaryButton>
                    </div>
                </div>

                <div v-else>
                    Selected service team: {{ selectedServiceTeam.name }}
                </div>


                <div class="flex justify-start gap-x-4 mt-8">
                    <SecondaryButton @click="stagePrevious()">
                        <svg class="size-6" fill="none" stroke="currentColor" stroke-width="1.5"
                             viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path d="M15.75 19.5 8.25 12l7.5-7.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        Back
                    </SecondaryButton>

                    <SecondaryButton v-if="voucherSet.allocated_to_service_team_id" @click="stageNext()">
                        Next
                        <svg class="size-6" fill="none" stroke="currentColor" stroke-width="1.5"
                             viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path d="m8.25 4.5 7.5 7.5-7.5 7.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>

                    </SecondaryButton>
                </div>

            </div>







            <div v-if="stage === 4">

                <div class="title text-4xl my-4">
                    Total value
                </div>

                <div class="my-8">
                    <label for="voucherSet.total_set_value">
                        What will the total value of the voucher set be, in <span
                        class="font-bold">{{ $props.auth.teamCountry?.currency_code }}</span>?
                        <input id="voucherSet.total_set_value" v-model="voucherSet.total_set_value" class="block mt-2"
                               type="number">
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

                <div class="title text-4xl my-4">
                    Denominations
                </div>


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

                <div class="title text-4xl my-4">
                    Expiration
                </div>


                <div class="my-8">
                    When will these vouchers expire?

                    <div class="mt-4">

                        <label class="italic">
                            Select expiry date
                            <input v-model="voucherSet.expires_at" :min="tomorrowsDate" class="rounded" type="date">
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

                <div class="title text-4xl my-4">
                    Type
                </div>

                <div class="mt-4">

                    <label class="">
                        Choose the type of the voucher set
                        <select v-model="voucherSet.voucher_set_type" class="ml-2">
                            <option :value="''">Select a type</option>
                            <option v-for="voucherType in $props.voucherSetTypes" :value="voucherType">
                                {{ voucherType }}
                            </option>
                        </select>
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

                    <SecondaryButton v-if="$props.voucherSetTypes.includes(voucherSet.voucher_set_type)"
                                     @click="stageNext()">
                        Next
                        <svg class="size-6" fill="none" stroke="currentColor" stroke-width="1.5"
                             viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path d="m8.25 4.5 7.5 7.5-7.5 7.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </SecondaryButton>
                </div>

            </div>

            <div v-if="stage === 7">

                <div class="title text-4xl my-4">
                    Review
                </div>

                <div class="my-8">
                    <div class="italic">
                        This will create a voucher set with the following details
                    </div>

                    <div class="my-8">
                        <ul class="list-disc space-y-6">
                            <li>
                                The voucher is <span class="font-bold">{{
                                    !voucherSet.is_test ? 'not a test' : 'a test'
                                }}</span>
                                <button class="text-xs text-blue-500 ml-2 underline" @click="stage = 1">Edit</button>
                            </li>

                            <li>
                                It has been allocated to service team: <span
                                class="font-bold">{{ voucherSet.allocated_to_service_team[0]?.name }}</span>
                                <button class="text-xs text-blue-500 ml-2 underline" @click="stage = 2">Edit</button>
                            </li>

                            <!--                            <li v-if="voucherSet.funded_by_team_id">-->
                            <!--                                It has been funded by: {{ fundingTeam.name }}-->
                            <!--                            </li>-->

                            <li>
                                It has a total value of:

                                <span class="font-bold">{{ voucherSet.total_set_value }}</span>
                                <button class="text-xs text-blue-500 ml-2 underline" @click="stage = 3">Edit</button>
                            </li>

                            <li>
                                Divided into denominations of:
                                <button class="text-xs text-blue-500 ml-2 underline" @click="stage = 4">Edit</button>

                                <ul>
                                    <li v-for="denomination in denominations" class="font-bold">
                                        {{ denomination.number }} {{ denomination.number === 1 ? 'unit' : 'units' }} of
                                        {{ denomination.value }} {{ $props.auth.teamCountry?.currency_code }}
                                    </li>
                                </ul>
                            </li>


                            <li>
                                The voucher <span class="font-bold">{{
                                    voucherSet.expires_at ? 'expires at: ' + voucherSet.expires_at : 'does not expire'
                                }}</span>
                                <button class="text-xs text-blue-500 ml-2 underline" @click="stage = 5">Edit</button>
                            </li>


                            <li>
                                Has a type of <span class="font-bold">{{ voucherSet.voucher_set_type }}</span>
                                <button class="text-xs text-blue-500 ml-2 underline" @click="stage = 6">Edit</button>
                            </li>

                        </ul>


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

                    <PrimaryButton @click="createVoucherSet()">
                        Generate!
                    </PrimaryButton>
                </div>

            </div>
        </div>
    </div>
</template>


