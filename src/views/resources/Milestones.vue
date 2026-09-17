<script setup>
import { ref } from 'vue'
import PageHeader from '@/components/ui/PageHeader.vue'

const milestones = ref([
  { id: 1, name: 'Project Kickoff', date: '2024-10-01', status: 'completed', project: 'Website Redesign' },
  { id: 2, name: 'Design Phase Complete', date: '2024-10-30', status: 'completed', project: 'Website Redesign' },
  { id: 3, name: 'MVP Release', date: '2024-11-30', status: 'in-progress', project: 'Mobile App' },
  { id: 4, name: 'Beta Testing', date: '2024-12-15', status: 'upcoming', project: 'Website Redesign' },
  { id: 5, name: 'Production Launch', date: '2024-12-31', status: 'upcoming', project: 'Website Redesign' }
])
</script>

<template>
  <div>
    <PageHeader title="Milestones" subtitle="Track project milestones and deliverables">
      <template #actions>
        <button class="ti-btn ti-btn-primary">
          <i class="ri-add-line me-1"></i> Add Milestone
        </button>
      </template>
    </PageHeader>

    <div class="box">
      <div class="box-body">
        <div class="relative">
          <!-- Timeline -->
          <div class="absolute left-8 top-0 bottom-0 w-0.5 bg-gray-200 dark:bg-gray-700"></div>
          
          <div class="space-y-8">
            <div v-for="milestone in milestones" :key="milestone.id" class="relative flex items-start gap-4 pl-8">
              <span class="absolute left-6 w-4 h-4 rounded-full border-2 border-white" :class="{
                'bg-success': milestone.status === 'completed',
                'bg-primary': milestone.status === 'in-progress',
                'bg-gray-300': milestone.status === 'upcoming'
              }"></span>
              <div class="flex-1 p-4 bg-light rounded-lg">
                <div class="flex items-center justify-between mb-2">
                  <h6 class="font-medium">{{ milestone.name }}</h6>
                  <span class="badge" :class="{
                    'bg-success/10 text-success': milestone.status === 'completed',
                    'bg-primary/10 text-primary': milestone.status === 'in-progress',
                    'bg-secondary/10 text-secondary': milestone.status === 'upcoming'
                  }">{{ milestone.status }}</span>
                </div>
                <div class="flex items-center gap-4 text-sm text-textmuted">
                  <span><i class="ri-calendar-line me-1"></i>{{ milestone.date }}</span>
                  <span><i class="ri-folder-line me-1"></i>{{ milestone.project }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

