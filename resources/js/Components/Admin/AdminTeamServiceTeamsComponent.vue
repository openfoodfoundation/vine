<script setup>
import {onMounted, ref} from "vue";
import AdminTeamDetailsComponent from "@/Components/Admin/AdminTeamDetailsComponent.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import Swal from "sweetalert2";
import AdminTeamSelectComponent from "@/Components/Admin/AdminTeamSelectComponent.vue";

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

function getServices() {
    axios.get('/admin/team-service-teams?cached=false&where[]=team_id,' + $props.teamId + '&relations=serviceTeam').then(response => {
        services.value = response.data.data
    }).catch(error => {
        console.log(error)
    })
}

function getServiceTeams() {
    axios.get('/admin/team-service-teams?cached=false&where[]=service_team_id,' + $props.teamId + '&relations=team').then(response => {
        serviceTeams.value = response.data.data
    }).catch(error => {
        console.log(error)
    })
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
    <div class="flex justify-end">
        <PrimaryButton @click="addNewService()" class="ms-4">
            Add Service Team
        </PrimaryButton>
    </div>

    <div v-if="addingNewService">
        <div class="py-2">Select service team...</div>
        <AdminTeamSelectComponent :excludeTeams="services" :teamId="teamId" @teamSelected="teamSelected"/>
    </div>

    <div v-else-if="creatingNewTeamService">
        <div class="py-2">Adding <span class="font-bold">{{ teamAddingAsService.name }}</span> as service team?</div>
        <PrimaryButton @click="submitTeamService()" class="ms-4">
            Add
        </PrimaryButton>
    </div>

    <div v-else>
        <div v-if="services.data && services.data.length" class="mb-2">
            <div class="mb-2 font-semibold">{{ teamName }}'s service teams</div>

            <div v-for="service in services.data" class="border-b py-1">
                <AdminTeamDetailsComponent :team="service.service_team"/>
            </div>
        </div>

        <div v-if="serviceTeams.data && serviceTeams.data.length" class="mb-2">
            <div class="mb-2 font-semibold">{{ teamName }} is service team for</div>

            <div v-for="serviceTeam in serviceTeams.data" class="border-b py-1">
                <AdminTeamDetailsComponent :team="serviceTeam.team"/>
            </div>
        </div>

        <div v-if="(services.data && services.data.length === 0) && (serviceTeams.data && serviceTeams.data.length === 0)">
            {{ teamName }} does not have service teams
        </div>
    </div>
</template>
