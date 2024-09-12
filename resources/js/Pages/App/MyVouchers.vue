<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head} from '@inertiajs/vue3';
import {onMounted, ref} from "vue";
import MyTeamTopNavigation from "@/Components/MyTeamTopNavigation.vue";

const myVouchers = ref({})

onMounted(() => {
    getMyVouchers();
})

function getMyVouchers() {
    axios.get('/my-team-vouchers?cached=false').then(response => {
        myVouchers.value = response.data.data
    }).catch(error => {
        console.log(error)
    })
}


</script>

<template>
    <Head title="My Team Vouchers"/>

    <AuthenticatedLayout>
        <template #header>
            <MyTeamTopNavigation/>
        </template>

        <div class="card">
            <div class="flex items-start font-bold">
                <div class="pl-2 text-2xl">My Team Vouchers</div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                Vouchers
            </div>

            <div v-if="myVouchers.data && myVouchers.data.length > 0">
                <div v-for="voucher in myVouchers.data" class="">
                    <a :href="'/voucher/' + voucher.id">
                        <div>{{ voucher.id }}</div>
                    </a>
                </div>
            </div>
        </div>

    </AuthenticatedLayout>
</template>
