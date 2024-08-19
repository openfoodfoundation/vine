<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head, Link, usePage} from '@inertiajs/vue3';
import AdminTopNavigation from "@/Components/Admin/AdminTopNavigation.vue";
import {onMounted, ref} from "vue";
import Swal from "sweetalert2";
import dayjs from "dayjs";
import relativeTime from 'dayjs/plugin/relativeTime'
import localizedFormat from 'dayjs/plugin/localizedFormat'
import PrimaryButton from "@/Components/PrimaryButton.vue";

const $props = defineProps({
    id: {
        required: true,
        type: Number,
    }
});

const apiAccessToken = ref({})

onMounted(() => {
    getApiAccessToken()
})

function dateFormat(dateTime) {
    dayjs.extend(relativeTime)
    dayjs.extend(localizedFormat)

    return dayjs(dateTime).fromNow() + ' (' + dayjs(dateTime).format('LLL') + ')'
}

function getApiAccessToken() {
    axios.get('/admin/user-personal-access-tokens/' + $props.id + '?cached=false&relations=user').then(response => {
        apiAccessToken.value = response.data.data
    }).catch(error => {
        console.log(error)
    })
}

function revokeApiAccessToken() {
    Swal.fire({
        title: "Are you sure you want to delete this token?",
        text: "This action cannot be undone, and the user will no longer be able to use this token. Please confirm if you wish to proceed.",
        icon: "warning",
        confirmButtonColor: "#3085d6",
        confirmButtonText: "Revoke this token",
        showCancelButton: true,
    }).then((result) => {
        if (result.isConfirmed) {
            axios.delete('/admin/user-personal-access-tokens/' + $props.id).then(response => {
                window.location.href = route('admin.api-access-tokens')
            }).catch(error => {
                console.log(error)
            })
        }
    });
}

function textFormat(ability) {
    return ability.replaceAll('-', ' ')
}

</script>

<template>
    <Head title="API Access Token"/>

    <AuthenticatedLayout>
        <template #header>
            <AdminTopNavigation></AdminTopNavigation>
        </template>

        <div class="card">

            <h2>{{ apiAccessToken.name }}</h2>

        </div>

        <div class="card">
            <div class="card-header">
                API Access Token details
            </div>

            <div class="text-xs">#{{ apiAccessToken.id }}</div>
            <div class="">{{ apiAccessToken.name }}</div>
            <div v-if="apiAccessToken.last_used_at" class="mt-2">Last used at: {{ dateFormat(apiAccessToken.last_used_at) }}</div>
        </div>

        <div class="card">
            <div class="card-header">
                Issued to user
            </div>

            <div v-if="apiAccessToken.user">
                <Link :href="route('admin.user', apiAccessToken.tokenable_id)">
                    {{ apiAccessToken.user.name }}
                </Link>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                API Access Token abilities
            </div>

            <div v-if="apiAccessToken.abilities && apiAccessToken.abilities.length">
                <div v-for="ability in apiAccessToken.abilities" class="py-1">
                    <div class="list-item ml-8">
                        {{ textFormat(ability) }}
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <PrimaryButton @click="revokeApiAccessToken()">
                Revoke this token
            </PrimaryButton>
        </div>
    </AuthenticatedLayout>
</template>
