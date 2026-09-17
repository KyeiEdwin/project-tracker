<script setup>
import { ref } from 'vue'
import PageHeader from '@/components/ui/PageHeader.vue'

const workflows = ref([
  { id: 1, name: 'Bug Fix Workflow', stages: ['Reported', 'Triaged', 'In Progress', 'Code Review', 'QA', 'Resolved'], projects: 5 },
  { id: 2, name: 'Feature Development', stages: ['Backlog', 'Design', 'Development', 'Testing', 'Deployment', 'Done'], projects: 8 },
  { id: 3, name: 'Sprint Workflow', stages: ['Sprint Backlog', 'In Progress', 'Review', 'Done'], projects: 12 }
])
</script>

<template>
  <div>
    <PageHeader title="Workflows" subtitle="Manage task workflows and stages">
      <template #actions>
        <button class="ti-btn ti-btn-primary">
          <i class="ri-add-line me-1"></i> New Workflow
        </button>
      </template>
    </PageHeader>

    <div class="grid grid-cols-12 gap-6">
      <div v-for="workflow in workflows" :key="workflow.id" class="col-span-12 lg:col-span-6 xl:col-span-4">
        <div class="box">
          <div class="box-header flex items-center justify-between">
            <h5 class="box-title">{{ workflow.name }}</h5>
            <div class="ti-dropdown hs-dropdown">
              <button class="ti-btn ti-btn-sm ti-btn-light"><i class="ri-more-2-fill"></i></button>
            </div>
          </div>
          <div class="box-body">
            <div class="flex flex-wrap gap-2 mb-4">
              <span v-for="(stage, idx) in workflow.stages" :key="idx" class="badge bg-light text-defaulttextcolor">
                {{ stage }}
                <i v-if="idx < workflow.stages.length - 1" class="ri-arrow-right-s-line ms-1"></i>
              </span>
            </div>
            <div class="text-sm text-textmuted">
              <i class="ri-folder-line me-1"></i> Used in {{ workflow.projects }} projects
            </div>
          </div>
          <div class="box-footer">
            <button class="ti-btn ti-btn-soft-primary ti-btn-sm w-full">Edit Workflow</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

