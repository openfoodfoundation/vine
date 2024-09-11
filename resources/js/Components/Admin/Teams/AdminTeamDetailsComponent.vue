<script setup>
import {Link} from "@inertiajs/vue3";


const $props = defineProps({
    team: {
        required: true,
        type: Object,
    }
});

</script>

<template>

    <div class="flex justify-start items-center">
        <div class="text-xs mr-2">#{{ $props.team.id }}</div>
        <div class="">{{ $props.team.name }}</div>
    </div>

</template>
