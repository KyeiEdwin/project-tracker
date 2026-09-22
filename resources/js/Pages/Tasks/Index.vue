<script setup>
import { computed, ref } from 'vue'
import { router, useForm } from '@inertiajs/vue3'
import PageHeader from '@/components/ui/PageHeader.vue'
import { useActionFeedback } from '@/composables/useActionFeedback'

const props = defineProps({
  tasks: { type: Array, default: () => [] },
  projects: { type: Array, default: () => [] },
  teamMembers: { type: Array, default: () => [] },
})

const { notify } = useActionFeedback()

const searchQuery = ref('')
const statusFilter = ref('all')
const modal = ref(null)
const selected = ref(null)

const form = useForm({
  project_id: '',
  team_member_id: '',
  title: '',
  status: 'pending',
  priority: 'medium',
  due_date: '',
  description: '',
})

const filteredTasks = computed(() => props.tasks.filter(t =>
  (`${t.title} ${t.project ?? ''} ${t.assignee ?? ''}`).toLowerCase().includes(searchQuery.value.toLowerCase())
  && (statusFilter.value === 'all' || t.status === statusFilter.value)
))

const statusClass = s => ({ completed: 'bg-success/10 text-success', done: 'bg-success/10 text-success', 'in-progress': 'bg-primary/10 text-primary', pending: 'bg-warning/10 text-warning', todo: 'bg-warning/10 text-warning', backlog: 'bg-secondary/10 text-secondary' })[s] ?? 'bg-secondary/10 text-secondary'
const priorityClass = p => ({ critical: 'bg-danger/10 text-danger', high: 'bg-danger/10 text-danger', medium: 'bg-warning/10 text-warning', low: 'bg-success/10 text-success' })[p] ?? 'bg-secondary/10 text-secondary'

const openNew = () => {
  selected.value = null
  form.clearErrors()
  form.defaults({ project_id: '', team_member_id: '', title: '', status: 'pending', priority: 'medium', due_date: '', description: '' })
  form.reset()
  modal.value = 'edit'
}

const openEdit = task => {
  selected.value = task
  form.clearErrors()
  form.defaults({
    project_id: task.projectId ?? '',
    team_member_id: task.teamMemberId ?? '',
    title: task.title ?? '',
    status: task.status ?? 'pending',
    priority: task.priority ?? 'medium',
    due_date: task.dueDate ?? '',
    description: task.description ?? '',
  })
  form.reset()
  modal.value = 'edit'
}

const save = () => {
  const options = {
    preserveScroll: true,
    onSuccess: () => { modal.value = null; notify(selected.value ? 'Task updated.' : 'Task created.') },
  }
  if (selected.value) form.put(`/tasks/${selected.value.id}`, options)
  else form.post('/tasks', options)
}

const remove = task => {
  router.delete(`/tasks/${task.id}`, {
    preserveScroll: true,
    onSuccess: () => { modal.value = null; notify('Task deleted.') },
  })
}

const changeStatus = task => {
  const statuses = ['pending', 'in-progress', 'completed']
  const next = statuses[(statuses.indexOf(task.status) + 1) % statuses.length]
  router.put(`/tasks/${task.id}`, {
    project_id: task.projectId, team_member_id: task.teamMemberId, title: task.title,
    status: next, priority: task.priority, due_date: task.dueDate, description: task.description,
  }, { preserveScroll: true, onSuccess: () => notify(`Task moved to ${next.replace('-', ' ')}.`) })
}
</script>

