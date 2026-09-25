<script setup>
import { ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'

const props = defineProps({
  show: { type: Boolean, required: true },
  projectId: { type: Number, required: true },
  parentItem: { type: Object, default: null }
})

const emit = defineEmits(['close', 'created'])

const form = ref({
  project_id: props.projectId,
  parent_id: props.parentItem?.id || null,
  title: '',
  type: 'story',
  points: 0,
  priority: 'medium',
  status: 'backlog',
  description: ''
})

const processing = ref(false)
const errors = ref({})

// Type options based on parent type
const typeOptions = computed(() => {
  if (!props.parentItem) {
    return [
      { value: 'epic', label: 'Epic 🎯', description: 'Large initiative spanning multiple sprints' },
      { value: 'feature', label: 'Feature 📦', description: 'Significant functionality' },
      { value: 'story', label: 'Story 📝', description: 'User-facing functionality' }
    ]
  }

  const parentType = props.parentItem.type
  const validChildren = {
    epic: [
      { value: 'feature', label: 'Feature 📦', description: 'Feature under this epic' },
      { value: 'story', label: 'Story 📝', description: 'Story under this epic' }
    ],
    feature: [
      { value: 'story', label: 'Story 📝', description: 'User story' },
      { value: 'task', label: 'Task ✓', description: 'Technical task' }
    ],
    story: [
      { value: 'task', label: 'Task ✓', description: 'Implementation task' },
      { value: 'bug', label: 'Bug 🐛', description: 'Bug fix' }
    ]
  }

  return validChildren[parentType] || []
})

// Priority options
const priorityOptions = [
  { value: 'low', label: 'Low', color: 'blue' },
  { value: 'medium', label: 'Medium', color: 'yellow' },
  { value: 'high', label: 'High', color: 'orange' },
  { value: 'critical', label: 'Critical', color: 'red' }
]

// Point options (Fibonacci sequence)
const pointOptions = [0, 1, 2, 3, 5, 8, 13, 21]

const resetForm = () => {
  form.value = {
    project_id: props.projectId,
    parent_id: props.parentItem?.id || null,
    title: '',
    type: typeOptions.value[0]?.value || 'story',
    points: 0,
    priority: 'medium',
    status: 'backlog',
    description: ''
  }
  errors.value = {}
}

const handleSubmit = () => {
  processing.value = true
  errors.value = {}

  router.post('/backlog-items', form.value, {
    preserveScroll: true,
    onSuccess: () => {
      emit('created')
      resetForm()
      emit('close')
    },
    onError: (err) => {
      errors.value = err
      processing.value = false
    },
    onFinish: () => {
      processing.value = false
    }
  })
}

const handleClose = () => {
  if (!processing.value) {
    resetForm()
    emit('close')
  }
}

// Initialize form when modal opens
watch(() => props.show, (show) => {
  if (show) {
    resetForm()
    if (typeOptions.value.length > 0) {
      form.value.type = typeOptions.value[0].value
    }
  }
})
</script>

<template>
  <Teleport to="body">
    <Transition name="modal">
      <div
        v-if="show"
        class="fixed inset-0 z-50 overflow-y-auto"
        @click.self="handleClose"
      >
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity"></div>

        <!-- Modal Content -->
        <div class="flex min-h-screen items-center justify-center p-4">
          <div
            class="relative bg-white rounded-lg shadow-xl max-w-2xl w-full transform transition-all"
            @click.stop
          >
            <!-- Header -->
            <div class="flex items-center justify-between p-6 border-b">
              <div>
                <h2 class="text-2xl font-bold text-gray-900">
                  {{ parentItem ? 'Add Child Item' : 'Quick Add Backlog Item' }}
                </h2>
                <p v-if="parentItem" class="text-sm text-gray-600 mt-1">
                  Adding to: <span class="font-medium">{{ parentItem.title }}</span>
                </p>
              </div>
              <button
                @click="handleClose"
                class="text-gray-400 hover:text-gray-600 transition"
                :disabled="processing"
              >
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            </div>

            <!-- Form -->
            <form @submit.prevent="handleSubmit" class="p-6 space-y-6">
              <!-- Title -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Title <span class="text-red-500">*</span>
                </label>
                <input
                  v-model="form.title"
                  type="text"
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  placeholder="Enter item title..."
                  required
                  :disabled="processing"
                  autofocus
                />
                <p v-if="errors.title" class="mt-1 text-sm text-red-600">{{ errors.title }}</p>
              </div>

              <!-- Type Selection -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Type <span class="text-red-500">*</span>
                </label>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                  <label
                    v-for="option in typeOptions"
                    :key="option.value"
                    class="relative flex cursor-pointer rounded-lg border p-4 focus:outline-none"
                    :class="[
                      form.type === option.value
                        ? 'border-blue-500 bg-blue-50 ring-2 ring-blue-500'
                        : 'border-gray-300 bg-white hover:border-gray-400'
                    ]"
                  >
                    <input
                      v-model="form.type"
                      type="radio"
                      :value="option.value"
                      class="sr-only"
                      :disabled="processing"
                    />
                    <div class="flex-1">
                      <div class="font-medium text-gray-900">{{ option.label }}</div>
                      <div class="text-sm text-gray-500">{{ option.description }}</div>
                    </div>
                  </label>
                </div>
                <p v-if="errors.type" class="mt-1 text-sm text-red-600">{{ errors.type }}</p>
              </div>

              <!-- Points and Priority Row -->
              <div class="grid grid-cols-2 gap-4">
                <!-- Story Points -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">
                    Story Points
                  </label>
                  <select
                    v-model.number="form.points"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    :disabled="processing"
                  >
                    <option v-for="point in pointOptions" :key="point" :value="point">
                      {{ point }} {{ point === 1 ? 'point' : 'points' }}
                    </option>
                  </select>
                </div>

                <!-- Priority -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">
                    Priority
                  </label>
                  <select
                    v-model="form.priority"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    :disabled="processing"
                  >
                    <option
                      v-for="option in priorityOptions"
                      :key="option.value"
                      :value="option.value"
                    >
                      {{ option.label }}
                    </option>
                  </select>
                </div>
              </div>

              <!-- Description (Optional) -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Description <span class="text-gray-400">(optional)</span>
                </label>
                <textarea
                  v-model="form.description"
                  rows="3"
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  placeholder="Add details about this item..."
                  :disabled="processing"
                ></textarea>
              </div>

              <!-- Actions -->
              <div class="flex items-center justify-end gap-3 pt-4 border-t">
                <button
                  type="button"
                  @click="handleClose"
                  class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition"
                  :disabled="processing"
                >
                  Cancel
                </button>
                <button
                  type="submit"
                  class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition disabled:opacity-50 disabled:cursor-not-allowed"
                  :disabled="processing"
                >
                  <span v-if="processing">Creating...</span>
                  <span v-else>Create Item</span>
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<style scoped>
.modal-enter-active,
.modal-leave-active {
  transition: opacity 0.3s ease;
}

.modal-enter-from,
.modal-leave-to {
  opacity: 0;
}

.modal-enter-active .relative,
.modal-leave-active .relative {
  transition: transform 0.3s ease;
}

.modal-enter-from .relative,
.modal-leave-to .relative {
  transform: scale(0.95);
}
</style>
