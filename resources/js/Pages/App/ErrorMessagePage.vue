<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head, Link} from '@inertiajs/vue3';
import PrimaryButton from "@/Components/PrimaryButton.vue";

const $props = defineProps({
    voucherSetId: {
        type: String,
        required: false,
    },
    voucherId: {
        type: String,
        required: false,
    },
    title: {
        type: String,
        required: true,
        default: ''
    },
    text: {
        type: String,
        required: true,
        default: ''
    }
});
</script>

<template>
    <Head :title="$props.title"/>

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-normal text-xl text-gray-800 leading-tight">{{ $props.title }}</h2>
        </template>

        <div class="card">
            <div class="mb-8">
                {{ $props.text }}
            </div>

            <Link :href="route('dashboard')">
                <PrimaryButton>Back to home</PrimaryButton>
            </Link>
        </div>

    </AuthenticatedLayout>
</template>
