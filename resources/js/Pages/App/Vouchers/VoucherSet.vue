<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head} from '@inertiajs/vue3';
import {onMounted, ref} from "vue";
import dayjs from "dayjs";
import relativeTime from "dayjs/plugin/relativeTime";
import utc from "dayjs/plugin/utc"

dayjs.extend(relativeTime);
dayjs.extend(utc);

const $props = defineProps({
    voucherSetId: {
        type: String,
        required: false,
    },
});

const voucherSet = ref({})

onMounted(() => {
    getVoucherSet()
});

function getVoucherSet() {
    axios.get('/my-team-voucher-sets/' + $props.voucherSetId + '?cached=false&relations=createdByTeam,allocatedToServiceTeam').then(response => {
        voucherSet.value = response.data.data
    }).catch(error => {
        console.log(error)
    })
}

</script>

<template>
    <Head title="Voucher Set"/>

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-normal text-xl text-gray-800 leading-tight">Voucher Set</h2>
        </template>

        <div class="card">
            {{ voucherSet }}
        </div>

        <div class="pb-32">

        </div>
    </AuthenticatedLayout>
</template>
