<script setup>

import {Link} from "@inertiajs/vue3";
</script>

<template>

    <h2>
        Admin Dashboard
    </h2>

    <div class="mt-4 lg:flex lg:justify-start lg:items-center lg:gap-x-4 grid grid-cols-3">
        <Link :href="route('admin.home')">
            Admin Home
        </Link>
        <Link :href="route('admin.users')">
            Users
        </Link>
        <Link :href="route('admin.teams')">
            Teams
        </Link>

        <Link :href="route('admin.teams')">
            Voucher Sets
        </Link>

        <Link :href="route('admin.teams')">
            Vouchers
        </Link>

        <Link :href="route('admin.teams')">
            Redemptions
        </Link>

        <Link :href="route('admin.teams')">
            API Access Tokens
        </Link>
    </div>
</template>

