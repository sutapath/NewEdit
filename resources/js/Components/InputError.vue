<template>
    <div v-if="message" class="text-red-600 dark:text-red-400">
        <p class="text-sm">{{ message }}</p>
    </div>
</template>

<script setup>
const props = defineProps({
    message: String
});
</script>
