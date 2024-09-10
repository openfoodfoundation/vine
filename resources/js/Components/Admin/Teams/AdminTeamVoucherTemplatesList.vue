<script setup>

import {Link} from "@inertiajs/vue3";
import {ref} from "vue";
import swal from "sweetalert2";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import PaginatorComponent from "@/Components/Admin/PaginatorComponent.vue";

const $props = defineProps({
    team: {
        type: Object,
        required: true
    }
});

const teamVoucherTemplates = ref({})


function getData(page = 1) {

    axios.get('/admin/team-voucher-templates?cached=false&where[]=team_id,' + $props.team?.id + '&page=' + page).then(response => {
        teamVoucherTemplates.value = response.data.data;
    }).catch(error => {
        swal.fire({
            icon: 'error',
            title: 'Oops!',
            text: error.response.data.message,
        });
    });

}

getData();


</script>

<template>

    <div class="card">

        <div class="card-header flex justify-between">
            <div>
                Voucher Templates
            </div>
            <div>
                <Link :href="'/admin/team-voucher-templates/new?teamId='+team.id">
                    <PrimaryButton class="">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        New Template
                    </PrimaryButton>
                </Link>
            </div>
        </div>

        <div class="grid gap-4 grid-cols-6 mt-8">
            <div v-for="template in teamVoucherTemplates.data">
                <div v-if="template.archived_at">
                    <div class="border-2 text-center rounded-lg border-dashed p-2 border-red-300 text-red-300 font-bold">
                        Archived
                    </div>
                </div>
                <div v-else>
                    <div class="border-2 text-center rounded-lg border-dashed p-2 border-green-300 text-green-300 font-bold">
                        Active
                    </div>
                </div>
                <Link :href="'/admin/team-voucher-template/' + template.id">
                    <img :src="template.example_template_image_url" alt="" class="border rounded">
                </Link>

            </div>
        </div>

        <div class="flex justify-end items-center mt-4">
            <div class="w-full lg:w-1/3">
                <PaginatorComponent
                    @setDataPage="getData"
                    :pagination-data="teamVoucherTemplates"></PaginatorComponent>
            </div>
        </div>
    </div>

</template>

