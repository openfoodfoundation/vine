<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head, Link} from '@inertiajs/vue3';
import AdminTopNavigation from "@/Components/Admin/AdminTopNavigation.vue";
import {onMounted, ref} from "vue";
import PaginatorComponent from "@/Components/Admin/PaginatorComponent.vue";
import Swal from "sweetalert2";
import AdminTeamMerchantTeamsComponent from "@/Components/Admin/TeamMerchantTeams/AdminTeamMerchantTeamsComponent.vue";
import AdminTeamServiceTeamsComponent from "@/Components/Admin/TeamServiceTeams/AdminTeamServiceTeamsComponent.vue";
import AdminUserSelectComponent from "@/Components/Admin/Users/AdminUserSelectComponent.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import AjaxLoadingIndicator from "@/Components/AjaxLoadingIndicator.vue";
import dayjs from "dayjs";
import relativeTime from "dayjs/plugin/relativeTime";
import utc from "dayjs/plugin/utc"
import TextInput from "@/Components/TextInput.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import DangerButton from "@/Components/DangerButton.vue";
import VoucherSetsComponent from "@/Components/Admin/Vouchers/VoucherSetsComponent.vue";
import VouchersComponent from "@/Components/Admin/Vouchers/VouchersComponent.vue";
import AdminTeamVoucherTemplatesList from "@/Components/Admin/Teams/AdminTeamVoucherTemplatesList.vue";

dayjs.extend(relativeTime);
dayjs.extend(utc);

const $props = defineProps({
    id: {
        required: true,
        type: Number,
    }
});

const invitingTeamUser = ref(false)
const limit = ref(10)
const newTeamName = ref('')
const selectedCountryId = ref('')
const countries = ref({})
const team = ref({
    name: '',
    country_id: ''

})
const teamUsers = ref({})

