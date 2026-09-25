<script setup>
import { ref, computed } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import BacklogHierarchyTree from '@/Components/Agile/BacklogHierarchyTree.vue'
import QuickAddBacklogItem from '@/Components/Agile/QuickAddBacklogItem.vue'

const props = defineProps({
  backlogItems: { type: Array, default: () => [] },
  projects: { type: Array, default: () => [] }
})

const viewMode = ref('hierarchy') // 'hierarchy' or 'list'
const selectedProject = ref(null)
const showQuickAdd = ref(false)
const quickAddParent = ref(null)

// Filter items by selected project
const filteredItems = computed(() => {
  if (!selectedProject.value) return props.backlogItems
  return props.backlogItems.filter(item => item.projectId === selectedProject.value)
})

// Group by status for list view
const groupedByStatus = computed(() => {
  const groups = {
    backlog: [],
    ready: [],
    'in-progress': [],
    done: []
  }
  
  filteredItems.value.forEach(item => {
    if (groups[item.status]) {
      groups[item.status].push(item)
    }
  })
  
  return groups
})

const handleItemSelected = (data) => {
  if (data.action === 'addChild') {
    quickAddParent.value = data.parent
    showQuickAdd.value = true
  } else if (data.action === 'edit') {
    router.get(`/backlog-items/${data.item.id}/edit`)
  }
}

const handleQuickAddClose = () => {
  showQuickAdd.value = false
  quickAddParent.value = null
}

const handleItemCreated = () => {
  showQuickAdd.value = false
  quickAddParent.value = null
  router.reload({ only: ['backlogItems'] })
}

const openCreateModal = () => {
  quickAddParent.value = null
  showQuickAdd.value = true
}

// Statistics
const stats = computed(() => {
  const items = filteredItems.value
  return {
    total: items.length,
    epics: items.filter(i => i.type === 'epic').length,
    features: items.filter(i => i.type === 'feature').length,
    stories: items.filter(i => i.type === 'story').length,
    totalPoints: items.reduce((sum, i) => sum + (i.points || 0), 0)
  }
})
</script>

