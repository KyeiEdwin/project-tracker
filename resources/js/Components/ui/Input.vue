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
    'w-full text-sm rounded-xl border transition-all duration-200',
    'focus:outline-none focus:ring-2',
    'disabled:opacity-50 disabled:cursor-not-allowed'
  ]
  
  // Enhanced padding for better visual spacing
  if (props.icon) {
    classes.push('pl-12 pr-4 py-3')
  } else {
    classes.push('px-4 py-3')
  }
  
  if (props.error) {
    classes.push('border-red-300 focus:border-red-500 focus:ring-red-500/20')
  } else {
    classes.push(
      'border-gray-200 dark:border-gray-700',
      'focus:border-green-500 focus:ring-green-500/20',
      'dark:bg-gray-800 dark:text-gray-100'
    )
  }
  
  return classes.join(' ')
})

const iconClass = computed(() => {
  return [
    'absolute left-4 top-1/2 -translate-y-1/2',
    'text-gray-400 dark:text-gray-500',
    'text-lg pointer-events-none'
  ].join(' ')
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
        :class="[icon, iconClass]"
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

<style scoped>
/* Enhanced input placeholder styling */
input::placeholder {
  color: #9ca3af;
  opacity: 1;
}

.dark input::placeholder {
  color: #6b7280;
  opacity: 1;
}

/* Smooth transitions for focus states */
input:focus {
  transform: translateY(-1px);
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
}

.dark input:focus {
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.2), 0 2px 4px -1px rgba(0, 0, 0, 0.1);
}
</style>
