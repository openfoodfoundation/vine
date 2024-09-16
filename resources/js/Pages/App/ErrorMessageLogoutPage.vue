<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head, Link} from '@inertiajs/vue3';
import PrimaryButton from "@/Components/PrimaryButton.vue";
import InputLabel from "@/Components/InputLabel.vue";
import Checkbox from "@/Components/Checkbox.vue";
import InputError from "@/Components/InputError.vue";
import GuestLayout from "@/Layouts/GuestLayout.vue";
import TextInput from "@/Components/TextInput.vue";

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
    <GuestLayout>
        <Head :title="$props.title"/>

        <div class="p-12 text-center">
            <div class="mb-8 font-bold">
                {{ $props.text }}
            </div>

            <Link :href="route('dashboard')">
                <PrimaryButton>Back to home</PrimaryButton>
            </Link>
        </div>
    </GuestLayout>
</template>
