<script setup>
import { ref } from 'vue'
import PageHeader from '@/components/ui/PageHeader.vue'

const sprints = ref([
  { id: 1, name: 'Sprint 12', startDate: '2024-12-02', endDate: '2024-12-15', status: 'active', points: 34, completed: 22 },
  { id: 2, name: 'Sprint 11', startDate: '2024-11-18', endDate: '2024-12-01', status: 'completed', points: 40, completed: 38 },
  { id: 3, name: 'Sprint 13', startDate: '2024-12-16', endDate: '2024-12-29', status: 'planned', points: 30, completed: 0 }
])

const currentSprint = ref({
  name: 'Sprint 12',
  goal: 'Complete user authentication and dashboard features',
  daysRemaining: 8,
  totalPoints: 34,
  completedPoints: 22,
  tasks: { total: 20, done: 13, inProgress: 5, todo: 2 }
})
</script>

<template>
  <div>
    <PageHeader title="Sprints" subtitle="Manage sprint cycles and iterations">
      <template #actions>
        <button class="ti-btn ti-btn-primary">
          <i class="ri-add-line me-1"></i> New Sprint
        </button>
      </template>
    </PageHeader>

    <div class="grid grid-cols-12 gap-6">
      <!-- Current Sprint Overview -->
      <div class="col-span-12 xl:col-span-4">
        <div class="box">
          <div class="box-header">
            <div class="flex items-center justify-between">
              <h5 class="box-title">{{ currentSprint.name }}</h5>
              <span class="badge bg-primary/10 text-primary">Active</span>
            </div>
          </div>
          <div class="box-body">
            <p class="text-textmuted mb-4">{{ currentSprint.goal }}</p>
            
            <div class="text-center mb-4">
              <span class="text-4xl font-bold text-primary">{{ currentSprint.daysRemaining }}</span>
              <span class="text-textmuted block">days remaining</span>
            </div>

            <div class="mb-4">
              <div class="flex justify-between text-sm mb-1">
                <span>Progress</span>
                <span>{{ Math.round((currentSprint.completedPoints / currentSprint.totalPoints) * 100) }}%</span>
              </div>
              <div class="progress progress-sm">
                <div class="progress-bar bg-primary" :style="{ width: (currentSprint.completedPoints / currentSprint.totalPoints) * 100 + '%' }"></div>
              </div>
              <div class="flex justify-between text-xs text-textmuted mt-1">
                <span>{{ currentSprint.completedPoints }} points</span>
                <span>{{ currentSprint.totalPoints }} points</span>
              </div>
            </div>

            <div class="grid grid-cols-3 gap-2 text-center">
              <div class="p-2 bg-warning/10 rounded">
                <span class="block text-lg font-bold text-warning">{{ currentSprint.tasks.todo }}</span>
                <span class="text-xs text-textmuted">To Do</span>
              </div>
              <div class="p-2 bg-primary/10 rounded">
                <span class="block text-lg font-bold text-primary">{{ currentSprint.tasks.inProgress }}</span>
                <span class="text-xs text-textmuted">In Progress</span>
              </div>
              <div class="p-2 bg-success/10 rounded">
                <span class="block text-lg font-bold text-success">{{ currentSprint.tasks.done }}</span>
                <span class="text-xs text-textmuted">Done</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Sprint List -->
      <div class="col-span-12 xl:col-span-8">
        <div class="box">
          <div class="box-header">
            <h5 class="box-title">All Sprints</h5>
          </div>
          <div class="box-body p-0">
            <table class="table table-hover whitespace-nowrap">
              <thead>
                <tr>
                  <th>Sprint</th>
                  <th>Duration</th>
                  <th>Status</th>
                  <th>Progress</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="sprint in sprints" :key="sprint.id">
                  <td class="font-medium">{{ sprint.name }}</td>
                  <td class="text-textmuted">{{ sprint.startDate }} - {{ sprint.endDate }}</td>
                  <td>
                    <span class="badge" :class="{
                      'bg-primary/10 text-primary': sprint.status === 'active',
                      'bg-success/10 text-success': sprint.status === 'completed',
                      'bg-secondary/10 text-secondary': sprint.status === 'planned'
                    }">{{ sprint.status }}</span>
                  </td>
                  <td>
                    <div class="flex items-center gap-2 min-w-[120px]">
                      <div class="progress progress-xs flex-1">
                        <div class="progress-bar bg-primary" :style="{ width: (sprint.completed / sprint.points) * 100 + '%' }"></div>
                      </div>
                      <span class="text-xs">{{ sprint.completed }}/{{ sprint.points }}</span>
                    </div>
                  </td>
                  <td>
                    <button class="ti-btn ti-btn-soft-primary ti-btn-sm">View</button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

