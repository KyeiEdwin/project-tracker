<script setup>
import { computed } from 'vue'

const props = defineProps({
  modelValue: {
    type: [String, Number],
    default: ''
  },
  type: {
    type: String,
    default: 'text'
  },
  label: {
    type: String,
    default: ''
  },
  placeholder: {
    type: String,
    default: ''
  },
  error: {
    type: String,
    default: ''
  },
  disabled: {
    type: Boolean,
    default: false
  },
  required: {
    type: Boolean,
    default: false
  },
  icon: {
    type: String,
    default: ''
  },
  hint: {
    type: String,
    default: ''
  }
})

const emit = defineEmits(['update:modelValue'])

const inputClass = computed(() => {
  const classes = [
    'w-full px-4 py-2.5 text-sm rounded-xl border transition-all duration-200',
    'focus:outline-none focus:ring-2',
    'disabled:opacity-50 disabled:cursor-not-allowed'
  ]
  
  if (props.error) {
    classes.push('border-red-300 focus:border-red-500 focus:ring-red-500/20')
  } else {
    classes.push(
      'border-gray-200 dark:border-gray-700',
      'focus:border-green-500 focus:ring-green-500/20',
      'dark:bg-gray-800 dark:text-gray-100'
    )
  }
  
  if (props.icon) {
    classes.push('pl-12')
  }
  
  return classes.join(' ')
})
</script>

<template>
  <div class="space-y-1.5">
    <label 
      v-if="label" 
      class="block text-sm font-medium text-gray-700 dark:text-gray-300"
    >
      {{ label }}
      <span v-if="required" class="text-red-500 ml-0.5">*</span>
    </label>
    
    <div class="relative">
      <i 
        v-if="icon" 
        :class="icon"
        class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-lg"
      ></i>
      
      <input
        :type="type"
        :value="modelValue"
        :placeholder="placeholder"
        :disabled="disabled"
        :required="required"
        :class="inputClass"
        @input="emit('update:modelValue', $event.target.value)"
      />
    </div>
    
    <p v-if="hint && !error" class="text-xs text-gray-500 dark:text-gray-400">
      {{ hint }}
    </p>
    
    <p v-if="error" class="text-xs text-red-600 dark:text-red-400 flex items-center gap-1">
      <i class="ri-error-warning-line"></i>
      {{ error }}
    </p>
  </div>
</template>
