<template>
  <div class="relative overflow-x-auto sm:rounded-xl shadow-md">
    <table class="w-full text-left text-gray-500 dark:text-gray-400">
      <thead class="athiti-regular text-white bg-gray-500 dark:bg-gray-700 dark:text-gray-400">
        <slot name="header"></slot>
      </thead>
      <tbody>
        <slot></slot>
      </tbody>
    </table>
  </div>
</template>

<style scoped>
 .athiti-regular {
	font-family: "Athiti", sans-serif;
	font-weight: 400;
	font-style: normal;
  }
</style>
