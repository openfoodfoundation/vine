<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head, Link} from '@inertiajs/vue3';
import AdminTopNavigation from "@/Components/Admin/AdminTopNavigation.vue";
import {onMounted, ref} from "vue";
import PaginatorComponent from "@/Components/Admin/PaginatorComponent.vue";


const apiAccessTokens = ref({})

onMounted(() => {
    getApiAccessTokens()
})

function getApiAccessTokens(page = 1) {
    axios.get('/admin/user-personal-access-tokens?cached=false&page=' + page + '&relations=user&orderBy=id,desc').then(response => {
        apiAccessTokens.value = response.data.data
    }).catch(error => {
        console.log(error)
    })
}

</script>

<template>
    <Head title="API Access Tokens"/>

    <AuthenticatedLayout>
        <template #header>
            <AdminTopNavigation></AdminTopNavigation>
        </template>


        <div class=" card">
            <div v-if="apiAccessTokens.data && apiAccessTokens.data.length">
                <Link :href="route('admin.api-access-token', token.id)" v-for="token in apiAccessTokens.data" class="hover:no-underline hover:opacity-75">
                    <div class="border-b flex justify-between items-center py-2 sm:p-2">
                        <div>

                            <div class="font-bold">
                                <span class="text-xs opacity-25">
                                    #{{ token.id }}
                                </span>
                                {{ token.name }}
                            </div>
                            <div v-if="token.user" class="text-sm">
                                Issued to: {{ token.user.name }}
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
                        @setDataPage="getApiAccessTokens"
                        :pagination-data="apiAccessTokens"></PaginatorComponent>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
