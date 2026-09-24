<script setup>
import { computed, onMounted, onUnmounted, ref } from 'vue'
import { router, useForm, usePage } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import Echo, { isRealtimeEnabled } from '@/realtime/echo'
import StatsCard from '@/Components/ui/StatsCard.vue'
import Card from '@/Components/ui/Card.vue'
import Badge from '@/Components/ui/Badge.vue'
import Button from '@/Components/ui/Button.vue'
import ProgressBar from '@/Components/ui/ProgressBar.vue'
import EmptyState from '@/Components/ui/EmptyState.vue'

defineOptions({ layout: AppLayout })

const props = defineProps({
  member: { type: Object, required: true },
  role: { type: String, required: true },
  roleContent: { type: Object, required: true },
  team: { type: Object, default: null },
  projects: { type: Array, default: () => [] },
  tasks: { type: Array, default: () => [] },
  metrics: { type: Object, required: true },
})

const page = usePage()
const permissions = computed(() => page.props.auth?.permissions || [])
const canUpdateTaskStatus = computed(() => permissions.value.includes('member.task.status.update'))
const canCompleteTask = computed(() => permissions.value.includes('member.task.complete'))

const logoutForm = useForm({})
const logout = () => logoutForm.post('/team-member/logout')
const completingTaskId = ref(null)
const realtimeChannels = []
const dashboardRefreshing = ref(false)
let refreshTimer = null

const refreshDashboard = () => {
  if (dashboardRefreshing.value) return

  dashboardRefreshing.value = true
  router.reload({
    only: ['projects', 'tasks', 'metrics'],
    preserveScroll: true,
    onFinish: () => {
      dashboardRefreshing.value = false
    },
  })
}

const subscribeToAssignedProjects = () => {
  if (!isRealtimeEnabled || !Echo || !props.projects.length) return false

  props.projects.forEach((project) => {
    const channel = Echo.private(`project.${project.id}`)
      .listen('.task.updated', refreshDashboard)
      .listen('.project.progress.updated', refreshDashboard)

    realtimeChannels.push(`project.${project.id}`)
    channel.error(() => {
      if (!refreshTimer) {
        refreshTimer = window.setInterval(refreshDashboard, 20000)
      }
    })
  })

  return true
}

onMounted(() => {
  const projectSubscribed = subscribeToAssignedProjects()

  if (!projectSubscribed) {
    refreshTimer = window.setInterval(refreshDashboard, 20000)
  }
})

onUnmounted(() => {
  realtimeChannels.forEach((channel) => Echo?.leave(channel))
  realtimeChannels.length = 0

  if (refreshTimer) {
    window.clearInterval(refreshTimer)
    refreshTimer = null
  }
})

const completeTask = (task) => {
  if (!canUpdateTaskStatus.value || !canCompleteTask.value) return

  completingTaskId.value = task.id
  router.patch(`/team-member/tasks/${task.id}/status`, { status: 'completed' }, {
    preserveScroll: true,
    onFinish: () => {
      completingTaskId.value = null
    },
  })
}

const taskMetrics = computed(() => [
  { 
    label: 'Assigned Tasks', 
    value: props.metrics.totalTasks ?? 0, 
    icon: 'ri-list-check-2',
    iconBg: 'bg-blue-600',
    badge: 'View all',
    badgeClass: 'badge-info'
  },
  { 
    label: 'Completed', 
    value: props.metrics.completedTasks ?? 0, 
    icon: 'ri-checkbox-circle-line',
    iconBg: 'bg-green-600',
    badge: `${Math.round((props.metrics.completedTasks / (props.metrics.totalTasks || 1)) * 100)}%`,
    badgeClass: 'badge-success',
    subtitle: 'Success rate'
  },
  { 
    label: 'In Progress', 
    value: props.metrics.inProgressTasks ?? 0, 
    icon: 'ri-loader-4-line',
    iconBg: 'bg-orange-600',
    badge: 'Active',
    badgeClass: 'badge-warning'
  },
  { 
    label: 'My Projects', 
    value: props.metrics.projects ?? 0, 
    icon: 'ri-folder-line',
    iconBg: 'bg-purple-600',
    badge: props.projects.length > 0 ? 'Assigned' : 'None',
    badgeClass: props.projects.length > 0 ? 'badge-primary' : 'badge-neutral'
  },
])

const statusVariant = (status) => {
  if (['completed', 'done'].includes(status)) return 'success'
  if (status === 'in-progress') return 'info'
  return 'warning'
}

const priorityColor = (priority) => {
  if (priority === 'high') return 'text-red-600 dark:text-red-400'
  if (priority === 'medium') return 'text-orange-600 dark:text-orange-400'
  return 'text-gray-600 dark:text-gray-400'
}
</script>

