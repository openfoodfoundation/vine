<script setup>

import {onMounted, ref, watch} from "vue";
import swal from "sweetalert2";
import {usePage} from "@inertiajs/vue3";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";

/**
 * Data
 */
const $props = usePage().props;

const canGenerateVoucherSet = ref(false);
const processStarted = ref(false);

// Funding team
const allFundingTeams = ref([]);
const filteredFundingTeams = ref([]);
const fundingTeamSearchQuery = ref('');
const selectedFundingTeam = ref('');

// Merchant teams
const allTeamMerchantTeams = ref([]);
const serviceTeamMerchantTeams = ref([]);
const merchantTeamSearchQuery = ref('');
const selectedMerchantTeams = ref([]);

// Service team
const allTeamServiceTeams = ref([]);
const filteredTeamServiceTeams = ref([]);
const selectedServiceTeam = ref('');
const serviceTeamSearchQuery = ref('');

// Voucher templates
const myTeamVoucherTemplates = ref([]);
const selectedVoucherTemplate = ref({});

// Voucher set
const voucherSet = ref({
    is_test: 0,
    allocated_to_service_team_id: '',
    merchant_team_ids: [],
    funded_by_team_id: '',
    voucher_template_id: '',
    total_set_value: 0,
    denominations: [
        {number: 1, value: 5},
    ],
    expires_at: '',
    voucher_set_type: '',
});


