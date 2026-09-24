<script setup>
defineProps({
  icon: {
    type: String,
    default: 'ri-inbox-line'
  },
  title: {
    type: String,
    default: 'No data available'
  },
  description: {
    type: String,
    default: ''
  },
  actionText: {
    type: String,
    default: ''
  }
})

const emit = defineEmits(['action'])
</script>

<template>
  <div class="flex flex-col items-center justify-center py-16 px-4 text-center">
    <!-- Icon -->
    <div class="w-20 h-20 rounded-3xl bg-gray-100 dark:bg-gray-800 flex items-center justify-center mb-6">
      <i :class="icon" class="text-4xl text-gray-400 dark:text-gray-600"></i>
    </div>
    
    <!-- Title -->
    <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-2">
      {{ title }}
    </h3>
    
    <!-- Description -->
    <p v-if="description" class="text-gray-600 dark:text-gray-400 max-w-md mb-6">
      {{ description }}
    </p>
    
    <!-- Slot for custom content -->
    <slot></slot>
    
    <!-- Action button -->
    <button 
      v-if="actionText"
      class="ti-btn ti-btn-primary mt-4"
      @click="emit('action')"
    >
      {{ actionText }}
    </button>
  </div>
</template>
