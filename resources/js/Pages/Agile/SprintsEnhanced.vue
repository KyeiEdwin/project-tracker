<script setup>
import { ref } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import axios from 'axios'
import AppLayout from '@/Layouts/AppLayout.vue'
import SprintMetricsCard from '@/Components/Agile/SprintMetricsCard.vue'
import BurndownChart from '@/Components/Agile/BurndownChart.vue'

const props = defineProps({
  sprints: { type: Array, default: () => [] },
  currentSprint: { type: Object, default: null },
  projects: { type: Array, default: () => [] }
})

const loading = ref(false)
const showAllSprints = ref(false)

// Sprint actions
const startSprint = async (sprint) => {
  if (!confirm(`Start sprint "${sprint.name}"?`)) return

  loading.value = true
  try {
    await axios.post(`/sprints/${sprint.id}/start`)
    router.reload({ only: ['sprints', 'currentSprint'] })
  } catch (error) {
    alert(error.response?.data?.message || 'Failed to start sprint')
  } finally {
    loading.value = false
  }
}

const closeSprint = async (sprint) => {
  if (!confirm(`Close sprint "${sprint.name}"? This action cannot be undone.`)) return

  loading.value = true
  try {
    await axios.post(`/sprints/${sprint.id}/close`)
    router.reload({ only: ['sprints', 'currentSprint'] })
  } catch (error) {
    alert(error.response?.data?.message || 'Failed to close sprint')
  } finally {
    loading.value = false
  }
}

// Display sprints
const displayedSprints = computed(() => {
  if (showAllSprints.value) return props.sprints
  return props.sprints.slice(0, 5)
})

const hasCurrentSprint = computed(() => {
  return props.currentSprint && props.currentSprint.id
})

</script>

