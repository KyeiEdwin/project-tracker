<script setup>
import { computed } from 'vue'

const props = defineProps({
  variant: {
    type: String,
    default: 'primary', // primary, secondary, light, success, danger, warning
    validator: (value) => ['primary', 'secondary', 'light', 'success', 'danger', 'warning', 'ghost'].includes(value)
  },
  size: {
    type: String,
    default: 'md', // sm, md, lg
    validator: (value) => ['sm', 'md', 'lg'].includes(value)
  },
  icon: {
    type: Boolean,
    default: false
  },
  loading: {
    type: Boolean,
    default: false
  },
  disabled: {
    type: Boolean,
    default: false
  },
  fullWidth: {
    type: Boolean,
    default: false
  }
})

const buttonClass = computed(() => {
  const classes = ['ti-btn']
  
  // Variant classes
  if (props.variant === 'primary') {
    classes.push('ti-btn-primary')
  } else if (props.variant === 'secondary') {
    classes.push('ti-btn-secondary')
  } else if (props.variant === 'light') {
    classes.push('ti-btn-light')
  } else if (props.variant === 'success') {
    classes.push('bg-green-600 text-white hover:bg-green-700 border-green-600')
  } else if (props.variant === 'danger') {
    classes.push('bg-red-500 text-white hover:bg-red-600 border-red-500')
  } else if (props.variant === 'warning') {
    classes.push('bg-yellow-500 text-white hover:bg-yellow-600 border-yellow-500')
  } else if (props.variant === 'ghost') {
    classes.push('bg-transparent hover:bg-gray-100 dark:hover:bg-gray-800 text-gray-700 dark:text-gray-300')
  }
  
  // Size classes
  if (props.size === 'sm') {
    classes.push('ti-btn-sm')
  } else if (props.size === 'lg') {
    classes.push('ti-btn-lg')
  }
  
  // Icon only
  if (props.icon) {
    classes.push('ti-btn-icon')
  }
  
  // Full width
  if (props.fullWidth) {
    classes.push('w-full')
  }
  
  return classes.join(' ')
})
</script>

<template>
  <button 
    :class="buttonClass" 
    :disabled="disabled || loading"
    type="button"
  >
    <i v-if="loading" class="ri-loader-4-line animate-spin"></i>
    <slot v-else />
  </button>
</template>

<style scoped>
@keyframes spin {
  from {
    transform: rotate(0deg);
  }
  to {
    transform: rotate(360deg);
  }
}

.animate-spin {
  animation: spin 1s linear infinite;
}
</style>
