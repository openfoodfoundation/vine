<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head, usePage} from '@inertiajs/vue3';
import AuditItemsComponent from "@/Components/AuditItemsComponent.vue";
import Swal from "sweetalert2";
import {onMounted, ref} from "vue";

const user = usePage().props.auth.user;
const isAdmin = ref(false);
const auditItems = ref({});

function getData() {

    let endpoint = '/my-team-audit-items?cache=false';

    if (isAdmin) {
        endpoint = '/admin/audit-items?cache=false&relations=team';
    }

    axios.get(endpoint).then(response => {

        auditItems.value = response.data.data;

    }).catch(error => {

        Swal.fire({
            icon: 'error',
            title: 'Oops!',
            text: error.response.data.message
        });
    });
}

onMounted(() => {
    if (user.is_admin && window.location.pathname === '/admin/audit-trail') {
        isAdmin.value = true;
    }

    getData();
})

</script>

<template>
    <Head title="Audit Items"/>

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-normal text-xl text-gray-800 leading-tight">Audit Items</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">


                    <div class="mb-8 p-4">
                        <div class="mb-4">
                            Filter to:
                        </div>
                        <div class="flex justify-start space-x-6">
                            <div class="hover:cursor-pointer hover:underline" @click="filterTo('')">User</div>
                            <div class="hover:cursor-pointer hover:underline" @click="filterTo('')">Team</div>
                            <div class="hover:cursor-pointer hover:underline" @click="filterTo('')">Voucher Set</div>
                            <div class="hover:cursor-pointer hover:underline" @click="filterTo('')">Voucher</div>
                            <div class="hover:cursor-pointer hover:underline" @click="filterTo('')">No Filter</div>
                        </div>
                    </div>


                    <AuditItemsComponent :audit-items="auditItems" :is-admin="isAdmin"/>

                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
