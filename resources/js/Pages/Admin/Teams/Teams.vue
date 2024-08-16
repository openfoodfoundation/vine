<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import AdminTopNavigation from "@/Components/Admin/AdminTopNavigation.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import {onMounted, ref} from "vue";
import PaginatorComponent from "@/Components/Admin/PaginatorComponent.vue";

const limit = ref(20)
const teams = ref({})

onMounted(() => {
    getTeams()
})

function getTeams(page = 1) {
    axios.get('/admin/teams?cached=false&page=' + page + '&limit=' + limit.value + '&orderBy=id,desc').then(response => {
        teams.value = response.data.data
    }).catch(error => {
        console.log(error)
    })
}

function setDataPage(page) {
    getTeams(page);
}

</script>

<template>
    <Head title="Teams" />

    <AuthenticatedLayout>
        <template #header>
            <AdminTopNavigation></AdminTopNavigation>
        </template>

        <div class="card">
            <PrimaryButton>
                <Link :href="route('admin.teams.new')">
                    Create New Team
                </Link>
            </PrimaryButton>
        </div>

        <div class=" card">
            <div v-if="teams.data && teams.data.length">
                <Link :href="route('admin.team', team.id)" v-for="team in teams.data" class="hover:no-underline hover:opacity-75">
                    <div class="border-b flex justify-between items-center py-2 sm:p-2">
                        <div>

                            <div class="font-bold">
                                 <span class="text-xs opacity-25">
                                  #{{ team.id }}
                                </span>
                                {{ team.name }}
                            </div>
                        </div>
                        <div >
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
                        :pagination-data="teams"></PaginatorComponent>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
