<script setup>
import { computed, ref } from 'vue'
import { router, useForm } from '@inertiajs/vue3'
import PageHeader from '@/components/ui/PageHeader.vue'
import { useActionFeedback } from '@/composables/useActionFeedback'

const props = defineProps({
  testCases: { type: Array, default: () => [] },
  stats: { type: Object, default: () => ({ total: 0, passed: 0, failed: 0, pending: 0 }) },
  projects: { type: Array, default: () => [] },
})

const { notify } = useActionFeedback()

const searchQuery = ref('')
const statusFilter = ref('all')
const modal = ref(null)
const selected = ref(null)

const form = useForm({
  project_id: '', name: '', type: 'functional', status: 'pending', priority: 'medium', owner: '', notes: '',
})

const filteredTests = computed(() => props.testCases.filter(t =>
  (`${t.name} ${t.project ?? ''} ${t.owner ?? ''}`).toLowerCase().includes(searchQuery.value.toLowerCase())
  && (statusFilter.value === 'all' || t.status === statusFilter.value)
))

const statusClass = s => ({ passed: 'bg-success/10 text-success', failed: 'bg-danger/10 text-danger', pending: 'bg-warning/10 text-warning', blocked: 'bg-secondary/10 text-secondary' })[s] ?? 'bg-secondary/10 text-secondary'

const openNew = () => {
  selected.value = null
  form.clearErrors()
  form.defaults({ project_id: '', name: '', type: 'functional', status: 'pending', priority: 'medium', owner: '', notes: '' })
  form.reset()
  modal.value = 'edit'
}

const edit = test => {
  selected.value = test
  form.clearErrors()
  form.defaults({
    project_id: test.projectId ?? '', name: test.name ?? '', type: test.type ?? 'functional',
    status: test.status ?? 'pending', priority: test.priority ?? 'medium', owner: test.owner ?? '', notes: test.notes ?? '',
  })
  form.reset()
  modal.value = 'edit'
}

const save = () => {
  const options = {
    preserveScroll: true,
    onSuccess: () => { modal.value = null; notify(selected.value ? 'Test case updated.' : 'Test case created.') },
  }
  if (selected.value) form.put(`/qa-tests/${selected.value.id}`, options)
  else form.post('/qa-tests', options)
}

const remove = test => {
  router.delete(`/qa-tests/${test.id}`, {
    preserveScroll: true,
    onSuccess: () => { modal.value = null; notify('Test case deleted.') },
  })
}
</script>

