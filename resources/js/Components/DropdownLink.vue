<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <Link
        class="block w-full px-4 ml-3 py-1 text-left text-sm leading-5 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-800 transition duration-150 ease-in-out"
    >
        <slot />
    </Link>
</template>