<template>
  <div>
    <PageHeader title="Task List" subtitle="Manage all tasks across projects">
      <template #actions>
        <button class="ti-btn ti-btn-primary" data-feedback-handled="true" @click="openNew"><i class="ri-add-line me-1"></i> New Task</button>
      </template>
    </PageHeader>
    <div class="box">
      <div class="box-header flex flex-wrap items-center justify-between gap-4">
        <div class="flex items-center gap-3">
          <div class="relative">
            <input v-model="searchQuery" class="ti-form-control !ps-10" placeholder="Search tasks...">
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
            <thead><tr><th>Task</th><th>Project</th><th>Assignee</th><th>Status</th><th>Priority</th><th>Due Date</th><th>Actions</th></tr></thead>
            <tbody>
              <tr v-if="!filteredTasks.length"><td colspan="7" class="py-8 text-center text-textmuted">No tasks found.</td></tr>
              <tr v-for="task in filteredTasks" :key="task.id">
                <td class="font-medium">{{ task.title }}</td>
                <td class="text-textmuted">{{ task.project ?? 'â€”' }}</td>
                <td>{{ task.assignee ?? 'â€”' }}</td>
                <td><button class="badge" :class="statusClass(task.status)" data-feedback-handled="true" @click="changeStatus(task)">{{ task.status }}</button></td>
                <td><span class="badge" :class="priorityClass(task.priority)">{{ task.priority }}</span></td>
                <td>{{ task.dueDate ?? 'â€”' }}</td>
                <td>
                  <div class="flex gap-1">
                    <button class="ti-btn ti-btn-soft-primary ti-btn-icon ti-btn-sm" data-feedback-handled="true" aria-label="View task" @click="selected=task;modal='view'"><i class="ri-eye-line"></i></button>
                    <button class="ti-btn ti-btn-soft-info ti-btn-icon ti-btn-sm" data-feedback-handled="true" aria-label="Edit task" @click="openEdit(task)"><i class="ri-edit-line"></i></button>
                    <button class="ti-btn ti-btn-soft-danger ti-btn-icon ti-btn-sm" data-feedback-handled="true" aria-label="Delete task" @click="selected=task;modal='delete'"><i class="ri-delete-bin-line"></i></button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <div v-if="modal" class="fixed inset-0 z-[80] flex items-center justify-center bg-black/40" @click.self="modal=null">
      <div class="bg-white dark:bg-bgdark rounded-xl shadow-xl w-full max-w-md mx-4">
        <div class="px-6 py-4 border-b flex justify-between">
          <h3 class="font-semibold">{{ modal==='edit' ? (selected ? 'Edit Task' : 'New Task') : modal==='delete' ? 'Delete Task' : 'Task details' }}</h3>
          <button class="ti-btn ti-btn-sm ti-btn-light" :disabled="form.processing" @click="modal=null">Close</button>
        </div>
        <div class="p-6" v-if="modal==='edit'">
          <label class="ti-form-label">Title</label>
          <input v-model="form.title" class="ti-form-control mb-1">
          <span v-if="form.errors.title" class="mb-2 block text-xs text-danger">{{ form.errors.title }}</span>

          <label class="ti-form-label">Project</label>
          <select v-model="form.project_id" class="ti-form-select mb-1">
            <option value="">Select project</option>
            <option v-for="p in projects" :key="p.id" :value="p.id">{{ p.name }}</option>
          </select>
          <span v-if="form.errors.project_id" class="mb-2 block text-xs text-danger">{{ form.errors.project_id }}</span>

          <label class="ti-form-label">Assignee</label>
          <select v-model="form.team_member_id" class="ti-form-select mb-3">
            <option value="">Unassigned</option>
            <option v-for="m in teamMembers" :key="m.id" :value="m.id">{{ m.name }}</option>
          </select>

          <label class="ti-form-label">Status</label>
          <select v-model="form.status" class="ti-form-select mb-3">
            <option value="pending">Pending</option>
            <option value="in-progress">In Progress</option>
            <option value="completed">Completed</option>
          </select>

          <label class="ti-form-label">Priority</label>
          <select v-model="form.priority" class="ti-form-select mb-3">
            <option value="low">Low</option>
            <option value="medium">Medium</option>
            <option value="high">High</option>
            <option value="critical">Critical</option>
          </select>

          <label class="ti-form-label">Due date</label>
          <input v-model="form.due_date" type="date" class="ti-form-control mb-3">

          <label class="ti-form-label">Description</label>
          <textarea v-model="form.description" class="ti-form-control" rows="3"></textarea>
        </div>
        <div class="p-6" v-else-if="modal==='view'">
          <p><b>{{ selected.title }}</b></p>
          <p class="text-textmuted">{{ selected.project ?? 'â€”' }} Â· {{ selected.assignee ?? 'Unassigned' }} Â· Due {{ selected.dueDate ?? 'â€”' }}</p>
        </div>
        <div class="p-6" v-else>Delete <b>{{ selected.title }}</b>? This cannot be undone.</div>
        <div class="px-6 py-4 border-t flex justify-end gap-2">
          <button class="ti-btn ti-btn-light" :disabled="form.processing" @click="modal=null">Cancel</button>
          <button v-if="modal==='edit'" class="ti-btn ti-btn-primary" :disabled="form.processing" @click="save">{{ form.processing ? 'Savingâ€¦' : 'Save Task' }}</button>
          <button v-if="modal==='delete'" class="ti-btn ti-btn-danger" @click="remove(selected)">Delete</button>
        </div>
      </div>
    </div>
  </div>
</template>
