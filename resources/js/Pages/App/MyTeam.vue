<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head} from '@inertiajs/vue3';
import {onMounted, ref} from "vue";
import MyTeamTopNavigation from "@/Components/MyTeamTopNavigation.vue";
import MyTeamVouchersComponent from "@/Components/App/MyTeamVouchersComponent.vue";
import MyTeamVoucherSetsComponent from "@/Components/App/MyTeamVoucherSetsComponent.vue";

const myTeam = ref({})
const myTeams = ref({})

onMounted(() => {
    getMyTeam();
    getMyTeams();
})

function getMyTeam() {
    axios.get('/my-team?cached=false&relations=teamUsers.user').then(response => {
        myTeam.value = response.data.data
    }).catch(error => {
        console.log(error)
    })
}

function getMyTeams() {
    axios.get('/my-teams?cached=false&orderBy=name,asc').then(response => {
        myTeams.value = response.data.data
    }).catch(error => {
        console.log(error)
    })
}

</script>

<template>
    <Head title="Dashboard"/>

    <AuthenticatedLayout>
        <template #header>
            <MyTeamTopNavigation />
        </template>

        <div class="card">
            <div class="flex items-start font-bold">
                <div class="pl-2 text-2xl">{{ myTeam.name }}</div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                Team members
            </div>

            <div v-if="myTeam.team_users && myTeam.team_users.length > 0">
                <div v-for="teamUser in myTeam.team_users" class="">
                    <div :class="{'border-b p-2': myTeam.team_users.length > 1}">
                        <div v-if="teamUser.user" class="flex items-center">
                            <div>{{ teamUser.user.name }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                Teams You Belong To
            </div>

            <div v-for="team in myTeams.data" class="">
                <div class="border-b py-2 flex justify-between">

                    <div>
                        {{ team.name }}
                    </div>
                    <div>
                        <div v-if="team.id === $page.props.auth.user.current_team_id">
                            Current
                        </div>
                        <div v-else>
                            <a :href="'/switch-team/' + team.id" class="text-red-500">Switch to this team</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                Voucher sets Created by my team
            </div>

            <MyTeamVoucherSetsComponent filter-voucher-sets="-created"></MyTeamVoucherSetsComponent>
        </div>

        <div class="card">
            <div class="card-header">
                Voucher sets allocated to my team
            </div>

            <MyTeamVoucherSetsComponent filter-voucher-sets="-allocated"></MyTeamVoucherSetsComponent>
        </div>

        <div class="card">
            <div class="card-header">
                Vouchers Created by my team
            </div>
            <MyTeamVouchersComponent filter-vouchers="-created"></MyTeamVouchersComponent>
        </div>

        <div class="card">
            <div class="card-header">
                Vouchers allocated to my team
            </div>

            <MyTeamVouchersComponent filter-vouchers="-allocated"></MyTeamVouchersComponent>
        </div>

        <div class="pb-32">

        </div>

    </AuthenticatedLayout>
</template>
