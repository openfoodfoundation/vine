<script setup>
import {Link} from '@inertiajs/vue3';

const $props = defineProps({
    user: {
        required: true,
        type: Object,
    }
});

</script>

<template>
    <div class="text-xs">#{{ $props.user.id }}</div>
    <div class="">{{ $props.user.name }}</div>
    <Link :href="route('admin.team', $props.user.current_team.id)" v-if="$props.user.current_team" class="hover:no-underline hover:opacity-75">
        {{ $props.user.current_team.name }}
    </Link>
    <div class="text-sm">{{ $props.user.email }}</div>
</template>
