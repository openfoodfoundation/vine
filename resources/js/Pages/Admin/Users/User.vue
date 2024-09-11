<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head, Link, usePage} from '@inertiajs/vue3';
import AdminTopNavigation from "@/Components/Admin/AdminTopNavigation.vue";
import {onMounted, ref} from "vue";
import Swal from "sweetalert2";
import PaginatorComponent from "@/Components/Admin/PaginatorComponent.vue";
import AdminUserDetailsComponent from "@/Components/Admin/Users/AdminUserDetailsComponent.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";

const $props = defineProps({
    id: {
        required: true,
        type: Number,
    }
});

const limit = ref(5)
const newPAT = ref({name: '', token_abilities: []})
const personalAccessTokenAbilities = usePage().props.personalAccessTokenAbilities;
const platformAppTokenAbilities = usePage().props.platformAppTokenAbilities;
const userTeams = ref({})
const user = ref({})

onMounted(() => {
    getUser()
    getUserTeams()
})

function createPAT() {
    newPAT.value.user_id = user.value.id
    axios.post('/admin/user-personal-access-tokens', newPAT.value).then(response => {
        let token = response.data.data.token
        let secret = response.data.data.secret

        Swal.fire({
            title: "Personal access token issued!",
            html: '<div>Please note that the token will be displayed only once. Make sure to save it securely.</div>' +
                '<div class="mt-4">Token: <b>' + token + '</b></div>' +
                '<div class="mt-4">Secret: <b>' + secret + '</b></div>' +
                '<div class="mt-4 text-xs">You will need the secret in order to sign your API requests.</div>',
            icon: "warning",
            confirmButtonColor: "#3085d6",
            confirmButtonText: "Got it!"
        }).then((result) => {
            newPAT.value = {name: '', token_abilities: []}
            getUser()
        });

    }).catch(error => {
        Swal.fire({
            icon: "error",
            title: "Oops..",
            text: error.response.data.meta.message
        })
    })
}

function getUser() {
    axios.get('/admin/users/' + $props.id + '?cached=false&relations=currentTeam').then(response => {
        user.value = response.data.data
    }).catch(error => {
        console.log(error)
    })
}

function getUserTeams(page = 1) {
    axios.get('/admin/team-users?cached=false&page=' + page + '&where[]=user_id,' + $props.id + '&relations=team&limit=' + limit.value + '&orderBy=id,desc').then(response => {
        userTeams.value = response.data.data;
    }).catch(error => {



    })
}

function setDataPage(page) {
    getUserTeams(page);
}

function textFormat(ability) {
    return ability.replaceAll('-', ' ')
}

function selectPlatformAppType(){
    newPAT.value.token_abilities = Object.keys(platformAppTokenAbilities);

    Swal.fire({
        icon: "info",
        title: "Platform Apps",
        html: '<div>We have selected the minimum required abilities for an API token for a "Platform" type app.</div>' +
            '<div class="mt-4">Be careful with these abilities, as they can perform additive and destructive actions, like creating teams, users and more API tokens.</div>'
    })
}

function clearAbilitiesList(){
    newPAT.value.token_abilities = [];
}

function updateAdminStatus() {
    let payload = {
        is_admin: user.value.is_admin
    }
    axios.put('/admin/users/' + $props.id, payload).then(response => {
        getUser()
    }).catch(error => {
        console.log(error)
    })
}


</script>

