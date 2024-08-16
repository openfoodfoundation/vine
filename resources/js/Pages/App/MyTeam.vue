<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head} from '@inertiajs/vue3';
import {onMounted, ref} from "vue";

const myTeam = ref({})

onMounted(() => {
    getMyTeam()
})

function getMyTeam() {
    axios.get('/my-team?cached=false&relations=teamUsers.user').then(response => {
        myTeam.value = response.data.data
    }).catch(error => {
        console.log(error)
    })
}

</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-normal text-xl text-gray-800 leading-tight">My Team</h2>
        </template>

        <div class="card">
            <div class="flex items-start font-bold">
                <div>#{{ myTeam.id }}</div>
                <div class="pl-2 text-2xl">{{ myTeam.name }}</div>
            </div>
        </div>

        <div class="card">
            <div class="text-sm pb-2 text-gray-500">Team members</div>

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
    </AuthenticatedLayout>
</template>
