<script setup>
import { computed } from 'vue'

const props = defineProps({
  title: {
    type: String,
    required: true
  },
  value: {
    type: [String, Number],
    required: true
  },
  icon: {
    type: String,
    required: true
  },
  trend: {
    type: String,
    default: '' // 'up', 'down'
  },
  trendValue: {
    type: String,
    default: ''
  },
  color: {
    type: String,
    default: 'green', // green, blue, purple, orange, red
    validator: (value) => ['green', 'blue', 'purple', 'orange', 'red', 'emerald'].includes(value)
  },
  description: {
    type: String,
    default: ''
  }
})

const colorClasses = computed(() => {
  const colors = {
    green: {
      bg: 'bg-gradient-to-br from-green-500 to-green-600',
      icon: 'bg-green-400/30',
      text: 'text-green-50'
    },
    emerald: {
      bg: 'bg-gradient-to-br from-emerald-500 to-emerald-600',
      icon: 'bg-emerald-400/30',
      text: 'text-emerald-50'
    },
    blue: {
      bg: 'bg-gradient-to-br from-blue-500 to-blue-600',
      icon: 'bg-blue-400/30',
      text: 'text-blue-50'
    },
    purple: {
      bg: 'bg-gradient-to-br from-purple-500 to-purple-600',
      icon: 'bg-purple-400/30',
      text: 'text-purple-50'
    },
    orange: {
      bg: 'bg-gradient-to-br from-orange-500 to-orange-600',
      icon: 'bg-orange-400/30',
      text: 'text-orange-50'
    },
    red: {
      bg: 'bg-gradient-to-br from-red-500 to-red-600',
      icon: 'bg-red-400/30',
      text: 'text-red-50'
    }
  }
  return colors[props.color] || colors.green
})

const trendIcon = computed(() => {
  return props.trend === 'up' ? 'ri-arrow-up-line' : 'ri-arrow-down-line'
})

const trendColor = computed(() => {
  return props.trend === 'up' ? 'text-green-200' : 'text-red-200'
})
</script>

<template>
  <div 
    class="relative overflow-hidden rounded-3xl p-6 shadow-lg transition-all duration-300 hover:shadow-xl hover:-translate-y-1 cursor-pointer"
    :class="colorClasses.bg"
  >
    <!-- Background decoration -->
    <div class="absolute top-0 right-0 w-32 h-32 opacity-10">
      <div class="absolute inset-0 rounded-full bg-white blur-2xl"></div>
    </div>
    
    <!-- Content -->
    <div class="relative">
      <!-- Icon -->
      <div 
        class="inline-flex items-center justify-center w-14 h-14 rounded-2xl mb-4 backdrop-blur-sm"
        :class="colorClasses.icon"
      >
        <i :class="icon" class="text-3xl text-white"></i>
      </div>
      
      <!-- Value and Title -->
      <div class="space-y-1 mb-3">
        <h3 class="text-4xl font-bold tracking-tight" :class="colorClasses.text">
          {{ value }}
        </h3>
        <p class="text-base font-medium opacity-90" :class="colorClasses.text">
          {{ title }}
        </p>
        <p v-if="description" class="text-sm opacity-75" :class="colorClasses.text">
          {{ description }}
        </p>
      </div>
      
      <!-- Trend -->
      <div v-if="trendValue" class="flex items-center gap-1.5">
        <span 
          class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-white/20 backdrop-blur-sm"
          :class="colorClasses.text"
        >
          <i :class="[trendIcon, 'text-sm', trendColor]"></i>
          {{ trendValue }}
        </span>
        <span class="text-xs opacity-75" :class="colorClasses.text">vs last period</span>
      </div>
    </div>
  </div>
</template>