/**
 * Functions
 */
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
        }).then(() => {
            window.location.href = '/admin/voucher-set/' + response.data.data.id;
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
    voucherSet.value.denominations.push({value: 5, number: 1})
}

function denominationDelete(index) {
    if (index > -1) {
        voucherSet.value.denominations.splice(index, 1);
    }
}

function filterFundingTeams() {
    if (fundingTeamSearchQuery.value.length) {
        filteredFundingTeams.value = allFundingTeams.value.filter(team => team.name.toLowerCase().includes(fundingTeamSearchQuery.value));
    } else {
        filteredFundingTeams.value = allFundingTeams.value;
    }
}

function filterMerchantTeams() {
    if (merchantTeamSearchQuery.value.length) {
        // Filters by name via search query
        const filteredByName = allTeamMerchantTeams.value.filter(team => team.merchant_team?.name.toLowerCase().includes(merchantTeamSearchQuery.value));

        // Filters out the already selected teams
        serviceTeamMerchantTeams.value = filteredByName.filter(team => !selectedMerchantTeams.value.some(selectedTeam => team.merchant_team.id === selectedTeam.id))
    } else {
        serviceTeamMerchantTeams.value = allTeamMerchantTeams.value;
    }
}

function filterServiceTeams() {
    if (serviceTeamSearchQuery.value.length) {
        filteredTeamServiceTeams.value = allTeamServiceTeams.value.filter(team => team.service_team?.name.toLowerCase().includes(serviceTeamSearchQuery.value));
    } else {
        filteredTeamServiceTeams.value = allTeamServiceTeams.value;
    }
}

function getFundingTeams() {
    axios.get('/admin/teams').then(response => {
        allFundingTeams.value = response.data.data.data;
    }).catch(error => {
        swal.fire({
            icon: 'error',
            title: 'Oops!',
            text: error.response.data.meta.message,
        });
    });
}

function getMerchantTeamsForSelectedServiceTeam(selectedServiceTeam) {
    axios.get('/admin/team-merchant-teams?relations=merchantTeam&where[]=team_id,' + selectedServiceTeam.id).then(response => {
        allTeamMerchantTeams.value = response.data.data.data;
        serviceTeamMerchantTeams.value = response.data.data.data;
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
        filteredTeamServiceTeams.value = response.data.data.data;
    }).catch(error => {
        swal.fire({
            icon: 'error',
            title: 'Oops!',
            text: error.response.data.meta.message,
        });
    });
}

function getMyTeamVoucherTemplates() {
    axios.get('/admin/team-voucher-templates?cached=false&where[]=team_id,' + $props.auth.user.current_team_id + '&where[]=archived_at,null').then(response => {
        myTeamVoucherTemplates.value = response.data.data.data;
    }).catch(error => {
        swal.fire({
            icon: 'error',
            title: 'Oops!',
            text: error.response.data.meta.message,
        });
    });
}

function scrollTo(sectionId) {
    const section = document.getElementById(sectionId);
    if (section) {
        section.scrollIntoView({behavior: "smooth"})
    }
}

function selectFundingTeam(team) {
    selectedFundingTeam.value = team;
    voucherSet.value.funded_by_team_id = team.id;
    filteredFundingTeams.value = [];
    fundingTeamSearchQuery.value = '';
}

function selectMerchantTeam(team) {

    /**
     * TODO:
     * Open Food Network require that we can only select 1 merchant in the pilot.
     * When they come back and ask for more, remove the below line.
     */
    voucherSet.value.merchant_team_ids = [];
    if (!voucherSet.value.merchant_team_ids.includes(team.id)) {
        voucherSet.value.merchant_team_ids.push(team.id);

        // TODO: See above TODO, we'll need to remove this too
        selectedMerchantTeams.value = [];
        selectedMerchantTeams.value.push(team);
    }

}

function selectServiceTeam(team) {
    selectedServiceTeam.value = team;
    voucherSet.value.allocated_to_service_team_id = team.id;
    filteredTeamServiceTeams.value = [];
    serviceTeamSearchQuery.value = '';

    getMerchantTeamsForSelectedServiceTeam(team);
}

function selectVoucherTemplate(template) {
    if (voucherSet.value.voucher_template_id === template.id) {
        voucherSet.value.voucher_template_id = '';
        selectedVoucherTemplate.value = {};
    } else {
        voucherSet.value.voucher_template_id = template.id;
        selectedVoucherTemplate.value = template;
    }
}

function resetSelectedServiceTeam() {
    selectedServiceTeam.value = '';
    voucherSet.value.allocated_to_service_team_id = '';
    filteredTeamServiceTeams.value = Object.assign({}, allTeamServiceTeams.value);
    selectedMerchantTeams.value = [];
    allTeamMerchantTeams.value = [];
    voucherSet.value.merchant_team_ids = [];
}


function startProcess() {
    processStarted.value = true;
}

function totalDenominations() {
    let totalAssigned = 0;

    voucherSet.value.denominations.forEach(denomination => {
        totalAssigned += (denomination.number * denomination.value);
    });

    return totalAssigned;
}

function unselectMerchantTeam(index) {
    if (index > -1) {
        selectedMerchantTeams.value.splice(index, 1);
    }
}

/**
 * On mount
 */
onMounted(() => {
    getFundingTeams();
    getServiceTeams();
    getMyTeamVoucherTemplates();
})


/**
 * Watchers
 */
watch(selectedMerchantTeams, () => {
    filterMerchantTeams();
}, {deep: true})

watch(voucherSet, () => {

    canGenerateVoucherSet.value = (
        voucherSet.value.total_set_value > 0 &&
        voucherSet.value.merchant_team_ids.length > 0 &&
        voucherSet.value.voucher_template_id &&
        voucherSet.value.allocated_to_service_team_id &&
        voucherSet.value.voucher_set_type &&
        allocationRemaining() >= 0
    );

}, {deep: true})

watch(fundingTeamSearchQuery, () => {
    filterFundingTeams();
})

watch(merchantTeamSearchQuery, () => {
    filterMerchantTeams();
})

watch(serviceTeamSearchQuery, () => {
    filterServiceTeams();
})


</script>

<template>
    <div>

        <div class="py-8 space-y-8">

            <div class="card">
                <div class="card-header">
                    Create A Voucher Set
                </div>

                <div class="">
                    <div v-if="!processStarted">
                        <div class="my-8">
                            This process will bring you through creating a voucher set. There are 8 steps to complete.
                        </div>

                        <div class="mt-8">
                            The following icons are used:
                        </div>
                    </div>

                    <ul class="mt-6 italic space-y-4">
                        <li class="flex gap-x-1">
                            <svg
                                class="size-6 text-green-500 fill-green-100" fill="none" stroke="currentColor"
                                stroke-width="1.5" viewBox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg">
                                <path d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"
                                      stroke-linecap="round"
                                      stroke-linejoin="round"/>
                            </svg>

                            <span class="not-italic font-semibold">Complete</span> &hyphen; You have met the
                            requirements for this section
                        </li>

                        <li class="flex gap-x-1">
                            <svg
                                class="size-6 text-orange-500 fill-orange-100" fill="none" stroke="currentColor"
                                stroke-width="1.5" viewBox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"/>
                            </svg>

                            <span class="not-italic font-semibold">Warning</span> &hyphen; This section is not
                            required, so please double check the default answer
                        </li>

                        <li class="flex gap-x-1">
                            <svg
                                class="size-6 text-red-500 fill-red-100" fill="none" stroke="currentColor"
                                stroke-width="1.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 5.25h.008v.008H12v-.008Z"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"/>
                            </svg>

                            <span class="not-italic font-semibold">Incomplete</span> &hyphen; You have not met the
                            requirements for this section
                        </li>
                    </ul>


                    <div class="flex justify-start mt-8">
                        <SecondaryButton v-if="!processStarted" @click="startProcess()">
                            Get started
                        </SecondaryButton>
                    </div>
                </div>
            </div>

            <div v-if="processStarted" class="space-y-8">


                <div id="testSection" class="card">
                    <div class="card-header flex justify-between">
                        Is this a test voucher set?

                        <svg v-if="voucherSet.is_test"
                             class="size-6 text-green-500 fill-green-100" fill="none" stroke="currentColor"
                             stroke-width="1.5" viewBox="0 0 24 24"
                             xmlns="http://www.w3.org/2000/svg">
                            <path d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"
                                  stroke-linecap="round"
                                  stroke-linejoin="round"/>
                        </svg>

                        <svg v-else
                             class="size-6 text-orange-500 fill-orange-100" fill="none" stroke="currentColor"
                             stroke-width="1.5"
                             viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"
                                stroke-linecap="round"
                                stroke-linejoin="round"/>
                        </svg>

                    </div>

                    <div class="my-8">
                        A test voucher set is for when you want to test the process, but the
                        vouchers are not real.
                    </div>


                    <div class="my-8">
                        <label class="mb-2" for="voucherSet.is_test">
                            <input id="voucherSet.is_test" v-model="voucherSet.is_test" class="mr-2" type="checkbox">
                            YES -
                            this is a test voucher set.
                        </label>
                    </div>
                </div>

                <div id="serviceTeamSection" class="card">

                    <div class="card-header flex justify-between">
                        The service team

                        <svg v-if="selectedServiceTeam"
                             class="size-6 text-green-500 fill-green-100" fill="none" stroke="currentColor"
                             stroke-width="1.5"
                             viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"
                                  stroke-linecap="round"
                                  stroke-linejoin="round"/>
                        </svg>

                        <svg v-else
                             class="size-6 text-red-500 fill-red-100" fill="none" stroke="currentColor"
                             stroke-width="1.5"
                             viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 5.25h.008v.008H12v-.008Z"
                                stroke-linecap="round"
                                stroke-linejoin="round"/>
                        </svg>

                    </div>

                    <div class="my-8">
                        Which service team are you allocating this voucher set to?
                    </div>


                    <div v-if="!selectedServiceTeam">
                        <label for="service-team-search">
                            <input id="service-team-search" v-model="serviceTeamSearchQuery"
                                   class="block w-1/2 md:w-1/3 mt-1"
                                   placeholder="Filter your service teams.."
                                   type="search"/>
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

                        <span v-if="selectedServiceTeam"
                              class="text-red-500 text-xs underline hover:cursor-pointer ml-2"
                              @click="resetSelectedServiceTeam()">
                            Remove
                        </span>
                    </div>

                </div>


                <div id="merchantTeamSection" class="card">

                    <div class="card-header flex justify-between">
                        The merchant team

                        <svg v-if="selectedMerchantTeams.length"
                             class="size-6 text-green-500 fill-green-100" fill="none" stroke="currentColor"
                             stroke-width="1.5"
                             viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"
                                  stroke-linecap="round"
                                  stroke-linejoin="round"/>
                        </svg>

                        <svg v-else
                             class="size-6 text-red-500 fill-red-100" fill="none" stroke="currentColor"
                             stroke-width="1.5"
                             viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 5.25h.008v.008H12v-.008Z"
                                stroke-linecap="round"
                                stroke-linejoin="round"/>
                        </svg>
                    </div>

                    <div class="my-8">
                        Which merchant team(s) are you assigning this voucher set to?
                    </div>


                    <div>
                        <label for="service-team-search">

                            <input id="merchant-team-search" v-model="merchantTeamSearchQuery"
                                   class="block w-1/2 md:w-1/3 mt-1"
                                   placeholder="Filter merchant teams.."
                                   type="search"/>
                        </label>

                        <div class="my-4 flex flex-wrap gap-2">
                            <PrimaryButton v-for="teamMerchantTeam in serviceTeamMerchantTeams"
                                           @click="selectMerchantTeam(teamMerchantTeam.merchant_team)">
                                {{ teamMerchantTeam.merchant_team.name }}
                            </PrimaryButton>
                        </div>
                    </div>

                    <div v-if="selectedMerchantTeams.length" class="mt-8">
                        Selected merchant team(s):
                        <div>
                            <div v-for="(merchantTeam, i) in selectedMerchantTeams" class="mt-2">

                                {{ merchantTeam.name }}

                                <span class="text-red-500 text-xs underline hover:cursor-pointer ml-2"
                                      @click="unselectMerchantTeam(i)">
                                    Remove
                                </span>
                            </div>
                        </div>
                    </div>

                </div>


                <div id="fundingTeamSection" class="card">

                    <div class="card-header flex justify-between">
                        The funding team

                        <svg v-if="selectedFundingTeam"
                             class="size-6 text-green-500 fill-green-100" fill="none" stroke="currentColor"
                             stroke-width="1.5"
                             viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"
                                  stroke-linecap="round"
                                  stroke-linejoin="round"/>
                        </svg>


                        <svg v-else
                             class="size-6 text-orange-500 fill-orange-100" fill="none" stroke="currentColor"
                             stroke-width="1.5" viewBox="0 0 24 24"
                             xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"
                                stroke-linecap="round"
                                stroke-linejoin="round"/>
                        </svg>

                    </div>

                    <div class="my-8">
                        Is this voucher set funded by anyone? If so, please nominate the team here.
                    </div>


                    <div v-if="!selectedFundingTeam">
                        <label for="service-team-search">
                            Search for a funding team
                            <input id="merchant-team-search" v-model="fundingTeamSearchQuery"
                                   class="block w-1/2 md:w-1/3"
                                   type="search"/>
                        </label>


                        <div class="my-4 flex flex-wrap gap-2">
                            <PrimaryButton v-for="fundingTeam in filteredFundingTeams"
                                           @click="selectFundingTeam(fundingTeam)">
                                {{ fundingTeam.name }}
                            </PrimaryButton>
                        </div>
                    </div>

                    <div v-else>
                        Selected funding team: {{ selectedFundingTeam.name }}

                        <span v-if="selectedFundingTeam"
                              class="text-red-500 text-xs underline hover:cursor-pointer ml-2"
                              @click="selectedFundingTeam = ''">
                            Remove
                        </span>
                    </div>


                </div>


                <div id="totalValueSection" class="card">

                    <div class="card-header flex justify-between">
                        Total value

                        <svg v-if="voucherSet.total_set_value > 0"
                             class="size-6 text-green-500 fill-green-100" fill="none" stroke="currentColor"
                             stroke-width="1.5"
                             viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"
                                  stroke-linecap="round"
                                  stroke-linejoin="round"/>
                        </svg>

                        <svg v-else
                             class="size-6 text-red-500 fill-red-100" fill="none" stroke="currentColor"
                             stroke-width="1.5"
                             viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 5.25h.008v.008H12v-.008Z"
                                stroke-linecap="round"
                                stroke-linejoin="round"/>
                        </svg>

                    </div>

                    <div class="my-8">
                        <label for="voucherSet.total_set_value">
                            What will the total value of the voucher set be, in <span
                            class="font-bold">{{ $props.auth.teamCountry?.currency_code }}</span>?
                            <input id="voucherSet.total_set_value" v-model="voucherSet.total_set_value"
                                   class="block mt-2"
                                   type="number">
                        </label>
                    </div>

                </div>


                <div id="denominationsSection" class="card">

                    <div class="card-header flex justify-between">
                        Denominations

                        <svg v-if="allocationRemaining() === 0"
                             class="size-6 text-green-500 fill-green-100" fill="none" stroke="currentColor"
                             stroke-width="1.5"
                             viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"
                                  stroke-linecap="round"
                                  stroke-linejoin="round"/>
                        </svg>

                        <svg v-else-if="allocationRemaining() > 0"
                             class="size-6 text-orange-500 fill-orange-100" fill="none" stroke="currentColor"
                             stroke-width="1.5" viewBox="0 0 24 24"
                             xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"
                                stroke-linecap="round"
                                stroke-linejoin="round"/>
                        </svg>

                        <svg v-else
                             class="size-6 text-red-500 fill-red-100" fill="none" stroke="currentColor"
                             stroke-width="1.5"
                             viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 5.25h.008v.008H12v-.008Z"
                                stroke-linecap="round"
                                stroke-linejoin="round"/>
                        </svg>
                    </div>


                    <div class="flex justify-between items-center">
                        <div class="mt-4">
                            <div>
                                Available to assign: {{ voucherSet.total_set_value }}
                                {{ $props.auth.teamCountry?.currency_code }}
                            </div>
                            <div :class="{'text-red': allocationRemaining() < 0}">
                                Remaining: {{ allocationRemaining() }}
                            </div>

                            <div>
                                <div v-for="(denomination, index) in voucherSet.denominations"
                                     class="mb-2 flex justify-start items-center border-b py-4">


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
                                        <button v-if="index !== 0"
                                                class="text-red-500 text-xs underline hover:cursor-pointer ml-2"
                                                @click="denominationDelete(index)">
                                            <i class="fa fa-times"></i> Remove
                                        </button>

                                    </div>
                                </div>


                                <div class="pt-8">
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

                </div>


                <div id="expirationSection" class="card">

                    <div class="card-header flex justify-between">
                        Expiration

                        <svg v-if="voucherSet.expires_at"
                             class="size-6 text-green-500 fill-green-100" fill="none" stroke="currentColor"
                             stroke-width="1.5" viewBox="0 0 24 24"
                             xmlns="http://www.w3.org/2000/svg">
                            <path d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"
                                  stroke-linecap="round"
                                  stroke-linejoin="round"/>
                        </svg>

                        <svg v-else
                             class="size-6 text-orange-500 fill-orange-100" fill="none" stroke="currentColor"
                             stroke-width="1.5"
                             viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"
                                stroke-linecap="round"
                                stroke-linejoin="round"/>
                        </svg>
                    </div>


                    <div class="my-8">
                        When will these vouchers expire?

                        <div class="mt-4">

                            <label class="italic">
                                Select expiry date
                                <input v-model="voucherSet.expires_at" class="rounded" type="date">
                            </label>


                        </div>

                        <div class="mt-4">
                            <div v-if="voucherSet.expires_at" class="mt-8">
                                Selected expiry date: {{ voucherSet.expires_at }}

                                <span
                                    class="text-red-500 text-xs underline hover:cursor-pointer ml-2"
                                    @click="voucherSet.expires_at = ''">
                                    Remove
                                </span>
                            </div>

                            <div v-else class="text-xs italic">
                                Not selecting an expiry date will mean vouchers stay valid forever.
                            </div>
                        </div>

                    </div>

                </div>

                <div id="voucherTypeSection" class="card">

                    <div class="card-header flex justify-between">
                        Type

                        <svg v-if="voucherSet.voucher_set_type"
                             class="size-6 text-green-500 fill-green-100" fill="none" stroke="currentColor"
                             stroke-width="1.5"
                             viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"
                                  stroke-linecap="round"
                                  stroke-linejoin="round"/>
                        </svg>

                        <svg v-else
                             class="size-6 text-red-500 fill-red-100" fill="none" stroke="currentColor"
                             stroke-width="1.5"
                             viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 5.25h.008v.008H12v-.008Z"
                                stroke-linecap="round"
                                stroke-linejoin="round"/>
                        </svg>
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

                </div>

                <div id="voucherTemplateSection" class="card">

                    <div class="card-header flex justify-between">
                        Template

                        <svg v-if="voucherSet.voucher_template_id"
                             class="size-6 text-green-500 fill-green-100" fill="none" stroke="currentColor"
                             stroke-width="1.5" viewBox="0 0 24 24"
                             xmlns="http://www.w3.org/2000/svg">
                            <path d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"
                                  stroke-linecap="round"
                                  stroke-linejoin="round"/>
                        </svg>

                        <svg v-else
                             class="size-6 text-red-500 fill-red-100" fill="none" stroke="currentColor"
                             stroke-width="1.5"
                             viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 5.25h.008v.008H12v-.008Z"
                                stroke-linecap="round"
                                stroke-linejoin="round"/>
                        </svg>
                    </div>

                    <div class="grid gap-4 grid-cols-6 mt-8">
                        <div v-for="template in myTeamVoucherTemplates">
                            <div :class="{
                                'border-green-500': template.id === voucherSet.voucher_template_id,
                                'opacity-40': voucherSet.voucher_template_id && template.id !== voucherSet.voucher_template_id
                                }" class="hover:cursor-pointer border-2 rounded"
                                 @click="selectVoucherTemplate(template)">
                                <img :src="template.example_template_image_url" alt="" class="border rounded">
                            </div>

                            <div v-if="template.id === voucherSet.voucher_template_id"
                                 class="text-xs italic text-center mt-1 text-green-500">
                                Selected!
                            </div>

                        </div>
                    </div>


                </div>

            </div>

            <div class="card">

                <div class="card-header flex justify-between">
                    Review

                    <div class="">

                        <div v-if="canGenerateVoucherSet"
                             class="flex text-green-500 items-center">

                            You have completed all the required sections
                            <svg
                                class="size-6 text-green-500 fill-green-100" fill="none" stroke="currentColor"
                                stroke-width="1.5"
                                viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"
                                      stroke-linecap="round"
                                      stroke-linejoin="round"/>
                            </svg>
                        </div>


                        <div v-else class="flex text-red-500 items-center">
                            You have required sections that still need completing
                            <svg
                                class="size-6 fill-red-100 ml-2" fill="none" stroke="currentColor"
                                stroke-width="1.5"
                                viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 5.25h.008v.008H12v-.008Z"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"/>
                            </svg>
                        </div>

                    </div>
                </div>

                <div class="my-8">
                    <div>
                        Please review the following details for your voucher set.
                    </div>

                </div>


                <div class="my-8">
                    <ul class="list-disc space-y-6 pl-4">
                        <li>
                            The voucher set is <span class="font-bold">{{
                                !voucherSet.is_test ? 'not a test' : 'a test'
                            }}</span>

                            <button class="text-xs text-blue-500 ml-2 underline"
                                    @click="scrollTo('testSection')">
                                Edit
                            </button>
                        </li>

                        <li>
                            It has {{ selectedServiceTeam ? '' : ' not yet ' }} been allocated to service team
                            <span
                                v-if="selectedServiceTeam" class="font-bold">{{
                                    selectedServiceTeam.name
                                }}</span>

                            <button class="text-xs text-blue-500 ml-2 underline"
                                    @click="scrollTo('serviceTeamSection')">
                                Edit
                            </button>
                        </li>

                        <li>
                            It has {{ selectedMerchantTeams.length ? '' : ' not yet ' }} been assigned to a
                            merchant team(s)

                            <button class="text-xs text-blue-500 ml-2 underline"
                                    @click="scrollTo('merchantTeamSection')">
                                Edit
                            </button>

                            <div v-for="merchantTeam in selectedMerchantTeams" :key="merchantTeam.id"
                                 class="my-1 font-bold">
                                {{ merchantTeam.name }}
                            </div>

                        </li>

                        <li>
                            It has <span class="font-bold">{{ selectedFundingTeam ? '' : ' not ' }}</span> been
                            associated to funding team
                            <span
                                v-if="selectedFundingTeam" class="font-bold">{{
                                    selectedFundingTeam.name
                                }}</span>

                            <button class="text-xs text-blue-500 ml-2 underline"
                                    @click="scrollTo('fundingTeamSection')">
                                Edit
                            </button>
                        </li>


                        <li>
                            It has a total value of:

                            <span class="font-bold">{{ voucherSet.total_set_value }}</span>

                            <button class="text-xs text-blue-500 ml-2 underline"
                                    @click="scrollTo('totalValueSection')">
                                Edit
                            </button>
                        </li>

                        <li>
                            Divided into denominations of:

                            <button class="text-xs text-blue-500 ml-2 underline"
                                    @click="scrollTo('denominationsSection')">
                                Edit
                            </button>
                            <ul>
                                <li v-for="denomination in voucherSet.denominations" class="font-bold">
                                    {{ denomination.number }} {{ denomination.number === 1 ? 'unit' : 'units' }}
                                    of
                                    {{ denomination.value }} {{ $props.auth.teamCountry?.currency_code }}
                                </li>
                            </ul>
                        </li>


                        <li>
                            The voucher <span class="font-bold">{{
                                voucherSet.expires_at ? 'expires at ' + voucherSet.expires_at : 'does not expire'
                            }}</span>

                            <button class="text-xs text-blue-500 ml-2 underline"
                                    @click="scrollTo('expirationSection')">
                                Edit
                            </button>
                        </li>


                        <li class="">
                            <div v-if="voucherSet.voucher_set_type">
                                Has a type of <span class="font-bold">{{ voucherSet.voucher_set_type }}</span>

                                <button class="text-xs text-blue-500 ml-2 underline"
                                        @click="scrollTo('expirationSection')">
                                    Edit
                                </button>
                            </div>
                            <div v-else>
                                Does <span class="font-bold">not yet have a type</span>


                                <button class="text-xs text-blue-500 ml-2 underline"
                                        @click="scrollTo('expirationSection')">
                                    Edit
                                </button>
                            </div>
                        </li>


                        <li class="">
                            <div v-if="voucherSet.voucher_template_id">
                                Is using this voucher template

                                <button class="text-xs text-blue-500 ml-2 underline"
                                        @click="scrollTo('voucherTemplateSection')">
                                    Edit
                                </button>

                                <div class="mt-2">
                                    <img :src="selectedVoucherTemplate.example_template_image_url" alt=""
                                         class="border rounded max-h-48">
                                </div>
                            </div>
                            <div v-else>
                                Does <span class="font-bold">not yet have a template</span>


                                <button class="text-xs text-blue-500 ml-2 underline"
                                        @click="scrollTo('voucherTemplateSection')">
                                    Edit
                                </button>
                            </div>
                        </li>

                    </ul>


                </div>

                <div class="flex justify-end">
                    <PrimaryButton v-if="canGenerateVoucherSet"
                                   @click="createVoucherSet()">
                        Generate!
                    </PrimaryButton>
                </div>
            </div>

        </div>

    </div>
</template>