<template>
  <AppLayout>
    <Head title="Product Backlog" />

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Header -->
      <div class="mb-8">
        <div class="flex items-center justify-between">
          <div>
            <h1 class="text-3xl font-bold text-gray-900">Product Backlog</h1>
            <p class="mt-2 text-gray-600">Manage your hierarchical product backlog</p>
          </div>
          <button
            @click="openCreateModal"
            class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium shadow-sm"
          >
            + Add Backlog Item
          </button>
        </div>

        <!-- Statistics -->
        <div class="mt-6 grid grid-cols-5 gap-4">
          <div class="bg-white p-4 rounded-lg border border-gray-200">
            <div class="text-2xl font-bold text-gray-900">{{ stats.total }}</div>
            <div class="text-sm text-gray-600">Total Items</div>
          </div>
          <div class="bg-purple-50 p-4 rounded-lg border border-purple-200">
            <div class="text-2xl font-bold text-purple-600">{{ stats.epics }}</div>
            <div class="text-sm text-purple-700">Epics</div>
          </div>
          <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
            <div class="text-2xl font-bold text-blue-600">{{ stats.features }}</div>
            <div class="text-sm text-blue-700">Features</div>
          </div>
          <div class="bg-green-50 p-4 rounded-lg border border-green-200">
            <div class="text-2xl font-bold text-green-600">{{ stats.stories }}</div>
            <div class="text-sm text-green-700">Stories</div>
          </div>
          <div class="bg-orange-50 p-4 rounded-lg border border-orange-200">
            <div class="text-2xl font-bold text-orange-600">{{ stats.totalPoints }}</div>
            <div class="text-sm text-orange-700">Story Points</div>
          </div>
        </div>
      </div>

      <!-- Filters and View Toggle -->
      <div class="mb-6 flex items-center justify-between bg-white p-4 rounded-lg border border-gray-200">
        <div class="flex items-center gap-4">
          <label class="text-sm font-medium text-gray-700">Project:</label>
          <select
            v-model="selectedProject"
            class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
          >
            <option :value="null">All Projects</option>
            <option v-for="project in projects" :key="project.value" :value="project.value">
              {{ project.label }}
            </option>
          </select>
        </div>

        <div class="flex items-center gap-2 bg-gray-100 p-1 rounded-lg">
          <button
            @click="viewMode = 'hierarchy'"
            :class="[
              'px-4 py-2 rounded-lg font-medium transition',
              viewMode === 'hierarchy'
                ? 'bg-white shadow-sm text-blue-600'
                : 'text-gray-600 hover:text-gray-900'
            ]"
          >
            🌳 Hierarchy
          </button>
          <button
            @click="viewMode = 'list'"
            :class="[
              'px-4 py-2 rounded-lg font-medium transition',
              viewMode === 'list'
                ? 'bg-white shadow-sm text-blue-600'
                : 'text-gray-600 hover:text-gray-900'
            ]"
          >
            📋 List
          </button>
        </div>
      </div>

      <!-- Content -->
      <div class="bg-white rounded-lg shadow-lg p-6 border border-gray-200">
        <!-- Hierarchy View -->
        <BacklogHierarchyTree
          v-if="viewMode === 'hierarchy'"
          :items="filteredItems"
          :project-id="selectedProject || 1"
          @item-selected="handleItemSelected"
        />

        <!-- List View (Kanban-style) -->
        <div v-else class="grid grid-cols-4 gap-4">
          <div
            v-for="(items, status) in groupedByStatus"
            :key="status"
            class="flex flex-col"
          >
            <div class="mb-3 px-4 py-2 bg-gray-50 rounded-lg border border-gray-200">
              <h3 class="font-semibold text-gray-900 capitalize">{{ status }}</h3>
              <span class="text-sm text-gray-600">{{ items.length }} items</span>
            </div>
            <div class="space-y-2 flex-1">
              <div
                v-for="item in items"
                :key="item.id"
                class="p-4 bg-white border border-gray-200 rounded-lg hover:shadow-md transition cursor-pointer"
                @click="router.get(`/backlog-items/${item.id}`)"
              >
                <div class="flex items-start justify-between mb-2">
                  <span class="text-2xl">{{ getTypeIcon(item.type) }}</span>
                  <span v-if="item.points" class="px-2 py-1 bg-gray-100 text-gray-700 text-xs font-medium rounded">
                    {{ item.points }}
                  </span>
                </div>
                <div class="font-medium text-gray-900 mb-1">{{ item.title }}</div>
                <div class="flex items-center gap-2">
                  <span :class="['px-2 py-1 text-xs rounded', getTypeColor(item.type)]">
                    {{ item.type }}
                  </span>
                  <span :class="['px-2 py-1 text-xs rounded', getPriorityColor(item.priority)]">
                    {{ item.priority }}
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Quick Add Modal -->
    <QuickAddBacklogItem
      :show="showQuickAdd"
      :project-id="selectedProject || (projects[0]?.value || 1)"
      :parent-item="quickAddParent"
      @close="handleQuickAddClose"
      @created="handleItemCreated"
    />
  </AppLayout>
</template>

<script>
export default {
  methods: {
    getTypeIcon(type) {
      const icons = { epic: '🎯', feature: '📦', story: '📝', task: '✓', bug: '🐛', spike: '⚡' }
      return icons[type] || '📄'
    },
    getTypeColor(type) {
      const colors = {
        epic: 'bg-purple-100 text-purple-700',
        feature: 'bg-blue-100 text-blue-700',
        story: 'bg-green-100 text-green-700',
        task: 'bg-gray-100 text-gray-700',
        bug: 'bg-red-100 text-red-700',
        spike: 'bg-yellow-100 text-yellow-700'
      }
      return colors[type] || 'bg-gray-100 text-gray-700'
    },
    getPriorityColor(priority) {
      const colors = {
        critical: 'bg-red-100 text-red-700',
        high: 'bg-orange-100 text-orange-700',
        medium: 'bg-yellow-100 text-yellow-700',
        low: 'bg-blue-100 text-blue-700'
      }
      return colors[priority] || colors.medium
    }
  }
}
</script>
