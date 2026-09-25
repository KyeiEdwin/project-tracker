<script setup>
import { computed } from 'vue'

const props = defineProps({
  sprint: { type: Object, required: true },
  loading: { type: Boolean, default: false }
})

const emit = defineEmits(['start', 'close'])

const completionRate = computed(() => {
  if (!props.sprint.points || props.sprint.points === 0) return 0
  return Math.round((props.sprint.completed / props.sprint.points) * 100)
})

const remainingPoints = computed(() => {
  return props.sprint.points - props.sprint.completed
})

const daysElapsed = computed(() => {
  if (!props.sprint.startDate || props.sprint.status === 'planned') return 0
  const start = new Date(props.sprint.startDate)
  const now = new Date()
  const diff = Math.max(0, Math.ceil((now - start) / (1000 * 60 * 60 * 24)))
  return diff
})

const totalDays = computed(() => {
  if (!props.sprint.startDate || !props.sprint.endDate) return 0
  const start = new Date(props.sprint.startDate)
  const end = new Date(props.sprint.endDate)
  return Math.ceil((end - start) / (1000 * 60 * 60 * 24)) + 1
})

const progressBarColor = computed(() => {
  const rate = completionRate.value
  if (rate >= 80) return 'bg-green-500'
  if (rate >= 50) return 'bg-blue-500'
  if (rate >= 25) return 'bg-yellow-500'
  return 'bg-orange-500'
})

const statusBadge = computed(() => {
  const status = props.sprint.status
  const badges = {
    planned: { text: 'Planned', class: 'bg-gray-100 text-gray-700' },
    active: { text: 'Active', class: 'bg-green-100 text-green-700' },
    completed: { text: 'Completed', class: 'bg-blue-100 text-blue-700' }
  }
  return badges[status] || badges.planned
})

const canStart = computed(() => {
  return props.sprint.status === 'planned' && !props.loading
})

const canClose = computed(() => {
  return props.sprint.status === 'active' && !props.loading
})
</script>

<template>
  <div class="bg-white rounded-lg shadow-lg p-6 border border-gray-200">
    <!-- Header -->
    <div class="flex items-start justify-between mb-6">
      <div class="flex-1">
        <div class="flex items-center gap-3 mb-2">
          <h3 class="text-2xl font-bold text-gray-900">{{ sprint.name }}</h3>
          <span 
            :class="['px-3 py-1 text-sm font-medium rounded-full', statusBadge.class]"
          >
            {{ statusBadge.text }}
          </span>
        </div>
        <p v-if="sprint.goal" class="text-gray-600">{{ sprint.goal }}</p>
      </div>

      <!-- Actions -->
      <div v-if="!loading" class="flex gap-2">
        <button
          v-if="canStart"
          @click="$emit('start')"
          class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-medium"
        >
          Start Sprint
        </button>
        <button
          v-if="canClose"
          @click="$emit('close')"
          class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium"
        >
          Close Sprint
        </button>
      </div>
    </div>

    <!-- Dates -->
    <div class="grid grid-cols-3 gap-4 mb-6">
      <div class="text-center p-3 bg-gray-50 rounded-lg">
        <div class="text-sm text-gray-600 mb-1">Start Date</div>
        <div class="font-semibold text-gray-900">{{ sprint.startDate }}</div>
      </div>
      <div class="text-center p-3 bg-gray-50 rounded-lg">
        <div class="text-sm text-gray-600 mb-1">End Date</div>
        <div class="font-semibold text-gray-900">{{ sprint.endDate }}</div>
      </div>
      <div class="text-center p-3 bg-gray-50 rounded-lg">
        <div class="text-sm text-gray-600 mb-1">Days Remaining</div>
        <div class="font-semibold" :class="sprint.daysRemaining < 3 ? 'text-red-600' : 'text-gray-900'">
          {{ sprint.daysRemaining }} {{ sprint.daysRemaining === 1 ? 'day' : 'days' }}
        </div>
      </div>
    </div>

    <!-- Story Points Progress -->
    <div class="mb-6">
      <div class="flex items-center justify-between mb-2">
        <span class="text-sm font-medium text-gray-700">Story Points Progress</span>
        <span class="text-sm font-semibold text-gray-900">
          {{ sprint.completed }} / {{ sprint.points }} ({{ completionRate }}%)
        </span>
      </div>
      <div class="w-full bg-gray-200 rounded-full h-4 overflow-hidden">
        <div
          :class="['h-full rounded-full transition-all duration-500', progressBarColor]"
          :style="{ width: completionRate + '%' }"
        ></div>
      </div>
    </div>

    <!-- Metrics Grid -->
    <div class="grid grid-cols-4 gap-4">
      <!-- Planned Points -->
      <div class="text-center p-4 bg-blue-50 rounded-lg border border-blue-200">
        <div class="text-3xl font-bold text-blue-600">{{ sprint.points }}</div>
        <div class="text-sm text-blue-700 mt-1">Planned</div>
      </div>

      <!-- Completed Points -->
      <div class="text-center p-4 bg-green-50 rounded-lg border border-green-200">
        <div class="text-3xl font-bold text-green-600">{{ sprint.completed }}</div>
        <div class="text-sm text-green-700 mt-1">Completed</div>
      </div>

      <!-- Remaining Points -->
      <div class="text-center p-4 bg-orange-50 rounded-lg border border-orange-200">
        <div class="text-3xl font-bold text-orange-600">{{ remainingPoints }}</div>
        <div class="text-sm text-orange-700 mt-1">Remaining</div>
      </div>

      <!-- Completion Rate -->
      <div class="text-center p-4 bg-purple-50 rounded-lg border border-purple-200">
        <div class="text-3xl font-bold text-purple-600">{{ completionRate }}%</div>
        <div class="text-sm text-purple-700 mt-1">Complete</div>
      </div>
    </div>

    <!-- Sprint Timeline (if active) -->
    <div v-if="sprint.status === 'active' && totalDays > 0" class="mt-6 pt-6 border-t">
      <div class="flex items-center justify-between mb-2">
        <span class="text-sm font-medium text-gray-700">Sprint Timeline</span>
        <span class="text-sm text-gray-600">
          Day {{ daysElapsed }} of {{ totalDays }}
        </span>
      </div>
      <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
        <div
          class="h-full bg-gradient-to-r from-blue-400 to-blue-600 rounded-full transition-all duration-500"
          :style="{ width: Math.min(100, (daysElapsed / totalDays) * 100) + '%' }"
        ></div>
      </div>
    </div>

    <!-- Loading Overlay -->
    <div v-if="loading" class="absolute inset-0 bg-white bg-opacity-75 flex items-center justify-center rounded-lg">
      <div class="text-center">
        <div class="inline-block animate-spin rounded-full h-8 w-8 border-4 border-gray-300 border-t-blue-600"></div>
        <div class="mt-2 text-sm text-gray-600">Updating sprint...</div>
      </div>
    </div>
  </div>
</template>

<style scoped>
@keyframes spin {
  to { transform: rotate(360deg); }
}

.animate-spin {
  animation: spin 1s linear infinite;
}
</style>
