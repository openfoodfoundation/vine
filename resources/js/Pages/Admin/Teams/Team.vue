<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head, Link} from '@inertiajs/vue3';
import AdminTopNavigation from "@/Components/Admin/AdminTopNavigation.vue";
import {onMounted, ref} from "vue";
import PaginatorComponent from "@/Components/Admin/PaginatorComponent.vue";
import Swal from "sweetalert2";
import AdminTeamMerchantTeamsComponent from "@/Components/Admin/TeamMerchantTeams/AdminTeamMerchantTeamsComponent.vue";
import AdminTeamDetailsComponent from "@/Components/Admin/AdminTeamDetailsComponent.vue";
import AdminTeamServiceTeamsComponent from "@/Components/Admin/TeamServiceTeams/AdminTeamServiceTeamsComponent.vue";
import AdminUserSelectComponent from "@/Components/Admin/AdminUserSelectComponent.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import AjaxLoadingIndicator from "@/Components/AjaxLoadingIndicator.vue";
import dayjs from "dayjs";
import relativeTime from "dayjs/plugin/relativeTime";
dayjs.extend(relativeTime);
import utc from "dayjs/plugin/utc"
dayjs.extend(utc);

const $props = defineProps({
    id: {
        required: true,
        type: Number,
    }
});

const limit = ref(5)
const team = ref({})
const teamUsers = ref({})
const invitingTeamUser = ref(false)

onMounted(() => {
    getTeam()
    getTeamUsers()
})

function createNewTeamUser(addingUserId) {
    let payload = {
        user_id: addingUserId,
        team_id: $props.id
    }

    axios.post('/admin/team-users', payload).then(response => {
        Swal.fire({
            title: 'Success!',
            icon: 'success',
            timer: 1000
        }).then(() => {
            getTeamUsers()
        })
    }).catch(error => {
        console.log(error)
    })
}

function getTeam() {
    axios.get('/admin/teams/' + $props.id + '?cached=false').then(response => {
        team.value = response.data.data
    }).catch(error => {
        console.log(error)
    })
}

function getTeamUsers(page = 1) {
    axios.get('/admin/team-users?cached=false&page=' + page + '&where[]=team_id,' + $props.id + '&relations=user&limit=' + limit.value + '&orderBy=id,desc').then(response => {
        teamUsers.value = response.data.data;
    }).catch(error => {
        console.log(error)
    })
}

function sendInvite(teamUser){
    invitingTeamUser.value = true;
    let payload = {
        send_invite_email: true
    };

    axios.put('/admin/team-users/'+teamUser.id, payload).then(response => {
        getTeamUsers();
        invitingTeamUser.value = false;
    }).catch(error => {
        console.log(error);
        invitingTeamUser.value = false;
    })
}

function setDataPage(page) {
    getTeamUsers(page);
}

</script>

<template>
    <Head title="Team"/>

    <AuthenticatedLayout>
        <template #header>
            <AdminTopNavigation></AdminTopNavigation>
        </template>

        <div class="card">
            <div class="">

                <h2>{{ team.name }}</h2>
                <div>#{{ $props.id }}</div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                Team details
            </div>

            <AdminTeamDetailsComponent :team="team"/>
        </div>

        <div class="card">
            <AjaxLoadingIndicator :loading="invitingTeamUser"></AjaxLoadingIndicator>
            <div class="card-header">
                Team members
            </div>

            <div v-if="teamUsers.data && teamUsers.data.length > 0">
                <div v-for="teamUser in teamUsers.data" class="flex  hover:opacity-75">
                    <Link :href="route('admin.user', teamUser.user_id)"
                          class="border-b p-2 mr-2 flex-grow flex justify-between items-center hover:no-underline">


                        <div>{{ teamUser.user?.name }}</div>
                        <div class="flex justify-end items-center">

                            <div v-if="teamUser.invitation_sent_at" class="pr-2 text-xs">
                                Invited: {{dayjs.utc(teamUser.invitation_sent_at).fromNow()}}
                            </div>

                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                            </svg>
                        </div>

                    </Link>
                    <SecondaryButton @click="sendInvite(teamUser)">
                        <div>
                            <div v-if="teamUser.invitation_sent_at">Resend Invite</div>
                            <div v-else-if="invitingTeamUser" class="px-2">Sending..</div>
                            <div v-else class="px-2">Send Invite</div>
                        </div>
                    </SecondaryButton>
                </div>


            </div>

            <div class="flex justify-end items-center mt-4">
                <div class="w-full lg:w-1/3">
                    <PaginatorComponent
                        @setDataPage="setDataPage"
                        :pagination-data="teamUsers"></PaginatorComponent>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                Add user to team
            </div>

            <AdminUserSelectComponent :teamId="$props.id" @createNewTeamUser="createNewTeamUser"/>
        </div>

        <div class="card">
            <div class="card-header">
                Merchant teams
            </div>

            <AdminTeamMerchantTeamsComponent :teamId="$props.id" :teamName="team.name"/>
        </div>

        <div class="card">
            <div class="card-header">
                Service teams
            </div>

            <AdminTeamServiceTeamsComponent :teamId="$props.id" :teamName="team.name"/>
        </div>

        <div class="card">
            <div class="text-sm pb-2 text-gray-500">Voucher sets created by team</div>
            - paginated
        </div>

        <div class="card">
            <div class="text-sm pb-2 text-gray-500">Voucher sets allocated to team</div>
            - paginated
        </div>

    </AuthenticatedLayout>
</template>
