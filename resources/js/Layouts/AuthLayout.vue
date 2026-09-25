<script setup>
import { Link } from '@inertiajs/vue3'
</script>

<template>
  <div class="auth-layout-wrapper">
    <slot />
  </div>
</template>

<style scoped>
.auth-layout-wrapper {
  width: 100%;
  height: 100%;
  min-height: 100vh;
}
</style>
