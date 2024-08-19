<script setup>
import {onMounted, ref} from "vue";
import TextInput from "@/Components/TextInput.vue";
import InputLabel from "@/Components/InputLabel.vue";
import AdminTeamDetailsComponent from "@/Components/Admin/AdminTeamDetailsComponent.vue";
import AdminTeamCreateComponent from "@/Components/Admin/AdminTeamCreateComponent.vue";

const $props = defineProps({
    excludeTeams: {
        required: false,
        default: {}
    },
    teamId: {
        required: false,
        default: null
    }
})

const creatingANewTeam = ref(false)
const excludeTeamIdsArray = ref([])
const searchStr = ref('')
const teams = ref({})

const emit = defineEmits([
        'teamSelected'
    ]
);

onMounted(() => {
    createExcludeTeamIdsArray()
})

function createExcludeTeamIdsArray() {
    if ($props.excludeTeams.data && $props.excludeTeams.data.length > 0) {
        $props.excludeTeams.data.forEach((team) => {
            if (team.service_team_id) {
                excludeTeamIdsArray.value.push(team.service_team_id)
            }
            if (team.merchant_team_id) {
                excludeTeamIdsArray.value.push(team.merchant_team_id)
            }
        })
    }

    if ($props.teamId) {
        excludeTeamIdsArray.value.push($props.teamId)
    }
}

function searchTeam() {
    axios.get('/admin/teams?where[]=name,like,*' + searchStr.value + '*&limit=100').then(response => {
        teams.value = response.data.data
    }).catch(error => {
        console.log(error)
    })
}

function startCreatingNewTeam() {
    creatingANewTeam.value = true
    teams.value = {}
}

function teamCreated(team) {
    teamSelected(team)
}

function teamSelected(team) {
    emit('teamSelected', team)
    searchStr.value = ''
    teams.value = {}
}

</script>

<template>
    <div v-if="creatingANewTeam">
        <AdminTeamCreateComponent :searchStr="searchStr" @teamCreated="teamCreated" />
    </div>

    <div v-else>
        <div>
            <InputLabel for="name" value="Team name(Type to search and press Enter)"/>
            <TextInput @keyup.enter.prevent="searchTeam()"
                       id="name"
                       type="text"
                       class="mt-1 block w-full"
                       v-model="searchStr"
                       required
            />
        </div>

        <div v-if="searchStr.length > 0 && teams.total > 0" class="mt-4">
            <div v-for="team in teams.data" class="border-b py-1">
                <button @click="teamSelected(team)" class="cursor-pointer flex justify-start items-end"
                        :class="{'text-gray-500 cursor-not-allowed': excludeTeamIdsArray.includes(team.id)}"
                :disabled="excludeTeamIdsArray.includes(team.id)">
                    <AdminTeamDetailsComponent :team="team"/>
                </button>
            </div>
            <div class="text-red-500 text-sm mt-4 cursor-pointer hover:underline" @click="startCreatingNewTeam()">
                Create a new team?
            </div>
        </div>

        <div v-if="searchStr.length > 0 && teams.total === 0">
            <div class="text-red-500 text-sm mt-4 cursor-pointer hover:underline" @click="startCreatingNewTeam()">
                We could not find teams. Do you want to create a new team?
            </div>
        </div>
    </div>
</template>
