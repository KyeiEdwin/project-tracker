<script setup>
import { ref } from 'vue'
import PageHeader from '@/components/ui/PageHeader.vue'

const columns = ref([
  {
    id: 'backlog',
    title: 'Backlog',
    color: 'secondary',
    tasks: [
      { id: 1, title: 'Research competitors', assignee: 'John Doe', priority: 'low' },
      { id: 2, title: 'Define requirements', assignee: 'Jane Smith', priority: 'medium' }
    ]
  },
  {
    id: 'todo',
    title: 'To Do',
    color: 'warning',
    tasks: [
      { id: 3, title: 'Design wireframes', assignee: 'Mike Johnson', priority: 'high' },
      { id: 4, title: 'Setup project structure', assignee: 'Sarah Wilson', priority: 'medium' }
    ]
  },
  {
    id: 'in-progress',
    title: 'In Progress',
    color: 'primary',
    tasks: [
      { id: 5, title: 'Implement authentication', assignee: 'John Doe', priority: 'high' },
      { id: 6, title: 'Build dashboard UI', assignee: 'Jane Smith', priority: 'medium' }
    ]
  },
  {
    id: 'done',
    title: 'Done',
    color: 'success',
    tasks: [
      { id: 7, title: 'Project kickoff meeting', assignee: 'Mike Johnson', priority: 'high' },
      { id: 8, title: 'Environment setup', assignee: 'Sarah Wilson', priority: 'low' }
    ]
  }
])

const getPriorityClass = (priority) => ({
  'high': 'bg-danger/10 text-danger',
  'medium': 'bg-warning/10 text-warning',
  'low': 'bg-success/10 text-success'
})[priority]
</script>

<template>
  <div>
    <PageHeader title="Kanban Board" subtitle="Visualize your workflow">
      <template #actions>
        <button class="ti-btn ti-btn-primary">
          <i class="ri-add-line me-1"></i> Add Task
        </button>
      </template>
    </PageHeader>

    <div class="flex gap-6 overflow-x-auto pb-4">
      <div v-for="column in columns" :key="column.id" class="flex-shrink-0 w-80">
        <div class="box h-full">
          <div class="box-header flex items-center justify-between">
            <div class="flex items-center gap-2">
              <span class="w-3 h-3 rounded-full" :class="`bg-${column.color}`"></span>
              <h6 class="box-title mb-0">{{ column.title }}</h6>
            </div>
            <span class="badge bg-light text-defaulttextcolor">{{ column.tasks.length }}</span>
          </div>
          <div class="box-body space-y-3 min-h-[400px] bg-light/50">
            <div 
              v-for="task in column.tasks" 
              :key="task.id"
              class="bg-white p-3 rounded-lg shadow-sm border cursor-move hover:shadow-md transition-shadow"
            >
              <h6 class="font-medium mb-2">{{ task.title }}</h6>
              <div class="flex items-center justify-between text-sm">
                <span class="text-textmuted">{{ task.assignee }}</span>
                <span class="badge" :class="getPriorityClass(task.priority)">{{ task.priority }}</span>
              </div>
            </div>
            <button class="w-full py-2 border-2 border-dashed rounded-lg text-textmuted hover:text-primary hover:border-primary transition-colors">
              <i class="ri-add-line me-1"></i> Add Task
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

