<script setup>
import { Link } from '@inertiajs/vue3'
</script>

<template>
  <div class="min-h-screen flex items-center justify-center px-4 py-12 bg-light">
    <div class="w-full max-w-md">
      <div class="text-center mb-6">
        <Link href="/login" class="text-xl font-semibold text-primary">Project Tracker</Link>
      </div>
      <div class="box shadow-sm">
        <div class="box-body p-6 sm:p-8">
          <slot />
        </div>
      </div>
    </div>
  </div>
</template>
