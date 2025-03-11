<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';
</script>

<template>
       <div class="min-w-screen min-h-screen  bg-gray-100 flex items-center justify-center px-5 py-5">
            <slot />
        </div>
</template>
