<script setup>
import moment from "moment";

const $props = defineProps(['auditItems', 'isAdmin'])

</script>

<template>

    <div v-for="auditItem in $props.auditItems.data"
         class="flex justify-between items-center border-b border-gray-200 p-4">
        <div class="space-x-3">
            <span class="font-bold">
                {{ auditItem.auditable_type.substring(auditItem.auditable_type.lastIndexOf("\\") + 1) }}
            </span>

            <a :href="'/admin/team/' + auditItem.team.id">
                <span v-if="isAdmin" class="text-sm text-gray-600 hover:underline hover:text-blue-500">
                    {{ auditItem.team.name }}
                </span>
            </a>

            <span class="text-sm text-gray-600">
                #{{ auditItem.auditable_id }}
            </span>
        </div>
        <div class="">
          <span :class="{
              'text-green-500': auditItem.auditable_text === 'created',
              'text-orange-500': auditItem.auditable_text === 'updated',
              'text-red-500': auditItem.auditable_text === 'deleted'
                }"
                class="capitalize font-bold"
          >
              {{ auditItem.auditable_text }}
          </span>
            on {{ moment(auditItem.updated_at).format("dddd, MMMM Do YYYY [at] h:mm:ss a") }}
        </div>
    </div>

</template>
