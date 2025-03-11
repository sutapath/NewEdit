<template>
    <div v-if="show" class="fixed inset-0 flex items-center justify-center bg-gray-500 bg-opacity-75">
      <div class="bg-white rounded-lg shadow-lg max-w-sm mx-auto p-6">
        <slot></slot>
        <button @click="$emit('close')" class="mt-4 px-4 py-2 bg-gray-300 rounded">
          Close
        </button>
      </div>
    </div>
  </template>
  
  <script setup>
  import { defineProps } from 'vue';
  
  const props = defineProps({
    show: Boolean
  });
  </script>
  