<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head, Link} from '@inertiajs/vue3';
import AdminTopNavigation from "@/Components/Admin/AdminTopNavigation.vue";
import {onMounted, ref} from "vue";
import PaginatorComponent from "@/Components/Admin/PaginatorComponent.vue";


const users = ref({})

onMounted(() => {
    getUsers()
})

function getUsers(page = 1) {
    axios.get('/admin/users?cached=false&page=' + page + '&relations=currentTeam&orderBy=id,desc').then(response => {
        users.value = response.data.data
    }).catch(error => {
        console.log(error)
    })
}

function setDataPage(page) {
    getUsers(page);
}

</script>

<template>
    <Head title="Users"/>

    <AuthenticatedLayout>
        <template #header>
            <AdminTopNavigation></AdminTopNavigation>
        </template>


        <div class=" card">
            <div v-if="users.data && users.data.length">
                <Link :href="route('admin.user', user.id)" v-for="user in users.data" class="hover:no-underline hover:opacity-75">
                    <div class="border-b flex justify-between items-center py-2 sm:p-2">
                        <div>

                            <div class="font-bold">
                                <span class="text-xs opacity-25">
                                    #{{ user.id }}
                                </span>
                                {{ user.name }}
                            </div>
                            <div v-if="user.current_team" class="">
                                {{ user.current_team.name }}
                            </div>
                            <div class="text-sm">
                                {{ user.email }}
                            </div>
                        </div>
                        <div class="text-2xl">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                            </svg>

                        </div>
                    </div>
                </Link>
            </div>

            <div class="flex justify-end items-center mt-4">
                <div class="w-full lg:w-1/3">
                    <PaginatorComponent
                        @setDataPage="setDataPage"
                        :pagination-data="users"></PaginatorComponent>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
