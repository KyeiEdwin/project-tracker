<script setup>
import { ref, computed } from 'vue'
import PageHeader from '@/components/ui/PageHeader.vue'

const searchQuery = ref('')
const statusFilter = ref('all')

const tasks = ref([
  { id: 1, title: 'Design homepage mockup', project: 'Website Redesign', assignee: 'John Doe', status: 'completed', priority: 'high', dueDate: '2024-12-05' },
  { id: 2, title: 'Implement user authentication', project: 'Mobile App', assignee: 'Jane Smith', status: 'in-progress', priority: 'high', dueDate: '2024-12-08' },
  { id: 3, title: 'Write API documentation', project: 'CRM Integration', assignee: 'Mike Johnson', status: 'pending', priority: 'medium', dueDate: '2024-12-10' },
  { id: 4, title: 'Fix responsive issues', project: 'Website Redesign', assignee: 'Sarah Wilson', status: 'in-progress', priority: 'low', dueDate: '2024-12-12' },
  { id: 5, title: 'Database optimization', project: 'Data Migration', assignee: 'John Doe', status: 'pending', priority: 'high', dueDate: '2024-12-15' }
])

const filteredTasks = computed(() => {
  return tasks.value.filter(task => {
    const matchesSearch = task.title.toLowerCase().includes(searchQuery.value.toLowerCase())
    const matchesStatus = statusFilter.value === 'all' || task.status === statusFilter.value
    return matchesSearch && matchesStatus
  })
})

const getStatusClass = (status) => ({
  'completed': 'bg-success/10 text-success',
  'in-progress': 'bg-primary/10 text-primary',
  'pending': 'bg-warning/10 text-warning'
})[status] || 'bg-secondary/10 text-secondary'

const getPriorityClass = (priority) => ({
  'high': 'bg-danger/10 text-danger',
  'medium': 'bg-warning/10 text-warning',
  'low': 'bg-success/10 text-success'
})[priority]
</script>

<template>
  <div>
    <PageHeader title="Task List" subtitle="Manage all tasks across projects">
      <template #actions>
        <button class="ti-btn ti-btn-primary">
          <i class="ri-add-line me-1"></i> New Task
        </button>
      </template>
    </PageHeader>

    <div class="box">
      <div class="box-header flex flex-wrap items-center justify-between gap-4">
        <div class="flex items-center gap-3">
          <div class="relative">
            <input v-model="searchQuery" type="text" class="ti-form-control !ps-10" placeholder="Search tasks...">
            <i class="ri-search-line absolute start-3 top-1/2 -translate-y-1/2 text-textmuted"></i>
          </div>
          <select v-model="statusFilter" class="ti-form-select w-auto">
            <option value="all">All Status</option>
            <option value="pending">Pending</option>
            <option value="in-progress">In Progress</option>
            <option value="completed">Completed</option>
          </select>
        </div>
      </div>

      <div class="box-body p-0">
        <div class="table-responsive">
          <table class="table table-hover whitespace-nowrap">
            <thead>
              <tr>
                <th><input type="checkbox" class="ti-form-check-input"></th>
                <th>Task</th>
                <th>Project</th>
                <th>Assignee</th>
                <th>Status</th>
                <th>Priority</th>
                <th>Due Date</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="task in filteredTasks" :key="task.id">
                <td><input type="checkbox" class="ti-form-check-input"></td>
                <td class="font-medium">{{ task.title }}</td>
                <td class="text-textmuted">{{ task.project }}</td>
                <td>{{ task.assignee }}</td>
                <td><span class="badge" :class="getStatusClass(task.status)">{{ task.status }}</span></td>
                <td><span class="badge" :class="getPriorityClass(task.priority)">{{ task.priority }}</span></td>
                <td>{{ task.dueDate }}</td>
                <td>
                  <div class="flex gap-1">
                    <button class="ti-btn ti-btn-soft-primary ti-btn-icon ti-btn-sm"><i class="ri-eye-line"></i></button>
                    <button class="ti-btn ti-btn-soft-info ti-btn-icon ti-btn-sm"><i class="ri-edit-line"></i></button>
                    <button class="ti-btn ti-btn-soft-danger ti-btn-icon ti-btn-sm"><i class="ri-delete-bin-line"></i></button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</template>

