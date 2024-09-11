<script setup>
import {ref} from "vue";
import TextInput from "@/Components/TextInput.vue";
import InputLabel from "@/Components/InputLabel.vue";
import AdminUserDetailsComponent from "@/Components/Admin/Users/AdminUserDetailsComponent.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";

const $props = defineProps({
    teamId: {
        required: true,
        type: Number,
    }
});


const creatingANewUser = ref(false)
const newUser = ref({name: '', email: '', current_team_id: null})
const searchStr = ref('')
const users = ref({})

const emit = defineEmits([
        'createNewTeamUser'
    ]
);

function createNewUser() {
    newUser.value.current_team_id = $props.teamId
    axios.post('admin/users', newUser.value).then(response => {
        let userId = response.data.data.id

        userSelected(userId)
        newUser.value = {name: '', email: '', current_team_id: null}
        creatingANewUser.value = false

    }).catch(error => {
        console.log(error)
    })
}

function searchUser() {
    axios.get('/admin/users?where[]=name,like,*' + searchStr.value + '*&limit=100').then(response => {
        users.value = response.data.data
    }).catch(error => {
        console.log(error)
    })
}

function startCreatingNewUser() {
    creatingANewUser.value = true
    users.value = {}
    newUser.value.name = searchStr.value
}

function userSelected(userId) {
    emit('createNewTeamUser', userId)
    searchStr.value = ''
    users.value = {}
}

</script>

<template>
    <div v-if="creatingANewUser">
        <div>
            <InputLabel for="name" value="Name"/>
            <TextInput
                id="name"
                type="text"
                class="mt-1 block w-full"
                v-model="newUser.name"
                required
            />
        </div>
        <div>
            <InputLabel for="email" value="Email"/>
            <TextInput
                id="email"
                type="email"
                class="mt-1 block w-full"
                v-model="newUser.email"
                required
            />
        </div>
        <div class="flex items-center justify-end mt-4">
            <PrimaryButton @click.prevent="createNewUser()" class="ms-4"
                           :class="{ 'opacity-25': !newUser.name || !newUser.email }"
                           :disabled="!newUser.name || !newUser.email">
                Submit
            </PrimaryButton>
        </div>
    </div>

    <div v-else>
        <div>
            <InputLabel for="name" value="Find A User"/>

            <TextInput
                @keyup="searchUser()"
                v-model="searchStr"
                class="mt-1 block w-full"
                placeholder="Search by name.."
                type="text"
            ></TextInput>
        </div>

        <div v-if="searchStr.length > 0 && users.total > 0" class="mt-4">

            <a href="#" @click="userSelected(user.id)"  class="border-b py-1" v-for="user in users.data" tabindex="0">

                <AdminUserDetailsComponent :user="user" />

            </a>

            <div class="text-red-500 text-sm mt-4 cursor-pointer hover:underline" @click="startCreatingNewUser()">
                Create a new user?
            </div>
        </div>

        <div v-if="searchStr.length > 0 && users.total === 0">
            <div class="text-red-500 text-sm mt-4 cursor-pointer hover:underline" @click="startCreatingNewUser()">
                We could not find users. Do you want to create a new user?
            </div>
        </div>
    </div>
</template>