<template>
    <Head title="Users"/>

    <AuthenticatedLayout>
        <template #header>
            <AdminTopNavigation></AdminTopNavigation>
        </template>

        <div class="card">

            <div class="flex justify-between items-center">
                <h2>{{ user.name }}</h2>

                <div>
                    <div v-if="$page.props.isImpersonating === null">
                        <PrimaryButton><Link :href="route('admin.impersonate', $props.id)">Impersonate</Link></PrimaryButton>
                    </div>
                </div>
            </div>

        </div>

        <div class="card">
            <div class="card-header">
                User details
            </div>

            <AdminUserDetailsComponent :user="user"/>
        </div>

        <div class="card">
            <div class="card-header">
                User teams
            </div>

            <div v-if="userTeams.data && userTeams.data.length > 0">
                <Link :href="route('admin.team', userTeam.team_id)" v-for="userTeam in userTeams.data"
                      class="hover:no-underline hover:opacity-75">
                    <div :class="{'border-b p-2': userTeams.data.length > 1}">
                        <div v-if="userTeam.team">
                            <div v-if="userTeam.team_id === user.current_team_id" class="text-xs text-red-500">*Current
                                team
                            </div>
                            <div class="">{{ userTeam.team.name }}</div>
                        </div>
                    </div>
                </Link>
            </div>

            <div class="flex justify-end items-center mt-4">
                <div class="w-full lg:w-1/3">
                    <PaginatorComponent
                        @setDataPage="setDataPage"
                        :pagination-data="userTeams"></PaginatorComponent>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                Admin status
            </div>

            <label for="admin" class="cursor-pointer flex justify-start items-center">
                <input @change="updateAdminStatus()" type="checkbox" id="admin" class="mr-4"
                       :true-value="1" :false-value="0" v-model="user.is_admin"> User is System Admin
            </label>
        </div>

        <div class="card">
            <div class="card-header">
                User Personal Access Tokens (PATs)
            </div>

            <div v-if="user.tokens && user.tokens.length">
                <div v-for="userToken in user.tokens" class="border-b py-2">
                    <Link :href="route('admin.api-access-token', {id: userToken.id})">
                        <div class="list-item ml-8">
                            {{ userToken.name }}
                        </div>
                        <div v-if="userToken.abilities && userToken.abilities.length">
                            <div v-for="ability in userToken.abilities" class="ml-8 text-xs">
                                - {{ textFormat(ability) }}
                            </div>
                        </div>
                    </Link>
                </div>
            </div>
            <div v-else>User does not have PATs.</div>
        </div>

        <div class="card">
            <div class="card-header">
                Create Personal Access Token

            </div>

            <div v-if="personalAccessTokenAbilities.length">


                <div class="mt-8 mb-4">
                    <h2>Step 1: Select Token Abilities</h2>
                </div>

                <div class="flex justify-start items-center space-x-4 pb-4 ">
                    <SecondaryButton @click="clearAbilitiesList">
                        Clear Selected
                    </SecondaryButton>

                    <div class="pl-16">
                        Quick select:
                    </div>
                    <PrimaryButton @click="selectPlatformAppType()">
                        Platform App
                    </PrimaryButton>
                </div>



                <div class="grid grid-cols-1 md:grid-cols-2 md:gap-2 ">
                    <div v-for="abilityGroup in personalAccessTokenAbilities" class="border rounded-xl p-4">
                        <div>
                            <h2>
                                {{abilityGroup.name}}
                            </h2>
                            <div class="text-xs">
                                {{abilityGroup.description}}
                            </div>
                            <div class="mt-8">
                                <div v-for="(ability, key) in abilityGroup.abilities">
                                    <label :for="ability" class="cursor-pointer">

                                        <input type="checkbox" :id="ability" class="mr-4" :value="key"
                                               v-model="newPAT.token_abilities"> {{ textFormat(ability) }}
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-8">
                    <h2>Step 2: Give the Token a name</h2>
                </div>
                <div class="pb-4">
                    <TextInput
                        id="name"
                        type="text"
                        class="mt-1 block w-full"
                        v-model="newPAT.name"
                        required
                    />
                </div>
                <div>
                    Selected Abilities: {{newPAT.token_abilities.join(', ')}}
                </div>

                <div class="flex items-center justify-end mt-4">
                    <PrimaryButton @click.prevent="createPAT()" class="" :class="{ 'opacity-25': !newPAT.name }"
                                   :desabled="!newPAT.name">
                        Create New Token
                    </PrimaryButton>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
