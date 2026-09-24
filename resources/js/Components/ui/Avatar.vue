<script setup>
import { computed } from 'vue'

const props = defineProps({
  src: {
    type: String,
    default: ''
  },
  alt: {
    type: String,
    default: 'Avatar'
  },
  name: {
    type: String,
    default: ''
  },
  size: {
    type: String,
    default: 'md', // xs, sm, md, lg, xl
    validator: (value) => ['xs', 'sm', 'md', 'lg', 'xl'].includes(value)
  },
  status: {
    type: String,
    default: '', // online, offline, away, busy
    validator: (value) => ['', 'online', 'offline', 'away', 'busy'].includes(value)
  },
  color: {
    type: String,
    default: 'bg-green-600'
  }
})

const initials = computed(() => {
  if (!props.name) return '?'
  const names = props.name.split(' ')
  if (names.length >= 2) {
    return `${names[0][0]}${names[1][0]}`.toUpperCase()
  }
  return props.name.substring(0, 2).toUpperCase()
})

const sizeClasses = computed(() => {
  const sizes = {
    xs: 'w-6 h-6 text-xs',
    sm: 'w-8 h-8 text-sm',
    md: 'w-10 h-10 text-base',
    lg: 'w-12 h-12 text-lg',
    xl: 'w-16 h-16 text-2xl'
  }
  return sizes[props.size] || sizes.md
})

const statusColor = computed(() => {
  const colors = {
    online: 'bg-green-500',
    offline: 'bg-gray-400',
    away: 'bg-yellow-500',
    busy: 'bg-red-500'
  }
  return colors[props.status] || ''
})

const statusSize = computed(() => {
  const sizes = {
    xs: 'w-1.5 h-1.5',
    sm: 'w-2 h-2',
    md: 'w-2.5 h-2.5',
    lg: 'w-3 h-3',
    xl: 'w-4 h-4'
  }
  return sizes[props.size] || sizes.md
})
</script>

<template>
  <div class="relative inline-block">
    <div 
      class="avatar rounded-full flex items-center justify-center overflow-hidden ring-2 ring-white dark:ring-gray-800 transition-transform hover:scale-105"
      :class="[sizeClasses, src ? '' : `${color} text-white font-semibold`]"
    >
      <img v-if="src" :src="src" :alt="alt" class="w-full h-full object-cover" />
      <span v-else>{{ initials }}</span>
    </div>
    
    <!-- Status indicator -->
    <span 
      v-if="status"
      class="absolute bottom-0 right-0 rounded-full border-2 border-white dark:border-gray-800"
      :class="[statusSize, statusColor]"
    ></span>
  </div>
</template>
