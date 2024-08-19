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

function submitTeamMerchant() {
    let payload = {
        team_id: $props.teamId,
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
        <AdminTeamSelectComponent :excludeTeams="merchants" :teamId="teamId" @teamSelected="teamSelected"/>
    </div>

    <div v-else-if="creatingNewTeamMerchant">
        <div class="py-2">Add <span class="font-bold">{{ teamAddingAsMerchant.name }}</span> as merchant team?</div>
        <PrimaryButton @click="submitTeamMerchant()" class="">
            Add
        </PrimaryButton>

    </div>

    <div v-else>
        <div v-if="merchants.data && merchants.data.length" class="mb-8">
            <div>
                <div class="mb-2 font-semibold">
                    {{ teamName }}'s merchant teams
                </div>
                <div class="text-xs italic">
                    Teams that may redeem vouchers for {{ teamName }}
                </div>
            </div>

            <div v-for="merchant in merchants.data" class="border-b py-1">
                <AdminTeamDetailsComponent :team="merchant.merchant_team"/>
            </div>
        </div>

        <div v-if="merchantTeams.data && merchantTeams.data.length" class="mb-8">
            <div>
                <div class="mb-2 font-semibold">
                    {{ teamName }} is merchant team for
                </div>
                <div class="text-xs italic">
                    {{ teamName }} may redeem vouchers for these teams
                </div>
            </div>

            <div v-for="merchantTeam in merchantTeams.data" class="border-b py-1">
                <AdminTeamDetailsComponent :team="merchantTeam.team"/>
            </div>
        </div>

        <div v-if="(merchants.data && merchants.data.length === 0) && (merchantTeams.data && merchantTeams.data.length === 0)">
            {{ teamName }} does not have merchant teams
        </div>
    </div>
</template>