<template>
  <AppLayout>
    <Head title="Sprint Management" />

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Header -->
      <div class="mb-8 flex items-center justify-between">
        <div>
          <h1 class="text-3xl font-bold text-gray-900">Sprint Management</h1>
          <p class="mt-2 text-gray-600">Track and manage your sprint cycles</p>
        </div>
        <button
          @click="router.get('/sprints/create')"
          class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium shadow-sm"
        >
          + Create Sprint
        </button>
      </div>

      <!-- Current Sprint Section -->
      <div v-if="hasCurrentSprint" class="mb-8">
        <h2 class="text-xl font-bold text-gray-900 mb-4">Current Sprint</h2>
        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
          <!-- Sprint Metrics -->
          <SprintMetricsCard
            :sprint="currentSprint"
            :loading="loading"
            @start="startSprint(currentSprint)"
            @close="closeSprint(currentSprint)"
          />

          <!-- Burndown Chart -->
          <BurndownChart
            v-if="currentSprint.status === 'active'"
            :sprint-id="currentSprint.id"
            :auto-refresh="true"
          />
          
          <!-- Placeholder for planned sprints -->
          <div
            v-else
            class="bg-white rounded-lg shadow-lg p-6 border border-gray-200 flex items-center justify-center"
          >
            <div class="text-center text-gray-500">
              <div class="text-4xl mb-3">📊</div>
              <div class="font-medium">Burndown chart will appear when sprint is started</div>
            </div>
          </div>
        </div>
      </div>

      <!-- No Current Sprint -->
      <div v-else class="mb-8 bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-lg p-8">
        <div class="text-center">
          <div class="text-5xl mb-4">🚀</div>
          <h2 class="text-2xl font-bold text-gray-900 mb-2">No Active Sprint</h2>
          <p class="text-gray-600 mb-6">
            Create a new sprint and start planning your next iteration
          </p>
          <button
            @click="router.get('/sprints/create')"
            class="px-8 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium shadow-md"
          >
            Create Your First Sprint
          </button>
        </div>
      </div>

      <!-- All Sprints -->
      <div>
        <div class="flex items-center justify-between mb-4">
          <h2 class="text-xl font-bold text-gray-900">All Sprints</h2>
          <button
            v-if="sprints.length > 5"
            @click="showAllSprints = !showAllSprints"
            class="text-sm text-blue-600 hover:text-blue-700 font-medium"
          >
            {{ showAllSprints ? 'Show Less' : `Show All (${sprints.length})` }}
          </button>
        </div>

        <!-- Sprints List -->
        <div class="space-y-4">
          <div
            v-for="sprint in displayedSprints"
            :key="sprint.id"
            class="bg-white rounded-lg shadow-md p-6 border border-gray-200 hover:shadow-lg transition cursor-pointer"
            @click="router.get(`/sprints/${sprint.id}`)"
          >
            <div class="flex items-start justify-between">
              <!-- Sprint Info -->
              <div class="flex-1">
                <div class="flex items-center gap-3 mb-2">
                  <h3 class="text-lg font-bold text-gray-900">{{ sprint.name }}</h3>
                  <span
                    :class="[
                      'px-3 py-1 text-sm font-medium rounded-full',
                      sprint.status === 'active'
                        ? 'bg-green-100 text-green-700'
                        : sprint.status === 'completed'
                        ? 'bg-blue-100 text-blue-700'
                        : 'bg-gray-100 text-gray-700'
                    ]"
                  >
                    {{ sprint.status }}
                  </span>
                </div>
                <p v-if="sprint.goal" class="text-gray-600 text-sm mb-3">{{ sprint.goal }}</p>
                
                <!-- Dates and Points -->
                <div class="flex items-center gap-6 text-sm text-gray-600">
                  <span>📅 {{ sprint.startDate }} → {{ sprint.endDate }}</span>
                  <span>📊 {{ sprint.completed }} / {{ sprint.points }} points</span>
                  <span v-if="sprint.status === 'active'" class="text-orange-600 font-medium">
                    ⏳ {{ sprint.daysRemaining }} days left
                  </span>
                </div>
              </div>

              <!-- Progress Bar -->
              <div class="ml-6 w-32">
                <div class="text-right text-sm font-semibold text-gray-900 mb-1">
                  {{ Math.round((sprint.completed / Math.max(sprint.points, 1)) * 100) }}%
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                  <div
                    :class="[
                      'h-full rounded-full transition-all',
                      sprint.status === 'completed' ? 'bg-blue-500' : 'bg-green-500'
                    ]"
                    :style="{ width: Math.round((sprint.completed / Math.max(sprint.points, 1)) * 100) + '%' }"
                  ></div>
                </div>
              </div>
            </div>

            <!-- Action Buttons -->
            <div class="mt-4 pt-4 border-t flex items-center gap-3">
              <button
                v-if="sprint.status === 'planned'"
                @click.stop="startSprint(sprint)"
                class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition text-sm font-medium"
                :disabled="loading"
              >
                Start Sprint
              </button>
              <button
                v-if="sprint.status === 'active'"
                @click.stop="closeSprint(sprint)"
                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm font-medium"
                :disabled="loading"
              >
                Close Sprint
              </button>
              <button
                @click.stop="router.get(`/sprints/${sprint.id}/edit`)"
                class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition text-sm font-medium"
                :disabled="sprint.status === 'completed'"
              >
                Edit
              </button>
              <button
                @click.stop="router.get(`/sprints/${sprint.id}`)"
                class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition text-sm font-medium"
              >
                View Details
              </button>
            </div>
          </div>

          <!-- Empty State -->
          <div v-if="sprints.length === 0" class="text-center py-12 bg-white rounded-lg border border-gray-200">
            <div class="text-4xl mb-3">📋</div>
            <div class="text-lg font-medium text-gray-900 mb-2">No Sprints Yet</div>
            <div class="text-sm text-gray-600 mb-6">Create your first sprint to start tracking work</div>
            <button
              @click="router.get('/sprints/create')"
              class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium"
            >
              Create Sprint
            </button>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
