<template>
  <tr class="border-b">
    <slot />
  </tr>
</template>

<style scoped>
.border-b {
  border-bottom: 1px solid #ddd; /* เส้นขอบด้านล่างของ row */
}
</style>
