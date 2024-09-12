<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head} from '@inertiajs/vue3';
import {onMounted, ref} from "vue";
import MyTeamTopNavigation from "@/Components/MyTeamTopNavigation.vue";

const myVoucherSets = ref({})

onMounted(() => {
    getMyVoucherSets();
})

function getMyVoucherSets() {
    axios.get('/my-team-voucher-sets?cached=false').then(response => {
        myVoucherSets.value = response.data.data
    }).catch(error => {
        console.log(error)
    })
}


</script>

<template>
    <Head title="My Team Vouchers Sets"/>

    <AuthenticatedLayout>
        <template #header>
            <MyTeamTopNavigation/>
        </template>

        <div class="card">
            <div class="flex items-start font-bold">
                <div class="pl-2 text-2xl">My Team Voucher Sets</div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                Voucher Sets
            </div>

            <div v-if="myVoucherSets.data && myVoucherSets.data.length > 0">
                <div v-for="voucherSet in myVoucherSets.data" class="">
                    <a :href="'/voucher-set/' + voucherSet.id">
                        <div>{{ voucherSet.id }}</div>
                    </a>
                </div>
            </div>
        </div>

    </AuthenticatedLayout>
</template>
