<script setup>
import { computed, ref } from 'vue'
import { Link, useForm } from '@inertiajs/vue3'
import PageHeader from '@/Components/ui/PageHeader.vue'
import { useProjectRealtime } from '@/composables/useProjectRealtime'

const props = defineProps({
  project: {
    type: Object,
    required: true
  },
  tasks: {
    type: Array,
    default: () => []
  },
  stakeholders: {
    type: Array,
    default: () => []
  },
  risks: {
    type: Array,
    default: () => []
  },
  teamMembers: { 
    type: Array, 
    default: () => [] 
  },
  stats: {
    type: Object,
    default: () => ({})
  }
})

const projectId = computed(() => props.project.id)
const project = ref({ ...props.project })

// Helper function to generate section links with project context
const sectionLink = (path) => `${path}?project_id=${projectId.value}`

const tasks = ref([...props.tasks])

useProjectRealtime(projectId, {
  onTaskUpdated: (event) => {
    if (event.deleted) {
      tasks.value = tasks.value.filter((task) => task.id !== event.task.id)
      return
    }

    const index = tasks.value.findIndex((task) => task.id === event.task.id)
    if (index === -1) tasks.value.push(event.task)
    else tasks.value[index] = event.task
  },
  onProgressUpdated: (event) => {
    project.value.progress = event.progress
  },
})

const activeTab = ref('overview')

// Add Task modal state
const showAddTaskModal = ref(false)
const newTask = useForm({ 
  project_id: projectId.value, 
  title: '', 
  team_member_id: '', 
  status: 'pending', 
  priority: 'medium' 
})

const openAddTaskModal = () => {
  activeTab.value = 'tasks'
  showAddTaskModal.value = true
}

const closeAddTaskModal = () => {
  showAddTaskModal.value = false
  newTask.reset()
  newTask.project_id = projectId.value
  newTask.status = 'pending'
  newTask.priority = 'medium'
}

const saveTask = () => {
  newTask.post('/tasks', { 
    preserveScroll: true, 
    onSuccess: closeAddTaskModal 
  })
}

const formatDate = (dateStr) => {
  if (!dateStr) return 'Not set'
  return new Date(dateStr).toLocaleDateString('en-US', { 
    month: 'short', 
    day: 'numeric', 
    year: 'numeric' 
  })
}

const getStatusClass = (status) => {
  const classes = {
    'planning': 'bg-info/10 text-info',
    'in-progress': 'bg-primary/10 text-primary',
    'on-hold': 'bg-warning/10 text-warning',
    'completed': 'bg-success/10 text-success'
  }
  return classes[status] || 'bg-secondary/10 text-secondary'
}

const getPriorityClass = (priority) => {
  const classes = {
    'high': 'bg-danger/10 text-danger',
    'medium': 'bg-warning/10 text-warning',
    'low': 'bg-success/10 text-success'
  }
  return classes[priority] || 'bg-secondary/10 text-secondary'
}
</script>

