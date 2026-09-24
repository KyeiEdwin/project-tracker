<script setup>
defineProps({
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
  iconBg: {
    type: String,
    default: 'bg-green-600'
  },
  badge: {
    type: String,
    default: ''
  },
  badgeClass: {
    type: String,
    default: 'badge-success'
  },
  trend: {
    type: String,
    default: '' // 'up', 'down', or ''
  },
  subtitle: {
    type: String,
    default: ''
  }
})
</script>

<template>
  <div class="box overflow-hidden group">
    <div class="box-body">
      <!-- Icon and Badge Row -->
      <div class="flex items-center justify-between mb-5">
        <div class="stat-card-icon-wrapper">
          <span 
            class="inline-flex items-center justify-center w-14 h-14 rounded-2xl text-white shadow-lg transition-transform duration-300 group-hover:scale-110" 
            :class="iconBg"
          >
            <i :class="icon" class="text-2xl"></i>
          </span>
        </div>
        <span v-if="badge" class="badge-modern" :class="badgeClass">
          <i v-if="trend === 'up'" class="ri-arrow-up-line text-xs mr-1"></i>
          <i v-if="trend === 'down'" class="ri-arrow-down-line text-xs mr-1"></i>
          {{ badge }}
        </span>
      </div>
      
      <!-- Value and Title -->
      <div>
        <h3 class="text-4xl font-bold mb-2 text-gray-900 dark:text-gray-50 tracking-tight">
          {{ value }}
        </h3>
        <p class="text-base font-medium text-gray-600 dark:text-gray-300 mb-1">
          {{ title }}
        </p>
        <p v-if="subtitle" class="text-sm text-gray-500 dark:text-gray-400">
          {{ subtitle }}
        </p>
      </div>
    </div>
  </div>
</template>

<style scoped>
.stat-card-icon-wrapper {
  position: relative;
}

.stat-card-icon-wrapper::before {
  content: '';
  position: absolute;
  inset: -4px;
  border-radius: 1.25rem;
  background: linear-gradient(135deg, rgba(34, 197, 94, 0.2), rgba(16, 185, 129, 0.1));
  opacity: 0;
  transition: opacity 0.3s ease;
}

.group:hover .stat-card-icon-wrapper::before {
  opacity: 1;
}
</style>

