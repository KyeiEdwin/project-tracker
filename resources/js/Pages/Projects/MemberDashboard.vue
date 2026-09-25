<script setup>
import { computed } from 'vue'
import { Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import { useProgressColor } from '@/composables/useProgressColor'

const props = defineProps({
  project: { type: Object, required: true },
  dashboard: {
    type: Object,
    required: true,
    default: () => ({
      progress: 0,
      tasks: { total: 0, completed: 0, inProgress: 0 },
      sprint: null,
      teamMembers: 0,
      risks: 0,
      openIssues: 0,
      tasksList: [],
    }),
  },
})

defineOptions({ layout: AppLayout })

const { getProgressColorClass, getProgressTextClass } = useProgressColor()

const taskMetrics = computed(() => [
  { label: 'Tasks', value: props.dashboard.tasks?.total ?? 0, icon: 'ri-list-check-2', tone: 'primary' },
  { label: 'Completed', value: props.dashboard.tasks?.completed ?? 0, icon: 'ri-checkbox-circle-line', tone: 'success' },
  { label: 'In Progress', value: props.dashboard.tasks?.inProgress ?? 0, icon: 'ri-loader-4-line', tone: 'warning' },
])

const overviewMetrics = computed(() => [
  { label: 'Sprint', value: props.dashboard.sprint?.name || 'No active sprint', icon: 'ri-git-branch-line' },
  { label: 'Team Members', value: props.dashboard.teamMembers ?? 0, icon: 'ri-team-line' },
  { label: 'Risks', value: props.dashboard.risks ?? 0, icon: 'ri-error-warning-line' },
  { label: 'Open Issues', value: props.dashboard.openIssues ?? 0, icon: 'ri-alert-line' },
])

</script>

<template>
  <div>
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between mb-6">
      <div>
        <p class="text-sm text-textmuted mb-1">Project dashboard</p>
        <h1 class="text-2xl font-semibold">{{ project.name }}</h1>
      </div>
      <Link :href="`/projects/${project.id}`" class="ti-btn ti-btn-light">
        <i class="ri-arrow-left-line me-1"></i> Project details
      </Link>
    </div>

    <div class="box mb-6">
      <div class="box-body">
        <div class="flex items-center justify-between mb-2">
          <span class="font-medium">Project progress</span>
          <span :class="['font-semibold text-lg', getProgressTextClass(dashboard.progress)]">
            {{ dashboard.progress }}%
          </span>
        </div>
        <div class="progress h-3" role="progressbar" :aria-valuenow="dashboard.progress" aria-valuemin="0" aria-valuemax="100">
          <div 
            class="progress-bar" 
            :class="getProgressColorClass(dashboard.progress)"
            :style="{ width: `${dashboard.progress}%` }"
          ></div>
        </div>
      </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
      <div v-for="metric in taskMetrics" :key="metric.label" class="box">
        <div class="box-body flex items-center gap-3">
          <span class="w-10 h-10 rounded-full flex items-center justify-center bg-primary/10 text-primary">
            <i :class="`${metric.icon} text-xl`"></i>
          </span>
          <div>
            <p class="text-textmuted text-sm">{{ metric.label }}</p>
            <p class="text-2xl font-semibold">{{ metric.value }}</p>
          </div>
        </div>
      </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-5 gap-4 mb-6">
      <div v-for="metric in overviewMetrics" :key="metric.label" class="box">
        <div class="box-body">
          <div class="flex items-center gap-2 text-textmuted text-sm mb-2">
            <i :class="`${metric.icon} text-lg`"></i>
            <span>{{ metric.label }}</span>
          </div>
          <p class="font-semibold truncate" :title="String(metric.value)">{{ metric.value }}</p>
        </div>
      </div>
    </div>

    <div class="grid grid-cols-1 gap-6">
      <div class="box">
        <div class="box-header">
          <h2 class="box-title">Assigned tasks</h2>
        </div>
        <div class="box-body">
          <div v-if="dashboard.tasksList?.length" class="divide-y">
            <div v-for="task in dashboard.tasksList.slice(0, 5)" :key="task.id" class="py-2 flex items-center justify-between gap-3">
              <span class="truncate">{{ task.title }}</span>
              <span class="badge bg-secondary/10 text-secondary whitespace-nowrap">{{ task.status }}</span>
            </div>
          </div>
          <p v-else class="text-textmuted">No tasks assigned to this project.</p>
        </div>
      </div>
    </div>
  </div>
</template>
