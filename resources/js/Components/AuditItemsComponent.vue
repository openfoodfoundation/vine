<script setup>
import dayjs from "dayjs";
import Swal from "sweetalert2";
import {onMounted, ref} from "vue";
import {Link} from "@inertiajs/vue3";

const $props = defineProps(
    {
        isAdmin: {
            type: Boolean,
            default: false,
            required: false
        }
    }
)

const auditItems = ref({});

function getData() {

    let endpoint = '/my-team-audit-items?cache=false&orderBy=id,desc';

    if ($props.isAdmin) {
        endpoint = '/admin/audit-items?cache=false&relations=team&orderBy=id,desc';
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
    getData();
});


</script>

<template>

   <div class="card">
       <div class="card-header">
           Audit Trail
       </div>
       <div v-for="auditItem in auditItems.data">
           <Link class="hover:no-underline" :href="(isAdmin)? auditItem.admin_url : auditItem.dashboard_url">
               <div
                   class="flex justify-between items-center border-b border-gray-200 p-4">
                   <div>
                       <div>
                           {{ auditItem.auditable_text }}
                       </div>

                       <div class="text-xs text-gray-500 italic">
                           {{ dayjs(auditItem.created_at).format("dddd, MMMM Do YYYY [at] h:mm:ss a") }}
                       </div>
                   </div>

                   <div>
                       <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                           <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                       </svg>
                   </div>
               </div>
           </Link>
       </div>
   </div>

</template>
