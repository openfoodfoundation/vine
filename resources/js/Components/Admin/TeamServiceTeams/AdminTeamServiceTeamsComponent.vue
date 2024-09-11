<script setup>
import {onMounted, ref} from "vue";
import {Link} from '@inertiajs/vue3';
import AdminTeamDetailsComponent from "@/Components/Admin/Teams/AdminTeamDetailsComponent.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import Swal from "sweetalert2";
import PaginatorComponent from "@/Components/Admin/PaginatorComponent.vue";
import AdminTeamServiceTeamSelectComponent
    from "@/Components/Admin/TeamServiceTeams/AdminTeamServiceTeamSelectComponent.vue";
import AdminTeamMerchantTeamSelectComponent
    from "@/Components/Admin/TeamMerchantTeams/AdminTeamMerchantTeamSelectComponent.vue";

const $props = defineProps({
    teamId: {
        required: true,
        type: Number,
    },
    teamName: {
        required: true,
        // type: String
    }
});

const addingNewService = ref(false)
const creatingNewTeamService = ref(false)
const services = ref({})
const serviceTeams = ref({})
const teamAddingAsService = ref({})

onMounted(() => {
    getServices()
    getServiceTeams()
})

function addNewService() {
    addingNewService.value = true
}

function cancelAddingNewService() {
    addingNewService.value = false
    creatingNewTeamService.value = false
    teamAddingAsService.value = {}
}

function getServices(page = 1) {
    axios.get('/admin/team-service-teams?cached=false&where[]=team_id,' + $props.teamId + '&page=' + page + '&relations=serviceTeam').then(response => {
        services.value = response.data.data
    }).catch(error => {
        console.log(error)
    })
}

function getServiceTeams(page = 1) {
    axios.get('/admin/team-service-teams?cached=false&where[]=service_team_id,' + $props.teamId + '&page=' + page + '&relations=team').then(response => {
        serviceTeams.value = response.data.data
    }).catch(error => {
        console.log(error)
    })
}

function removeServiceTeams(id) {
    Swal.fire({
        title: "Are you sure you want to delete?",
        text: "This action cannot be undone. Please confirm if you wish to proceed.",
        icon: "warning",
        confirmButtonColor: "#3085d6",
        confirmButtonText: "Delete service team",
        showCancelButton: true,
    }).then((result) => {
        if (result.isConfirmed) {
            axios.delete('/admin/team-service-teams/' + id).then(response => {
                Swal.fire({
                    title: 'Success!',
                    icon: 'success',
                    timer: 1000
                }).then(() => {
                    getServices()
                    getServiceTeams()
                })
            }).catch(error => {
                console.log(error)
            })
        }
    });
}

function submitTeamService() {
    let payload = {
        team_id: $props.teamId,
        service_team_id: teamAddingAsService.value.id
    }

    axios.post('/admin/team-service-teams', payload).then(response => {
        Swal.fire({
            title: 'Success!',
            icon: 'success',
            timer: 1000
        }).then(() => {
            teamAddingAsService.value = {}
            creatingNewTeamService.value = false
            getServices()
            getServiceTeams()
        })
    }).catch(error => {
        console.log(error)
    })
}

function teamSelected(team) {
    teamAddingAsService.value = team
    addingNewService.value = false
    creatingNewTeamService.value = true
}

</script>

<template>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-0 md:gap-4">
        <div class="card">
            <div class="card-header flex justify-between items-center">

                <div>
                    <div>
                        Service teams
                    </div>
                    <div class="text-xs italic">
                        These teams may distribute vouchers for redemption at {{ teamName }}
                    </div>
                </div>

                <div class="">
                    <div class="flex justify-end">
                        <div class="flex justify-end">
                            <div v-if="!addingNewService && !creatingNewTeamService">
                                <PrimaryButton @click="addNewService()" class="ms-4">
                                    Add Service Team
                                </PrimaryButton>
                            </div>
                            <div v-else>
                                <PrimaryButton @click="cancelAddingNewService()" class="ms-4">
                                    Cancel
                                </PrimaryButton>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <div v-if="addingNewService">
                <div class="py-2">Select service team...</div>
                <AdminTeamServiceTeamSelectComponent :teamId="teamId"  @teamSelected="teamSelected"></AdminTeamServiceTeamSelectComponent>
            </div>

            <div v-else-if="creatingNewTeamService">
                <div class="py-2">Adding <span class="font-bold">{{ teamAddingAsService.name }}</span> as service team?</div>
                <PrimaryButton @click="submitTeamService()" class="">
                    Add
                </PrimaryButton>
            </div>
            <div v-else>
                <div v-if="services.data && services.data.length" class="mb-8">

                    <div v-for="service in services.data" class="border-b py-1 flex justify-between items-end">
                        <Link :href="route('admin.team', service.service_team_id)">
                            <AdminTeamDetailsComponent :team="service.service_team"/>
                        </Link>
                        <button @click="removeServiceTeams(service.id)" class="text-xs text-red-500 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"  class="size-3">
                                <line x1="18" y1="6" x2="6" y2="18"></line>
                                <line x1="6" y1="6" x2="18" y2="18"></line>
                            </svg>
                            Delete
                        </button>
                    </div>
                    <div class="flex justify-end items-center mt-4">
                        <div class="w-full lg:w-1/3">
                            <PaginatorComponent
                                @setDataPage="getServices"
                                :pagination-data="services"></PaginatorComponent>
                        </div>
                    </div>
                </div>



            </div>


        </div>

        <div class="card">

            <div class="card-header">
                <div>
                    Teams {{ teamName }} is service for
                </div>
                <div class="text-xs italic">
                    {{ teamName }} may distribute vouchers to these teams
                </div>
            </div>

            <div v-if="serviceTeams.data && serviceTeams.data.length" class="mb-8">


                <div v-for="serviceTeam in serviceTeams.data" class="border-b py-1 flex justify-between items-end">
                    <Link :href="route('admin.team', serviceTeam.team_id)">
                        <AdminTeamDetailsComponent :team="serviceTeam.team"/>
                    </Link>
                    <button @click="removeServiceTeams(serviceTeam.id)" class="text-xs text-red-500 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"  class="size-3">
                            <line x1="18" y1="6" x2="6" y2="18"></line>
                            <line x1="6" y1="6" x2="18" y2="18"></line>
                        </svg>
                        Delete
                    </button>
                </div>
                <div class="flex justify-end items-center mt-4">
                    <div class="w-full lg:w-1/3">
                        <PaginatorComponent
                            @setDataPage="getServiceTeams"
                            :pagination-data="serviceTeams"></PaginatorComponent>
                    </div>
                </div>
            </div>

        </div>

    </div>







</template>
