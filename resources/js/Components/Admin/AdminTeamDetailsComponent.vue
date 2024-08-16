<script setup>

const $props = defineProps({
    team: {
        required: true,
        type: Object,
    }
});

</script>

<template>
    <div>
        <div class="text-xs">#{{ $props.team.id }}</div>
        <div class="">{{ $props.team.name }}</div>
    </div>
</template>