<template>
  <div>
    <PageHeader :title="project.name" :subtitle="project.description">
      <template #actions>
        <Link 
          :href="`/projects/${project.id}/edit`" 
          class="ti-btn ti-btn-light"
        >
          <i class="ri-edit-line me-1"></i> Edit Project
        </Link>
        <button class="ti-btn ti-btn-primary" @click="openAddTaskModal">
          <i class="ri-add-line me-1"></i> Add Task
        </button>
      </template>
    </PageHeader>

    <div class="grid grid-cols-12 gap-4 lg:gap-6">
      <!-- Main Content -->
      <div class="col-span-12 xl:col-span-8">
        <!-- Tabs -->
        <div class="box">
          <div class="box-header border-b overflow-x-auto">
            <nav class="flex gap-3 md:gap-4 min-w-max">
              <button 
                @click="activeTab = 'overview'"
                class="pb-2 px-1 border-b-2 transition-colors whitespace-nowrap text-sm md:text-base"
                :class="activeTab === 'overview' ? 'border-primary text-primary' : 'border-transparent text-textmuted hover:text-defaulttextcolor'"
              >
                Overview
              </button>
              <button 
                @click="activeTab = 'tasks'"
                class="pb-2 px-1 border-b-2 transition-colors whitespace-nowrap text-sm md:text-base"
                :class="activeTab === 'tasks' ? 'border-primary text-primary' : 'border-transparent text-textmuted hover:text-defaulttextcolor'"
              >
                Tasks ({{ stats.totalTasks || 0 }})
              </button>
              <button 
                @click="activeTab = 'stakeholders'"
                class="pb-2 px-1 border-b-2 transition-colors whitespace-nowrap text-sm md:text-base"
                :class="activeTab === 'stakeholders' ? 'border-primary text-primary' : 'border-transparent text-textmuted hover:text-defaulttextcolor'"
              >
                Stakeholders ({{ stats.stakeholdersCount || 0 }})
              </button>
              <button 
                @click="activeTab = 'risks'"
                class="pb-2 px-1 border-b-2 transition-colors whitespace-nowrap text-sm md:text-base"
                :class="activeTab === 'risks' ? 'border-primary text-primary' : 'border-transparent text-textmuted hover:text-defaulttextcolor'"
              >
                Risks ({{ stats.openRisks || 0 }})
              </button>
            </nav>
          </div>
          <div class="box-body">
            <!-- Overview Tab -->
            <div v-if="activeTab === 'overview'">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                  <label class="text-xs text-textmuted uppercase mb-1 block">Status</label>
                  <span class="badge" :class="getStatusClass(project.status)">
                    {{ (project.status || '').replace('-', ' ') }}
                  </span>
                </div>
                <div>
                  <label class="text-xs text-textmuted uppercase mb-1 block">Priority</label>
                  <span class="badge" :class="getPriorityClass(project.priority)">
                    {{ project.priority }}
                  </span>
                </div>
                <div>
                  <label class="text-xs text-textmuted uppercase mb-1 block">Start Date</label>
                  <p class="font-medium">{{ formatDate(project.startDate) }}</p>
                </div>
                <div>
                  <label class="text-xs text-textmuted uppercase mb-1 block">Due Date</label>
                  <p class="font-medium">{{ formatDate(project.dueDate) }}</p>
                </div>
                <div>
                  <label class="text-xs text-textmuted uppercase mb-1 block">Client</label>
                  <p class="font-medium">{{ project.client || 'Not specified' }}</p>
                </div>
                <div>
                  <label class="text-xs text-textmuted uppercase mb-1 block">Team</label>
                  <p class="font-medium">{{ project.team || 'Not assigned' }}</p>
                </div>
              </div>
              
              <div v-if="project.description" class="mt-4">
                <label class="text-xs text-textmuted uppercase mb-1 block">Description</label>
                <p class="text-defaulttextcolor">{{ project.description }}</p>
              </div>
            </div>

            <!-- Tasks Tab -->
            <div v-if="activeTab === 'tasks'">
              <div v-if="tasks.length === 0" class="text-center py-8 text-textmuted">
                <i class="ri-task-line text-4xl mb-2"></i>
                <p>No tasks yet. Add one to get started.</p>
              </div>
              <ul v-else class="space-y-3">
                <li v-for="task in tasks" :key="task.id" class="flex items-center justify-between p-3 bg-light rounded-lg">
                  <div class="flex items-center gap-3">
                    <input type="checkbox" class="ti-form-check-input" :checked="task.status === 'completed'">
                    <div>
                      <span class="font-medium">{{ task.title }}</span>
                      <span class="block text-xs text-textmuted">{{ task.assignee || 'Unassigned' }}</span>
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

            <!-- Stakeholders Tab -->
            <div v-if="activeTab === 'stakeholders'">
              <div v-if="stakeholders.length === 0" class="text-center py-8 text-textmuted">
                <i class="ri-user-line text-4xl mb-2"></i>
                <p>No stakeholders added yet.</p>
                <Link :href="sectionLink('/initiation/stakeholders')" class="ti-btn ti-btn-primary ti-btn-sm mt-3">
                  <i class="ri-add-line me-1"></i> Add Stakeholder
                </Link>
              </div>
              <div v-else class="space-y-3">
                <div v-for="stakeholder in stakeholders" :key="stakeholder.id" class="p-4 bg-light rounded-lg">
                  <div class="flex justify-between items-start">
                    <div>
                      <h6 class="font-medium">{{ stakeholder.name }}</h6>
                      <p class="text-sm text-textmuted">{{ stakeholder.role }}</p>
                      <p class="text-xs text-textmuted mt-1">{{ stakeholder.email }}</p>
                    </div>
                    <div class="text-right">
                      <span class="badge bg-primary/10 text-primary text-xs">{{ stakeholder.influence }}</span>
                    </div>
                  </div>
                </div>
                <Link :href="sectionLink('/initiation/stakeholders')" class="ti-btn ti-btn-light ti-btn-sm w-full">
                  View All Stakeholders
                </Link>
              </div>
            </div>

            <!-- Risks Tab -->
            <div v-if="activeTab === 'risks'">
              <div v-if="risks.length === 0" class="text-center py-8 text-textmuted">
                <i class="ri-shield-cross-line text-4xl mb-2"></i>
                <p>No risks identified yet.</p>
                <Link :href="sectionLink('/quality/risks')" class="ti-btn ti-btn-primary ti-btn-sm mt-3">
                  <i class="ri-add-line me-1"></i> Add Risk
                </Link>
              </div>
              <div v-else class="space-y-3">
                <div v-for="risk in risks" :key="risk.id" class="p-4 bg-light rounded-lg">
                  <div class="flex justify-between items-start">
                    <div>
                      <h6 class="font-medium">{{ risk.title }}</h6>
                      <p class="text-sm text-textmuted mt-1">{{ risk.description }}</p>
                    </div>
                    <span class="badge" :class="{
                      'bg-danger/10 text-danger': risk.severity === 'high',
                      'bg-warning/10 text-warning': risk.severity === 'medium',
                      'bg-success/10 text-success': risk.severity === 'low'
                    }">
                      {{ risk.severity }}
                    </span>
                  </div>
                </div>
                <Link :href="sectionLink('/quality/risks')" class="ti-btn ti-btn-light ti-btn-sm w-full">
                  View All Risks
                </Link>
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
              <span>Started: {{ formatDate(project.startDate) }}</span>
              <span>Due: {{ formatDate(project.dueDate) }}</span>
            </div>
          </div>
        </div>

        <!-- Quick Stats -->
        <div class="box">
          <div class="box-header">
            <h5 class="box-title">Quick Stats</h5>
          </div>
          <div class="box-body">
            <div class="grid grid-cols-2 gap-3">
              <div class="text-center p-3 bg-primary/5 rounded">
                <div class="text-2xl font-bold text-primary">{{ stats.totalTasks || 0 }}</div>
                <div class="text-xs text-textmuted">Total Tasks</div>
              </div>
              <div class="text-center p-3 bg-success/5 rounded">
                <div class="text-2xl font-bold text-success">{{ stats.completedTasks || 0 }}</div>
                <div class="text-xs text-textmuted">Completed</div>
              </div>
              <div class="text-center p-3 bg-warning/5 rounded">
                <div class="text-2xl font-bold text-warning">{{ stats.openRisks || 0 }}</div>
                <div class="text-xs text-textmuted">Open Risks</div>
              </div>
              <div class="text-center p-3 bg-info/5 rounded">
                <div class="text-2xl font-bold text-info">{{ stats.stakeholdersCount || 0 }}</div>
                <div class="text-xs text-textmuted">Stakeholders</div>
              </div>
            </div>
          </div>
        </div>

        <!-- Project Sections Navigation -->
        <div class="box">
          <div class="box-header">
            <h5 class="box-title">Project Sections</h5>
          </div>
          <div class="box-body">
            <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
              <!-- Stakeholders Card -->
              <Link 
                :href="sectionLink('/initiation/stakeholders')"
                class="group p-3 md:p-4 bg-light dark:bg-bgdark rounded-lg border border-defaultborder hover:border-primary hover:bg-primary/5 transition-all duration-200 text-center"
              >
                <div class="mb-2">
                  <i class="ri-user-line text-2xl md:text-3xl text-primary group-hover:scale-110 transition-transform"></i>
                </div>
                <p class="text-xs md:text-sm font-medium text-defaulttextcolor">Stakeholders</p>
                <p class="text-[10px] md:text-xs text-textmuted mt-1">{{ stats.stakeholdersCount || 0 }} members</p>
              </Link>

              <!-- Resources Card -->
              <Link 
                :href="sectionLink('/resources/team')"
                class="group p-3 md:p-4 bg-light dark:bg-bgdark rounded-lg border border-defaultborder hover:border-primary hover:bg-primary/5 transition-all duration-200 text-center"
              >
                <div class="mb-2">
                  <i class="ri-team-line text-2xl md:text-3xl text-primary group-hover:scale-110 transition-transform"></i>
                </div>
                <p class="text-xs md:text-sm font-medium text-defaulttextcolor">Resources</p>
                <p class="text-[10px] md:text-xs text-textmuted mt-1">{{ stats.teamMembersCount || 0 }} team members</p>
              </Link>

              <!-- Risks Card -->
              <Link 
                :href="sectionLink('/quality/risks')"
                class="group p-3 md:p-4 bg-light dark:bg-bgdark rounded-lg border border-defaultborder hover:border-primary hover:bg-primary/5 transition-all duration-200 text-center"
              >
                <div class="mb-2">
                  <i class="ri-shield-cross-line text-2xl md:text-3xl text-primary group-hover:scale-110 transition-transform"></i>
                </div>
                <p class="text-xs md:text-sm font-medium text-defaulttextcolor">Risks</p>
                <p class="text-[10px] md:text-xs text-textmuted mt-1">{{ stats.openRisks || 0 }} open</p>
              </Link>

              <!-- Gantt Chart Card -->
              <Link 
                :href="sectionLink('/resources/gantt')"
                class="group p-3 md:p-4 bg-light dark:bg-bgdark rounded-lg border border-defaultborder hover:border-primary hover:bg-primary/5 transition-all duration-200 text-center"
              >
                <div class="mb-2">
                  <i class="ri-bar-chart-line text-2xl md:text-3xl text-primary group-hover:scale-110 transition-transform"></i>
                </div>
                <p class="text-xs md:text-sm font-medium text-defaulttextcolor">Gantt</p>
                <p class="text-[10px] md:text-xs text-textmuted mt-1">Timeline view</p>
              </Link>

              <!-- Reports Card -->
              <Link 
                :href="sectionLink('/reports/analytics')"
                class="group p-3 md:p-4 bg-light dark:bg-bgdark rounded-lg border border-defaultborder hover:border-primary hover:bg-primary/5 transition-all duration-200 text-center"
              >
                <div class="mb-2">
                  <i class="ri-file-chart-line text-2xl md:text-3xl text-primary group-hover:scale-110 transition-transform"></i>
                </div>
                <p class="text-xs md:text-sm font-medium text-defaulttextcolor">Reports</p>
                <p class="text-[10px] md:text-xs text-textmuted mt-1">{{ stats.documentsCount || 0 }} documents</p>
              </Link>
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
            <p v-if="newTask.errors.title" class="mt-1 text-xs text-danger">{{ newTask.errors.title }}</p>
          </div>

          <div>
            <label class="ti-form-label text-sm mb-1">Assignee</label>
            <select v-model="newTask.team_member_id" class="ti-form-select">
              <option value="">Unassigned</option>
              <option v-for="member in teamMembers" :key="member.id" :value="member.id">
                {{ member.name }}
              </option>
            </select>
          </div>

          <div>
            <label class="ti-form-label text-sm mb-1">Status</label>
            <select v-model="newTask.status" class="ti-form-select">
              <option value="pending">Pending</option>
              <option value="in-progress">In Progress</option>
              <option value="completed">Completed</option>
            </select>
          </div>

          <div>
            <label class="ti-form-label text-sm mb-1">Priority</label>
            <select v-model="newTask.priority" class="ti-form-select">
              <option value="low">Low</option>
              <option value="medium">Medium</option>
              <option value="high">High</option>
            </select>
          </div>
        </div>

        <div class="px-6 py-4 border-t border-defaultborder/60 flex justify-end gap-3 bg-light/40 dark:bg-bgdark/40 rounded-b-xl">
          <button class="ti-btn ti-btn-light" type="button" @click="closeAddTaskModal">
            Cancel
          </button>
          <button 
            class="ti-btn ti-btn-primary" 
            type="button"
            :disabled="newTask.processing || !newTask.title.trim()"
            @click="saveTask"
          >
            <span v-if="newTask.processing">Saving...</span>
            <span v-else>Save Task</span>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>
