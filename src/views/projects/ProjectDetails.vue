<script setup>
import { ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import PageHeader from '@/components/ui/PageHeader.vue'

const route = useRoute()
const router = useRouter()
const projectId = route.params.id

const project = ref({
  id: projectId,
  name: 'Website Redesign',
  description: 'Complete redesign of the corporate website with modern UI/UX',
  status: 'in-progress',
  progress: 75,
  priority: 'high',
  startDate: '2024-10-01',
  endDate: '2024-12-15',
  budget: 50000,
  spent: 35000,
  team: 'Marketing Team',
  client: 'Acme Corp',
  projectType: 'agile'
})

const navigateToStakeholders = () => {
  router.push(`/initiation/stakeholders?projectId=${projectId}`)
}

const tasks = ref([
  { id: 1, title: 'Design mockups', status: 'completed', assignee: 'John Doe' },
  { id: 2, title: 'Frontend development', status: 'in-progress', assignee: 'Jane Smith' },
  { id: 3, title: 'Backend API', status: 'in-progress', assignee: 'Mike Johnson' },
  { id: 4, title: 'Testing', status: 'pending', assignee: 'Sarah Wilson' }
])

const activeTab = ref('overview')

// Add Task modal state
const showAddTaskModal = ref(false)
const newTask = ref({
  title: '',
  assignee: '',
  status: 'pending'
})

const openAddTaskModal = () => {
  // Switch to tasks tab when adding a task
  activeTab.value = 'tasks'
  showAddTaskModal.value = true
}

const closeAddTaskModal = () => {
  showAddTaskModal.value = false
  newTask.value = {
    title: '',
    assignee: '',
    status: 'pending'
  }
}

const saveTask = () => {
  if (!newTask.value.title.trim()) {
    return
  }

  const nextId = tasks.value.length ? Math.max(...tasks.value.map(t => t.id)) + 1 : 1

  tasks.value.push({
    id: nextId,
    title: newTask.value.title.trim(),
    assignee: newTask.value.assignee.trim() || 'Unassigned',
    status: newTask.value.status
  })

  closeAddTaskModal()
}
</script>

<template>
  <div>
    <PageHeader :title="project.name" :subtitle="project.team">
      <template #actions>
        <button class="ti-btn ti-btn-light">
          <i class="ri-edit-line me-1"></i> Edit
        </button>
        <button class="ti-btn ti-btn-primary" @click="openAddTaskModal">
          <i class="ri-add-line me-1"></i> Add Task
        </button>
      </template>
    </PageHeader>

    <div class="grid grid-cols-12 gap-6">
      <!-- Main Content -->
      <div class="col-span-12 xl:col-span-8">
        <!-- Tabs -->
        <div class="box">
          <div class="box-header border-b">
            <nav class="flex gap-4">
              <button 
                @click="activeTab = 'overview'"
                class="pb-2 px-1 border-b-2 transition-colors"
                :class="activeTab === 'overview' ? 'border-primary text-primary' : 'border-transparent text-textmuted hover:text-defaulttextcolor'"
              >
                Overview
              </button>
              <button 
                @click="activeTab = 'tasks'"
                class="pb-2 px-1 border-b-2 transition-colors"
                :class="activeTab === 'tasks' ? 'border-primary text-primary' : 'border-transparent text-textmuted hover:text-defaulttextcolor'"
              >
                Tasks
              </button>
              <button 
                @click="activeTab = 'files'"
                class="pb-2 px-1 border-b-2 transition-colors"
                :class="activeTab === 'files' ? 'border-primary text-primary' : 'border-transparent text-textmuted hover:text-defaulttextcolor'"
              >
                Files
              </button>
              <button 
                @click="activeTab = 'activity'"
                class="pb-2 px-1 border-b-2 transition-colors"
                :class="activeTab === 'activity' ? 'border-primary text-primary' : 'border-transparent text-textmuted hover:text-defaulttextcolor'"
              >
                Activity
              </button>
            </nav>
          </div>
          <div class="box-body">
            <!-- Overview Tab -->
            <div v-if="activeTab === 'overview'">
              <p class="text-textmuted mb-4">{{ project.description }}</p>
              
              <div class="grid grid-cols-2 gap-4">
                <div>
                  <label class="text-xs text-textmuted uppercase">Start Date</label>
                  <p class="font-medium">{{ project.startDate }}</p>
                </div>
                <div>
                  <label class="text-xs text-textmuted uppercase">End Date</label>
                  <p class="font-medium">{{ project.endDate }}</p>
                </div>
                <div>
                  <label class="text-xs text-textmuted uppercase">Client</label>
                  <p class="font-medium">{{ project.client }}</p>
                </div>
                <div>
                  <label class="text-xs text-textmuted uppercase">Team</label>
                  <p class="font-medium">{{ project.team }}</p>
                </div>
              </div>
            </div>

            <!-- Tasks Tab -->
            <div v-if="activeTab === 'tasks'">
              <ul class="space-y-3">
                <li v-for="task in tasks" :key="task.id" class="flex items-center justify-between p-3 bg-light rounded-lg">
                  <div class="flex items-center gap-3">
                    <input type="checkbox" class="ti-form-check-input" :checked="task.status === 'completed'">
                    <div>
                      <span class="font-medium">{{ task.title }}</span>
                      <span class="block text-xs text-textmuted">{{ task.assignee }}</span>
                    </div>
                  </div>
                  <span 
                    class="badge"
                    :class="{
                      'bg-success/10 text-success': task.status === 'completed',
                      'bg-primary/10 text-primary': task.status === 'in-progress',
                      'bg-warning/10 text-warning': task.status === 'pending'
                    }"
                  >
                    {{ task.status }}
                  </span>
                </li>
              </ul>
            </div>

            <!-- Files Tab -->
            <div v-if="activeTab === 'files'">
              <div class="text-center py-8 text-textmuted">
                <i class="ri-folder-open-line text-4xl mb-2"></i>
                <p>No files uploaded yet</p>
              </div>
            </div>

            <!-- Activity Tab -->
            <div v-if="activeTab === 'activity'">
              <div class="text-center py-8 text-textmuted">
                <i class="ri-history-line text-4xl mb-2"></i>
                <p>Activity log will appear here</p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Sidebar -->
      <div class="col-span-12 xl:col-span-4">
        <!-- Progress -->
        <div class="box">
          <div class="box-header">
            <h5 class="box-title">Progress</h5>
          </div>
          <div class="box-body">
            <div class="text-center mb-4">
              <span class="text-4xl font-bold text-primary">{{ project.progress }}%</span>
            </div>
            <div class="progress progress-lg mb-4">
              <div class="progress-bar bg-primary" :style="{ width: project.progress + '%' }"></div>
            </div>
            <div class="flex justify-between text-sm text-textmuted">
              <span>Started: {{ project.startDate }}</span>
              <span>Due: {{ project.endDate }}</span>
            </div>
          </div>
        </div>

        <!-- Budget -->
        <div class="box">
          <div class="box-header">
            <h5 class="box-title">Budget</h5>
          </div>
          <div class="box-body">
            <div class="flex justify-between mb-2">
              <span class="text-textmuted">Total Budget</span>
              <span class="font-medium">${{ project.budget.toLocaleString() }}</span>
            </div>
            <div class="flex justify-between mb-2">
              <span class="text-textmuted">Spent</span>
              <span class="font-medium text-warning">${{ project.spent.toLocaleString() }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-textmuted">Remaining</span>
              <span class="font-medium text-success">${{ (project.budget - project.spent).toLocaleString() }}</span>
            </div>
          </div>
        </div>

        <!-- Status -->
        <div class="box">
          <div class="box-header">
            <h5 class="box-title">Status</h5>
          </div>
          <div class="box-body">
            <div class="flex items-center gap-3 mb-3">
              <span class="badge bg-primary/10 text-primary">{{ project.status }}</span>
              <span class="badge bg-danger/10 text-danger">{{ project.priority }} priority</span>
            </div>
          </div>
        </div>

        <!-- Project Areas - Icon Cards -->
        <div class="box">
          <div class="box-header">
            <h5 class="box-title">Project Areas</h5>
          </div>
          <div class="box-body">
            <div class="grid grid-cols-2 gap-3">
              <!-- Stakeholders Card -->
              <button 
                @click="navigateToStakeholders"
                class="group p-4 bg-light dark:bg-bgdark rounded-lg border border-defaultborder hover:border-primary hover:bg-primary/5 transition-all duration-200 text-center"
              >
                <div class="mb-2">
                  <i class="ri-user-line text-3xl text-primary group-hover:scale-110 transition-transform"></i>
                </div>
                <p class="text-sm font-medium text-defaulttextcolor">Stakeholders</p>
                <p class="text-xs text-textmuted mt-1">Manage stakeholders</p>
              </button>

              <!-- Resources Card -->
              <button 
                class="group p-4 bg-light dark:bg-bgdark rounded-lg border border-defaultborder hover:border-primary hover:bg-primary/5 transition-all duration-200 text-center"
              >
                <div class="mb-2">
                  <i class="ri-team-line text-3xl text-primary group-hover:scale-110 transition-transform"></i>
                </div>
                <p class="text-sm font-medium text-defaulttextcolor">Resources</p>
                <p class="text-xs text-textmuted mt-1">Team & allocation</p>
              </button>

              <!-- Risks Card -->
              <button 
                class="group p-4 bg-light dark:bg-bgdark rounded-lg border border-defaultborder hover:border-primary hover:bg-primary/5 transition-all duration-200 text-center"
              >
                <div class="mb-2">
                  <i class="ri-shield-cross-line text-3xl text-primary group-hover:scale-110 transition-transform"></i>
                </div>
                <p class="text-sm font-medium text-defaulttextcolor">Risks</p>
                <p class="text-xs text-textmuted mt-1">Risk management</p>
              </button>

              <!-- Communication Card -->
              <button 
                class="group p-4 bg-light dark:bg-bgdark rounded-lg border border-defaultborder hover:border-primary hover:bg-primary/5 transition-all duration-200 text-center"
              >
                <div class="mb-2">
                  <i class="ri-message-3-line text-3xl text-primary group-hover:scale-110 transition-transform"></i>
                </div>
                <p class="text-sm font-medium text-defaulttextcolor">Chat</p>
                <p class="text-xs text-textmuted mt-1">Team communication</p>
              </button>

              <!-- Gantt Chart Card -->
              <button 
                class="group p-4 bg-light dark:bg-bgdark rounded-lg border border-defaultborder hover:border-primary hover:bg-primary/5 transition-all duration-200 text-center"
              >
                <div class="mb-2">
                  <i class="ri-bar-chart-line text-3xl text-primary group-hover:scale-110 transition-transform"></i>
                </div>
                <p class="text-sm font-medium text-defaulttextcolor">Gantt</p>
                <p class="text-xs text-textmuted mt-1">Timeline view</p>
              </button>

              <!-- Reports Card -->
              <button 
                class="group p-4 bg-light dark:bg-bgdark rounded-lg border border-defaultborder hover:border-primary hover:bg-primary/5 transition-all duration-200 text-center"
              >
                <div class="mb-2">
                  <i class="ri-file-chart-line text-3xl text-primary group-hover:scale-110 transition-transform"></i>
                </div>
                <p class="text-sm font-medium text-defaulttextcolor">Reports</p>
                <p class="text-xs text-textmuted mt-1">Analytics & docs</p>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Add Task Modal -->
    <div 
      v-if="showAddTaskModal" 
      class="fixed inset-0 z-[80] flex items-center justify-center bg-black/40"
    >
      <div class="bg-white dark:bg-bgdark rounded-xl shadow-xl w-full max-w-md mx-4 max-h-[calc(100vh-6rem)] overflow-y-auto">
        <div class="px-6 py-4 border-b border-defaultborder/60 flex items-center justify-between">
          <h3 class="text-base font-semibold">Add Task</h3>
          <button 
            class="ti-btn ti-btn-sm ti-btn-icon ti-btn-light" 
            type="button"
            @click="closeAddTaskModal"
          >
            <i class="ri-close-line"></i>
          </button>
        </div>

        <div class="px-6 py-5 space-y-4">
          <div>
            <label class="ti-form-label text-sm mb-1">Task Title <span class="text-danger">*</span></label>
            <input 
              v-model="newTask.title" 
              type="text" 
              class="ti-form-control" 
              placeholder="Enter task title"
            >
          </div>

          <div>
            <label class="ti-form-label text-sm mb-1">Assignee</label>
            <input 
              v-model="newTask.assignee" 
              type="text" 
              class="ti-form-control" 
              placeholder="Who is responsible?"
            >
          </div>

          <div>
            <label class="ti-form-label text-sm mb-1">Status</label>
            <select v-model="newTask.status" class="ti-form-select">
              <option value="pending">Pending</option>
              <option value="in-progress">In Progress</option>
              <option value="completed">Completed</option>
            </select>
          </div>

          <p v-if="!newTask.title.trim()" class="text-xs text-warning mt-1">
            Enter a task title to enable save.
          </p>
        </div>

        <div class="px-6 py-4 border-t border-defaultborder/60 flex justify-end gap-3 bg-light/40 dark:bg-bgdark/40 rounded-b-xl">
          <button class="ti-btn ti-btn-light" type="button" @click="closeAddTaskModal">
            Cancel
          </button>
          <button 
            class="ti-btn ti-btn-primary" 
            type="button"
            :disabled="!newTask.title.trim()"
            @click="saveTask"
          >
            Save Task
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

