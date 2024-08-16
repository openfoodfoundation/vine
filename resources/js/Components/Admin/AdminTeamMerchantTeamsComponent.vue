<script setup>
import {onMounted, ref} from "vue";
import AdminTeamDetailsComponent from "@/Components/Admin/AdminTeamDetailsComponent.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import AdminTeamSearchComponent from "@/Components/Admin/AdminTeamSearchComponent.vue";
import Swal from "sweetalert2";

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

function getMerchants() {
    axios.get('/admin/team-merchant-teams?cached=false&where[]=team_id,' + $props.teamId + '&relations=merchantTeam').then(response => {
        merchants.value = response.data.data
    }).catch(error => {
        console.log(error)
    })
}

function getMerchantTeams() {
    axios.get('/admin/team-merchant-teams?cached=false&where[]=merchant_team_id,' + $props.teamId + '&relations=team').then(response => {
        merchantTeams.value = response.data.data
    }).catch(error => {
        console.log(error)
    })
}

function submitTeamMerchant(text) {
    let payload = {
        team_id: $props.teamId,
        merchant_team_id: teamAddingAsMerchant.value.id
    }

    if (text === 'addedAsMerchant') {
        payload = {
            team_id: teamAddingAsMerchant.value.id,
            merchant_team_id: $props.teamId
        }
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
}

function teamSelected(team) {
    teamAddingAsMerchant.value = team
    addingNewMerchant.value = false
    creatingNewTeamMerchant.value = true
}

</script>

<template>
    <div class="flex justify-end">
        <PrimaryButton @click="addNewMerchant()" class="ms-4">
            Add Merchant Team
        </PrimaryButton>
    </div>

    <div v-if="addingNewMerchant">
        <div class="py-2">Select merchant team...</div>
        <AdminTeamSearchComponent @teamSelected="teamSelected" />
    </div>

    <div v-else-if="creatingNewTeamMerchant">
        <div class="flex items-center">
            <div class="pr-20 border-r">
                <div class="py-2">Adding <span class="font-bold">{{ teamAddingAsMerchant.name }}</span> as <span class="font-bold">{{ $props.teamName }}</span>'s merchant.</div>
                <PrimaryButton @click="submitTeamMerchant('getNewMerchant')" class="ms-4">
                    Add {{ teamAddingAsMerchant.name }} as merchant for {{ $props.teamName }}
                </PrimaryButton>
            </div>
            <div class="pl-20">
                <div class="py-2">Adding <span class="font-bold">{{ $props.teamName }}</span> as <span class="font-bold">{{ teamAddingAsMerchant.name }}</span>'s merchant.</div>
                <PrimaryButton @click="submitTeamMerchant('addedAsMerchant')" class="ms-4">
                    Add {{ $props.teamName }} as merchant for {{ teamAddingAsMerchant.name }}
                </PrimaryButton>
            </div>
        </div>
    </div>

    <div v-else>
        <div v-if="merchants.data && merchants.data.length" class="mb-2">
            <div class="mb-2 font-semibold">{{ teamName }}'s merchant teams</div>

            <div v-for="merchant in merchants.data" class="border-b py-1">
                <AdminTeamDetailsComponent :team="merchant.merchant_team" />
            </div>
        </div>

        <div v-if="merchantTeams.data && merchantTeams.data.length" class="mb-2">
            <div class="mb-2 font-semibold">{{ teamName }} is merchant team for</div>

            <div v-for="merchantTeam in merchantTeams.data" class="border-b py-1">
                <AdminTeamDetailsComponent :team="merchantTeam.team" />
            </div>
        </div>

        <div v-if="(merchants.data && merchants.data.length === 0) && (merchantTeams.data && merchantTeams.data.length === 0)">
            {{ teamName }} does not have merchant teams
        </div>
    </div>
</template>
