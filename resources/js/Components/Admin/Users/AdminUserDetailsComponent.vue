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
    <div>
        <div class="">
            <span class="text-xs text-gray-300">#{{ $props.user.id }}.</span>
            {{ $props.user.name }}
        </div>

        <div class="text-sm">{{ $props.user.email }}</div>
    </div>
</template>