<template>
  <div>
    <PageHeader title="QA & Testing" subtitle="Track test cases and quality status">
      <template #actions>
        <button class="ti-btn ti-btn-primary" data-feedback-handled="true" @click="openNew"><i class="ri-add-line me-1"></i> New Test Case</button>
      </template>
    </PageHeader>

    <div class="grid grid-cols-12 gap-6 mb-6">
      <div class="col-span-6 md:col-span-3 box p-4"><p class="text-textmuted text-sm">Total</p><h4>{{ stats.total }}</h4></div>
      <div class="col-span-6 md:col-span-3 box p-4"><p class="text-textmuted text-sm">Passed</p><h4 class="text-success">{{ stats.passed }}</h4></div>
      <div class="col-span-6 md:col-span-3 box p-4"><p class="text-textmuted text-sm">Failed</p><h4 class="text-danger">{{ stats.failed }}</h4></div>
      <div class="col-span-6 md:col-span-3 box p-4"><p class="text-textmuted text-sm">Pending</p><h4 class="text-warning">{{ stats.pending }}</h4></div>
    </div>

    <div class="box">
      <div class="box-header flex flex-wrap items-center justify-between gap-4">
        <div class="flex items-center gap-3">
          <div class="relative">
            <input v-model="searchQuery" class="ti-form-control !ps-10" placeholder="Search test cases...">
            <i class="ri-search-line absolute start-3 top-1/2 -translate-y-1/2 text-textmuted"></i>
          </div>
          <select v-model="statusFilter" class="ti-form-select w-auto">
            <option value="all">All Status</option>
            <option value="passed">Passed</option>
            <option value="failed">Failed</option>
            <option value="pending">Pending</option>
            <option value="blocked">Blocked</option>
          </select>
        </div>
      </div>
      <div class="box-body p-0">
        <div class="table-responsive">
          <table class="table table-hover whitespace-nowrap">
            <thead><tr><th>Test Case</th><th>Project</th><th>Type</th><th>Owner</th><th>Status</th><th>Priority</th><th>Last Run</th><th>Actions</th></tr></thead>
            <tbody>
              <tr v-if="!filteredTests.length"><td colspan="8" class="py-8 text-center text-textmuted">No test cases found.</td></tr>
              <tr v-for="test in filteredTests" :key="test.id">
                <td class="font-medium">{{ test.name }}</td>
                <td class="text-textmuted">{{ test.project ?? 'â€”' }}</td>
                <td>{{ test.type }}</td>
                <td>{{ test.owner ?? 'â€”' }}</td>
                <td><span class="badge" :class="statusClass(test.status)">{{ test.status }}</span></td>
                <td>{{ test.priority }}</td>
                <td>{{ test.lastRun }}</td>
                <td>
                  <div class="flex gap-1">
                    <button class="ti-btn ti-btn-soft-primary ti-btn-icon ti-btn-sm" data-feedback-handled="true" aria-label="View test case" @click="selected=test;modal='view'"><i class="ri-eye-line"></i></button>
                    <button class="ti-btn ti-btn-soft-info ti-btn-icon ti-btn-sm" data-feedback-handled="true" aria-label="Edit test case" @click="edit(test)"><i class="ri-edit-line"></i></button>
                    <button class="ti-btn ti-btn-soft-danger ti-btn-icon ti-btn-sm" data-feedback-handled="true" aria-label="Delete test case" @click="selected=test;modal='delete'"><i class="ri-delete-bin-line"></i></button>
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
          <h3 class="font-semibold">{{ modal==='edit' ? (selected ? 'Edit Test Case' : 'New Test Case') : modal==='view' ? 'Test case details' : 'Delete Test Case' }}</h3>
          <button class="ti-btn ti-btn-sm ti-btn-light" :disabled="form.processing" @click="modal=null">Close</button>
        </div>
        <div v-if="modal==='edit'" class="p-6">
          <label class="ti-form-label">Name</label>
          <input v-model="form.name" class="ti-form-control mb-1">
          <span v-if="form.errors.name" class="mb-2 block text-xs text-danger">{{ form.errors.name }}</span>

          <label class="ti-form-label">Project</label>
          <select v-model="form.project_id" class="ti-form-select mb-1">
            <option value="">Select project</option>
            <option v-for="p in projects" :key="p.id" :value="p.id">{{ p.name }}</option>
          </select>
          <span v-if="form.errors.project_id" class="mb-2 block text-xs text-danger">{{ form.errors.project_id }}</span>

          <label class="ti-form-label">Type</label>
          <select v-model="form.type" class="ti-form-select mb-3">
            <option value="functional">Functional</option>
            <option value="performance">Performance</option>
            <option value="ui">UI</option>
            <option value="security">Security</option>
            <option value="regression">Regression</option>
          </select>

          <label class="ti-form-label">Status</label>
          <select v-model="form.status" class="ti-form-select mb-3">
            <option value="pending">Pending</option>
            <option value="passed">Passed</option>
            <option value="failed">Failed</option>
            <option value="blocked">Blocked</option>
          </select>

          <label class="ti-form-label">Priority</label>
          <select v-model="form.priority" class="ti-form-select mb-3">
            <option value="low">Low</option>
            <option value="medium">Medium</option>
            <option value="high">High</option>
            <option value="critical">Critical</option>
          </select>

          <label class="ti-form-label">Owner</label>
          <input v-model="form.owner" class="ti-form-control mb-3">

          <label class="ti-form-label">Notes</label>
          <textarea v-model="form.notes" class="ti-form-control" rows="3"></textarea>
        </div>
        <div v-else-if="modal==='view'" class="p-6">
          <p><b>{{ selected.name }}</b> â€” {{ selected.type }}</p>
          <p class="text-textmuted">{{ selected.project ?? 'â€”' }} Â· owner {{ selected.owner ?? 'â€”' }} Â· last run {{ selected.lastRun }}</p>
        </div>
        <div v-else class="p-6">Delete <b>{{ selected.name }}</b>? This cannot be undone.</div>
        <div class="px-6 py-4 border-t flex justify-end gap-2">
          <button class="ti-btn ti-btn-light" :disabled="form.processing" @click="modal=null">Cancel</button>
          <button v-if="modal==='edit'" class="ti-btn ti-btn-primary" :disabled="form.processing" @click="save">{{ form.processing ? 'Savingâ€¦' : 'Save Test Case' }}</button>
          <button v-if="modal==='delete'" class="ti-btn ti-btn-danger" @click="remove(selected)">Delete</button>
        </div>
      </div>
    </div>
  </div>
</template>
