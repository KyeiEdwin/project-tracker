<script setup>
import { computed } from 'vue'

const props = defineProps({
  variant: {
    type: String,
    default: 'success', // success, warning, danger, info, primary, secondary
    validator: (value) => ['success', 'warning', 'danger', 'info', 'primary', 'secondary', 'neutral'].includes(value)
  },
  size: {
    type: String,
    default: 'md', // sm, md, lg
    validator: (value) => ['sm', 'md', 'lg'].includes(value)
  },
  dot: {
    type: Boolean,
    default: false
  }
})

const badgeClass = computed(() => {
  const classes = ['badge-modern']
  
  // Variant classes
  if (props.variant === 'success') {
    classes.push('badge-success')
  } else if (props.variant === 'warning') {
    classes.push('badge-warning')
  } else if (props.variant === 'danger') {
    classes.push('badge-danger')
  } else if (props.variant === 'info') {
    classes.push('badge-info')
  } else if (props.variant === 'primary') {
    classes.push('bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300')
  } else if (props.variant === 'secondary') {
    classes.push('bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300')
  } else if (props.variant === 'neutral') {
    classes.push('bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400')
  }
  
  // Size classes
  if (props.size === 'sm') {
    classes.push('text-xs px-2 py-1')
  } else if (props.size === 'lg') {
    classes.push('text-sm px-4 py-2')
  }
  
  return classes.join(' ')
})
</script>

<template>
  <span :class="badgeClass">
    <span v-if="dot" class="inline-block w-1.5 h-1.5 rounded-full bg-current mr-1.5"></span>
    <slot />
  </span>
</template>