<template>
  <div>
    <!-- Welcome Header -->
    <div class="mb-8">
      <div class="flex items-center justify-between flex-wrap gap-4">
        <div>
          <div class="flex items-center gap-3 mb-3">
            <Badge variant="primary" size="md">
              <i class="ri-shield-user-line mr-1"></i>
              {{ role }}
            </Badge>
            <Badge v-if="team" variant="secondary" size="md">
              <i class="ri-team-line mr-1"></i>
              {{ team.name }}
            </Badge>
          </div>
          
          <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-50 tracking-tight mb-2">
            Welcome back, {{ member.name }} 👋
          </h1>
          <p class="text-gray-600 dark:text-gray-400 text-base">
            {{ roleContent.description }}
          </p>
        </div>
        
        <Button 
          variant="light" 
          size="md"
          :loading="logoutForm.processing"
          :disabled="logoutForm.processing"
          @click="logout"
        >
          <i class="ri-logout-box-r-line mr-2"></i>
          Sign out
        </Button>
      </div>
    </div>

    <!-- Metrics Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
      <StatsCard
        v-for="metric in taskMetrics"
        :key="metric.label"
        :title="metric.label"
        :value="metric.value"
        :icon="metric.icon"
        :icon-bg="metric.iconBg"
        :badge="metric.badge"
        :badge-class="metric.badgeClass"
        :subtitle="metric.subtitle"
      />
    </div>

    <!-- Tasks and Projects Grid -->
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
      <!-- My Tasks -->
      <Card 
        id="tasks"
        title="My Assigned Tasks" 
        :subtitle="`${tasks.length} task${tasks.length !== 1 ? 's' : ''} assigned`"
      >
        <template #actions>
          <Button variant="ghost" size="sm">
            <i class="ri-filter-line mr-1"></i>
            Filter
          </Button>
        </template>

        <div v-if="tasks.length" class="space-y-3">
          <div 
            v-for="task in tasks" 
            :key="task.id"
            class="p-4 rounded-xl border border-gray-200 dark:border-gray-700 hover:border-green-300 dark:hover:border-green-700 transition-all duration-200 group"
          >
            <div class="flex items-start justify-between gap-3 mb-3">
              <div class="flex-1 min-w-0">
                <h4 class="font-semibold text-gray-900 dark:text-gray-100 mb-1 group-hover:text-green-600 dark:group-hover:text-green-400 transition-colors">
                  {{ task.title }}
                </h4>
                <p class="text-sm text-gray-600 dark:text-gray-400 flex items-center gap-2">
                  <i class="ri-folder-3-line"></i>
                  <span class="truncate">{{ task.project || 'Unassigned Project' }}</span>
                </p>
              </div>
              
              <div class="flex items-center gap-2">
                <Badge :variant="statusVariant(task.status)" size="sm">
                  {{ task.status }}
                </Badge>
              </div>
            </div>
            
            <div class="flex items-center justify-between gap-3">
              <div class="flex items-center gap-3 text-sm text-gray-500 dark:text-gray-400">
                <span v-if="task.priority" class="flex items-center gap-1" :class="priorityColor(task.priority)">
                  <i class="ri-flag-line"></i>
                  {{ task.priority }}
                </span>
                <span v-if="task.due_date" class="flex items-center gap-1">
                  <i class="ri-calendar-line"></i>
                  {{ task.due_date }}
                </span>
              </div>
              
              <Button
                v-if="canUpdateTaskStatus && canCompleteTask && !['completed', 'done'].includes(task.status)"
                variant="success"
                size="sm"
                :loading="completingTaskId === task.id"
                :disabled="completingTaskId === task.id"
                @click="completeTask(task)"
              >
                <i class="ri-check-line mr-1"></i>
                Complete
              </Button>
            </div>
          </div>
        </div>
        
        <EmptyState
          v-else
          icon="ri-task-line"
          title="No tasks assigned"
          description="You don't have any tasks assigned at the moment. Check back later or contact your project manager."
        />
      </Card>

      <!-- My Projects -->
      <Card 
        id="projects"
        title="My Projects" 
        :subtitle="`${projects.length} project${projects.length !== 1 ? 's' : ''} assigned`"
      >
        <template #actions>
          <Button variant="ghost" size="sm">
            <i class="ri-eye-line mr-1"></i>
            View All
          </Button>
        </template>

        <div v-if="projects.length" class="space-y-4">
          <div 
            v-for="project in projects" 
            :key="project.id"
            class="p-4 rounded-xl border border-gray-200 dark:border-gray-700 hover:border-green-300 dark:hover:border-green-700 transition-all duration-200"
          >
            <div class="flex items-center justify-between mb-3">
              <div class="flex-1 min-w-0">
                <h4 class="font-semibold text-gray-900 dark:text-gray-100 mb-1 truncate">
                  {{ project.name }}
                </h4>
                <p v-if="project.description" class="text-sm text-gray-600 dark:text-gray-400 line-clamp-1">
                  {{ project.description }}
                </p>
              </div>
              <Badge variant="primary" size="sm" dot>
                Active
              </Badge>
            </div>
            
            <ProgressBar 
              :value="project.progress || 0" 
              show-label
              size="md"
            >
              <template #label>
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Progress</span>
              </template>
            </ProgressBar>
            
            <div class="mt-3 flex items-center gap-4 text-sm text-gray-500 dark:text-gray-400">
              <span v-if="project.start_date" class="flex items-center gap-1">
                <i class="ri-calendar-line"></i>
                Started {{ project.start_date }}
              </span>
              <span v-if="project.team_count" class="flex items-center gap-1">
                <i class="ri-team-line"></i>
                {{ project.team_count }} members
              </span>
            </div>
          </div>
        </div>
        
        <EmptyState
          v-else
          icon="ri-folder-open-line"
          title="No projects assigned"
          description="You haven't been assigned to any projects yet. Your project manager will assign you when needed."
        />
      </Card>
    </div>
  </div>
</template>

<style scoped>
/* Fade in animation for dashboard elements */
.box,
.grid > * {
  animation: fadeInUp 0.5s ease-out;
}

@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* Stagger animation */
.grid > *:nth-child(1) { animation-delay: 0s; }
.grid > *:nth-child(2) { animation-delay: 0.1s; }
.grid > *:nth-child(3) { animation-delay: 0.2s; }
.grid > *:nth-child(4) { animation-delay: 0.3s; }

/* Line clamp utility */
.line-clamp-1 {
  display: -webkit-box;
  -webkit-line-clamp: 1;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
