<template>
  <th class="px-6 py-4 athiti-regular   border text-gray-900">
    <slot />
  </th>
</template>

<style scoped>
 .athiti-regular {
	font-family: "Athiti", sans-serif;
	font-weight: 400;
	font-style: normal;
  }

.border {
  border: 1px solid #ddd; /* เพิ่มเส้นขอบให้กับ td */
}
</style>
