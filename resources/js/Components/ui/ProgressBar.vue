<script setup>
import { computed } from 'vue'

const props = defineProps({
  value: {
    type: Number,
    required: true,
    validator: (value) => value >= 0 && value <= 100
  },
  showLabel: {
    type: Boolean,
    default: false
  },
  size: {
    type: String,
    default: 'md', // sm, md, lg
    validator: (value) => ['sm', 'md', 'lg'].includes(value)
  },
  variant: {
    type: String,
    default: 'primary', // primary, success, warning, danger
    validator: (value) => ['primary', 'success', 'warning', 'danger'].includes(value)
  }
})

const heightClass = computed(() => {
  if (props.size === 'sm') return 'h-1.5'
  if (props.size === 'lg') return 'h-3'
  return 'h-2'
})

const colorClass = computed(() => {
  if (props.variant === 'success') return 'bg-green-500'
  if (props.variant === 'warning') return 'bg-yellow-500'
  if (props.variant === 'danger') return 'bg-red-500'
  return 'bg-gradient-to-r from-green-600 to-emerald-500'
})
</script>

<template>
  <div>
    <div v-if="showLabel" class="flex justify-between items-center mb-2">
      <slot name="label"></slot>
      <span class="text-sm font-semibold text-gray-700 dark:text-gray-300">{{ value }}%</span>
    </div>
    <div class="progress-modern" :class="heightClass">
      <div 
        class="h-full rounded-full transition-all duration-500 ease-out shadow-sm"
        :class="colorClass"
        :style="{ width: `${value}%` }"
      ></div>
    </div>
  </div>
</template>