onMounted(() => {
    getTeam()
    getTeamUsers()
    getCountries()
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

function getCountries() {
    axios.get('/countries?limit=300').then(response => {
        countries.value = response.data.data;
    }).catch(error => {
        console.log(error)
    })
}

function getTeam() {
    axios.get('/admin/teams/' + $props.id + '?cached=false').then(response => {
        team.value = response.data.data

        selectedCountryId.value = team.value.country_id

        newTeamName.value = team.value.name
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

function sendInvite(teamUser) {
    invitingTeamUser.value = true;
    let payload = {
        send_invite_email: true
    };

    axios.put('/admin/team-users/' + teamUser.id, payload).then(response => {
        getTeamUsers();
        invitingTeamUser.value = false;
    }).catch(error => {
        console.log(error);
        invitingTeamUser.value = false;
    })
}

function confirmTeamUserDeletion(teamUser) {

    Swal.fire({
        icon: 'warning',
        title: 'Are you sure?',
        text: 'This will remove this user from this team. You can always add them back.',
        showConfirmButton: true,
        showCancelButton: true,
    }).then(response => {
        if (response.isConfirmed) {
            axios.delete('/admin/team-users/' + teamUser.id).then(response => {
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
    });


}

function setDataPage(page) {
    getTeamUsers(page);
}

function updateTeam() {


    let payload = {
        name: newTeamName.value,
        country_id: selectedCountryId.value
    }

    if (team.value.country_id !== selectedCountryId.value) {
        Swal.fire({
            title: 'Wait...',
            icon: 'warning',
            text: 'It looks like you\'re changing this teams\' country. Please be aware this will NOT update the selected currency for any of their existing voucher sets.',
            confirmButtonText: 'I get it. Proceed.',
            cancelButtonText: 'Go back',
            showCancelButton: true,
            showConfirmButton: true,
            allowOutsideClick: false
        }).then(result => {

            if (result.isDismissed) {
                return;
            }

            if (result.isConfirmed) {
                axios.put('/admin/teams/' + $props.id, payload).then(response => {
                    Swal.fire({
                        title: 'Success!',
                        icon: 'success',
                        timer: 1000
                    }).then(() => {
                        getTeam()
                    })
                }).catch(error => {
                    console.log(error)
                })
            }

        })
    }
    else {
        axios.put('/admin/teams/' + $props.id, payload).then(response => {
            Swal.fire({
                title: 'Success!',
                icon: 'success',
                timer: 1000
            }).then(() => {
                getTeam()
            })
        }).catch(error => {
            console.log(error)
        })
    }


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

                <h2>
                    <span class="text-gray-300 pr-1">#{{ $props.id }}</span>
                    {{ team.name }}
                </h2>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                Team details
            </div>

            <div class="flex justify-start items-center mt-4">
                <label for="name" class="w-full font-bold">
                    Team Name:
                    <TextInput
                        id="name"
                        type="text"
                        class="mt-1 block w-full font-normal"
                        v-model="newTeamName"/>
                </label>
            </div>

            <div class="flex justify-start items-center mt-4">
                <label for="country" class="w-full font-bold">
                    Country:
                    <select id="country" class="mt-1 block w-full font-normal" v-model="selectedCountryId">
                        <option v-for="country in countries.data" :value="country.id" :key="country.id">
                            {{ country.name }}
                        </option>
                    </select>
                </label>
            </div>
            <div v-if="newTeamName !== team.name || selectedCountryId !== team.country_id" class="mt-8 flex justify-end">
                <primary-button @click="updateTeam()">Update</primary-button>
            </div>
        </div>

        <div class="card">
            <AjaxLoadingIndicator :loading="invitingTeamUser"></AjaxLoadingIndicator>
            <div class="card-header">
                Team members
            </div>

            <div v-if="teamUsers.data && teamUsers.data.length > 0">
                <div v-for="teamUser in teamUsers.data" class="flex justify-between items-center  hover:opacity-75">
                    <Link :href="route('admin.user', teamUser.user_id)"
                          class="border-b p-2 mr-2 flex-grow flex justify-between items-center hover:no-underline">


                        <div>{{ teamUser.user?.name }}</div>
                        <div class="flex justify-end items-center">

                            <div v-if="teamUser.invitation_sent_at" class="pr-2 text-xs">
                                Invited: {{ dayjs.utc(teamUser.invitation_sent_at).fromNow() }}
                            </div>

                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                 stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/>
                            </svg>
                        </div>

                    </Link>
                    <div class="flex">
                        <SecondaryButton @click="sendInvite(teamUser)" class="mr-2">
                            <div>
                                <div v-if="teamUser.invitation_sent_at">Resend Invite</div>
                                <div v-else-if="invitingTeamUser" class="px-2">Sending..</div>
                                <div v-else class="px-2">Send Invite</div>
                            </div>
                        </SecondaryButton>
                        <DangerButton @click.prevent="confirmTeamUserDeletion(teamUser)">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                 stroke="currentColor" class="h-3 font-bold">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/>
                            </svg>
                        </DangerButton>
                    </div>
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


        <div class="container mx-auto">
            <AdminTeamMerchantTeamsComponent :team="team"/>
        </div>


        <div class="container mx-auto">
            <AdminTeamServiceTeamsComponent :teamId="$props.id" :teamName="team.name"/>
        </div>

        <div class="card">
            <div class="card-header">
                Voucher sets created by team
            </div>
            <VoucherSetsComponent :team-id="$props.id" filter-voucher-sets="created_by_team_id"></VoucherSetsComponent>
        </div>

        <div class="card">
            <div class="card-header">
                Voucher sets allocated to team
            </div>
            <VoucherSetsComponent :team-id="$props.id" filter-voucher-sets="allocated_to_service_team_id"></VoucherSetsComponent>
        </div>

        <div class="card">
            <div class="card-header">
                Vouchers created by team
            </div>
            <VouchersComponent :team-id="$props.id" filter-vouchers="created_by_team_id"></VouchersComponent>
        </div>

        <div class="card">
            <div class="card-header">
                Vouchers allocated to team
            </div>
            <VouchersComponent :team-id="$props.id" filter-vouchers="allocated_to_service_team_id"></VouchersComponent>
        </div>

        <div class="container mx-auto" v-if="team.id">
            <AdminTeamVoucherTemplatesList :team="team"></AdminTeamVoucherTemplatesList>
        </div>


        <div class="p-32"></div>

    </AuthenticatedLayout>
</template>
