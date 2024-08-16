<script setup>
import moment from "moment";

const $props = defineProps(['auditItems', 'isAdmin'])

</script>

<template>

    <div v-for="auditItem in $props.auditItems.data"
         class="flex justify-between items-center border-b border-gray-200 p-4">
        <div class="">
             <span :class="{
              'text-green-500': auditItem.auditable_text.includes('created'),
              'text-orange-500': auditItem.auditable_text.includes('updated'),
              'text-red-500': auditItem.auditable_text.includes('deleted')
                }"
                   class="capitalize font-bold"
             >
              {{ auditItem.auditable_text }}
          </span>

            <span class="ml-6">
                {{ auditItem.auditable_type.substring(auditItem.auditable_type.lastIndexOf("\\") + 1) }}
            </span>


            <span class="text-sm text-gray-600 ml-2">
                # {{ auditItem.auditable_id }}
            </span>

            <a v-if="isAdmin && auditItem.team?.id" :href="'/admin/team/' + auditItem.team.id">
                <span class="text-sm text-gray-600 hover:underline hover:text-blue-500">
                    {{ auditItem.team.name }}
                </span>
            </a>
        </div>

        <div class="">


            {{ moment(auditItem.updated_at).format("dddd, MMMM Do YYYY [at] h:mm:ss a") }}
        </div>
    </div>

</template>
