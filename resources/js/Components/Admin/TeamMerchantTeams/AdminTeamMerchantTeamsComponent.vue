<script setup>
import {onMounted, ref} from "vue";
import {Link} from '@inertiajs/vue3';
import AdminTeamDetailsComponent from "@/Components/Admin/Teams/AdminTeamDetailsComponent.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import Swal from "sweetalert2";
import PaginatorComponent from "@/Components/Admin/PaginatorComponent.vue";
import AdminTeamMerchantTeamSelectComponent
    from "@/Components/Admin/TeamMerchantTeams/AdminTeamMerchantTeamSelectComponent.vue";

const $props = defineProps({
    team: {
        required: true,
        type: Object
    }
});

const addingNewMerchant = ref(false)
const creatingNewTeamMerchant = ref(false)
const merchants = ref({})
const merchantTeams = ref({})
const teamAddingAsMerchant = ref({})

onMounted(() => {
    getMerchants()
    getMerchantTeams()
})

function addNewMerchant() {
    addingNewMerchant.value = true
}

function cancelAddingNewMerchant() {
    addingNewMerchant.value = false
    creatingNewTeamMerchant.value = false
    teamAddingAsMerchant.value = {}
}

function getMerchants(page = 1) {
    axios.get('/admin/team-merchant-teams?cached=false&where[]=team_id,' + $props.team.id + '&page=' + page + '&relations=merchantTeam').then(response => {
        merchants.value = response.data.data
    }).catch(error => {
        console.log(error)
    })
}

function getMerchantTeams(page = 1) {
    axios.get('/admin/team-merchant-teams?cached=false&where[]=merchant_team_id,' + $props.team.id + '&page=' + page + '&relations=team').then(response => {
        merchantTeams.value = response.data.data
    }).catch(error => {
        console.log(error)
    })
}

function removeMerchantTeams(id) {
    Swal.fire({
        title: "Are you sure you want to delete?",
        text: "This action cannot be undone. Please confirm if you wish to proceed.",
        icon: "warning",
        confirmButtonColor: "#3085d6",
        confirmButtonText: "Delete merchant team",
        showCancelButton: true,
    }).then((result) => {
        if (result.isConfirmed) {
            axios.delete('/admin/team-merchant-teams/' + id).then(response => {
                Swal.fire({
                    title: 'Success!',
                    icon: 'success',
                    timer: 1000
                }).then(() => {
                    getMerchants()
                    getMerchantTeams()
                })
            }).catch(error => {
                console.log(error)
            })
        }
    });
}

function submitTeamMerchant() {
    if ($props.team.country_id === teamAddingAsMerchant.value.country_id) {

        let payload = {
            team_id: $props.team.id,
            merchant_team_id: teamAddingAsMerchant.value.id
        }

        axios.post('/admin/team-merchant-teams', payload).then(response => {
            Swal.fire({
                title: 'Success!',
                icon: 'success',
                timer: 1000
            }).then(() => {
                teamAddingAsMerchant.value = {}
                creatingNewTeamMerchant.value = false
                getMerchants()
                getMerchantTeams()
            })
        }).catch(error => {
            console.log(error)
        })

    } else {

        Swal.fire({
            title: "Country / Currency mismatch",
            html: "Selected merchant (" + teamAddingAsMerchant.value.name + ") is not sharing same country as the team (" + $props.team.name + "). We cannot add merchant which has different country/currency. Please update.",
            icon: "warning",
            confirmButtonColor: "#3085d6",
            confirmButtonText: "Got it!"
        }).then((result) => {
            teamAddingAsMerchant.value = {}
            creatingNewTeamMerchant.value = false
        });

    }
}

function teamSelected(team) {
    teamAddingAsMerchant.value = team
    addingNewMerchant.value = false
    creatingNewTeamMerchant.value = true
}

</script>

<template>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-0 md:gap-4">
        <div class="card">
            <div class="card-header flex justify-between items-center">

                <div>
                    <div>
                        Merchant teams
                    </div>
                    <div class="text-xs italic">
                        These teams may redeem vouchers for {{ team.name }}
                    </div>
                </div>

                <div class="">
                    <div class="flex justify-end">
                        <div v-if="!addingNewMerchant && !creatingNewTeamMerchant">
                            <PrimaryButton @click="addNewMerchant()" class="ms-4">
                                Add Merchant Team
                            </PrimaryButton>
                        </div>
                        <div v-else>
                            <PrimaryButton @click="cancelAddingNewMerchant()" class="ms-4">
                                Cancel
                            </PrimaryButton>
                        </div>
                    </div>
                </div>
            </div>


            <div v-if="addingNewMerchant">
                <div class="py-2">Select merchant team...</div>
                <AdminTeamMerchantTeamSelectComponent :teamId="team.id"
                                                      @teamSelected="teamSelected"></AdminTeamMerchantTeamSelectComponent>
            </div>

            <div v-else-if="creatingNewTeamMerchant">
                <div class="py-2">Add <span class="font-bold">{{ teamAddingAsMerchant.name }}</span> as merchant team?
                </div>
                <PrimaryButton @click="submitTeamMerchant()" class="">
                    Add
                </PrimaryButton>
            </div>
            <div v-else>
                <div v-if="merchants.data && merchants.data.length" class="mb-8">

                    <div v-for="merchant in merchants.data" class="border-b py-1 flex justify-between items-end">
                        <Link :href="route('admin.team', merchant.merchant_team_id)">
                            <AdminTeamDetailsComponent :team="merchant.merchant_team"/>
                        </Link>
                        <button @click="removeMerchantTeams(merchant.id)"
                                class="text-xs text-red-500 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                 stroke="currentColor" stroke-width="1.5" class="size-3">
                                <line x1="18" y1="6" x2="6" y2="18"></line>
                                <line x1="6" y1="6" x2="18" y2="18"></line>
                            </svg>
                            Delete
                        </button>
                    </div>
                    <div class="flex justify-end items-center mt-4">
                        <div class="w-full lg:w-1/3">
                            <PaginatorComponent
                                @setDataPage="getMerchants"
                                :pagination-data="merchants"></PaginatorComponent>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <div class="card">

            <div class="card-header">
                <div v-if="team.name">
                    Teams {{ team.name }} is merchant for
                </div>
                <div class="text-xs italic">
                    {{ team.name }} may redeem vouchers for these teams
                </div>
            </div>

            <div v-if="merchantTeams.data && merchantTeams.data.length" class="mb-8">

                <div v-for="merchantTeam in merchantTeams.data" class="border-b py-1 flex justify-between items-end">
                    <Link :href="route('admin.team', merchantTeam.team_id)">
                        <AdminTeamDetailsComponent :team="merchantTeam.team"/>
                    </Link>
                    <button @click="removeMerchantTeams(merchantTeam.id)"
                            class="text-xs text-red-500 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                             stroke-width="1.5" class="size-3">
                            <line x1="18" y1="6" x2="6" y2="18"></line>
                            <line x1="6" y1="6" x2="18" y2="18"></line>
                        </svg>
                        Delete
                    </button>
                </div>
                <div class="flex justify-end items-center mt-4">
                    <div class="w-full lg:w-1/3">
                        <PaginatorComponent
                            @setDataPage="getMerchantTeams"
                            :pagination-data="merchantTeams"></PaginatorComponent>
                    </div>
                </div>
            </div>

        </div>

    </div>


</template>
