<script setup>
import { ref, watch } from 'vue'
import { router, useForm } from '@inertiajs/vue3'
import PageHeader from '@/components/ui/PageHeader.vue'

const props = defineProps({
  stakeholders: { type: Array, default: () => [] },
  projects: { type: Array, default: () => [] },
})

const stakeholders = ref([...props.stakeholders])
watch(() => props.stakeholders, (value) => { stakeholders.value = [...value] })

const getInfluenceClass = (level) => ({
  'high': 'bg-danger/10 text-danger',
  'medium': 'bg-warning/10 text-warning',
  'low': 'bg-success/10 text-success'
})[level]

// Add Stakeholder modal state
const showAddModal = ref(false)
const newStakeholder = useForm({ project_id: '', name: '', email: '', role: '', department: '', influence: 'medium', interest: 'medium' })

const openAddModal = () => {
  showAddModal.value = true
}

const closeAddModal = () => {
  showAddModal.value = false
  newStakeholder.reset()
  newStakeholder.influence = 'medium'
  newStakeholder.interest = 'medium'
}

const saveStakeholder = () => {
  newStakeholder.post('/stakeholders', { preserveScroll: true, onSuccess: closeAddModal })
}

const deleteStakeholder = (stakeholder) => {
  if (window.confirm(`Remove ${stakeholder.name}?`)) router.delete(`/stakeholders/${stakeholder.id}`, { preserveScroll: true })
}
</script>

<template>
  <div>
    <PageHeader title="Stakeholders" subtitle="Manage project stakeholders and communication">
      <template #actions>
        <button class="ti-btn ti-btn-primary" @click="openAddModal">
          <i class="ri-user-add-line me-1"></i> Add Stakeholder
        </button>
      </template>
    </PageHeader>

    <div class="grid grid-cols-12 gap-6">
      <div class="col-span-12 xl:col-span-8">
        <div class="box">
          <div class="box-header">
            <h5 class="box-title">Stakeholder Directory</h5>
          </div>
          <div class="box-body p-0">
            <table class="table table-hover whitespace-nowrap">
              <thead>
                <tr>
                  <th>Name</th>
                  <th>Role</th>
                  <th>Department</th>
                  <th>Influence</th>
                  <th>Interest</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="stakeholder in stakeholders" :key="stakeholder.id">
                  <td class="font-medium">{{ stakeholder.name }}</td>
                  <td>{{ stakeholder.role }}</td>
                  <td class="text-textmuted">{{ stakeholder.department }}</td>
                  <td><span class="badge" :class="getInfluenceClass(stakeholder.influence)">{{ stakeholder.influence }}</span></td>
                  <td><span class="badge" :class="getInfluenceClass(stakeholder.interest)">{{ stakeholder.interest }}</span></td>
                  <td>
                    <div class="flex gap-1">
                      <button class="ti-btn ti-btn-soft-primary ti-btn-icon ti-btn-sm"><i class="ri-eye-line"></i></button>
                      <button class="ti-btn ti-btn-soft-info ti-btn-icon ti-btn-sm"><i class="ri-edit-line"></i></button>
                      <button class="ti-btn ti-btn-soft-danger ti-btn-icon ti-btn-sm" @click="deleteStakeholder(stakeholder)"><i class="ri-delete-bin-line"></i></button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <div class="col-span-12 xl:col-span-4">
        <div class="box">
          <div class="box-header">
            <h5 class="box-title">Stakeholder Matrix</h5>
          </div>
          <div class="box-body">
            <div class="grid grid-cols-2 gap-2 text-center text-sm">
              <div class="p-4 bg-danger/10 rounded">
                <strong class="text-danger">Manage Closely</strong>
                <p class="text-xs text-textmuted mt-1">High Power, High Interest</p>
              </div>
              <div class="p-4 bg-warning/10 rounded">
                <strong class="text-warning">Keep Satisfied</strong>
                <p class="text-xs text-textmuted mt-1">High Power, Low Interest</p>
              </div>
              <div class="p-4 bg-primary/10 rounded">
                <strong class="text-primary">Keep Informed</strong>
                <p class="text-xs text-textmuted mt-1">Low Power, High Interest</p>
              </div>
              <div class="p-4 bg-success/10 rounded">
                <strong class="text-success">Monitor</strong>
                <p class="text-xs text-textmuted mt-1">Low Power, Low Interest</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Add Stakeholder Modal -->
    <div
      v-if="showAddModal"
      class="fixed inset-0 z-[80] flex items-center justify-center bg-black/40"
    >
      <div class="bg-white dark:bg-bgdark rounded-xl shadow-xl w-full max-w-lg mx-4 max-h-[calc(100vh-6rem)] overflow-y-auto">
        <div class="px-6 py-4 border-b border-defaultborder/60 flex items-center justify-between">
          <h3 class="text-base font-semibold">Add Stakeholder</h3>
          <button 
            class="ti-btn ti-btn-sm ti-btn-icon ti-btn-light" 
            type="button"
            @click="closeAddModal"
          >
            <i class="ri-close-line"></i>
          </button>
        </div>

        <div class="px-6 py-5 space-y-4">
          <div>
            <label class="ti-form-label text-sm mb-1">Project <span class="text-danger">*</span></label>
            <select v-model="newStakeholder.project_id" class="ti-form-select"><option value="">Select project</option><option v-for="project in projects" :key="project.id" :value="project.id">{{ project.name }}</option></select>
            <p v-if="newStakeholder.errors.project_id" class="text-xs text-danger mt-1">{{ newStakeholder.errors.project_id }}</p>
          </div>
          <div>
            <label class="ti-form-label text-sm mb-1">Name <span class="text-danger">*</span></label>
            <input 
              v-model="newStakeholder.name"
              type="text"
              class="ti-form-control"
              placeholder="Enter stakeholder name"
            >
          </div>

          <div>
            <label class="ti-form-label text-sm mb-1">Role <span class="text-danger">*</span></label>
            <input 
              v-model="newStakeholder.role"
              type="text"
              class="ti-form-control"
              placeholder="e.g. Project Sponsor, Product Owner"
            >
            <p v-if="newStakeholder.errors.name || newStakeholder.errors.role" class="text-xs text-danger mt-1">{{ newStakeholder.errors.name || newStakeholder.errors.role }}</p>
          </div>

          <div>
            <label class="ti-form-label text-sm mb-1">Department</label>
            <input 
              v-model="newStakeholder.department"
              type="text"
              class="ti-form-control"
              placeholder="e.g. Engineering, Operations"
            >
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="ti-form-label text-sm mb-1">Influence</label>
              <select v-model="newStakeholder.influence" class="ti-form-select">
                <option value="high">High</option>
                <option value="medium">Medium</option>
                <option value="low">Low</option>
              </select>
            </div>
            <div>
              <label class="ti-form-label text-sm mb-1">Interest</label>
              <select v-model="newStakeholder.interest" class="ti-form-select">
                <option value="high">High</option>
                <option value="medium">Medium</option>
                <option value="low">Low</option>
              </select>
            </div>
          </div>

          <p v-if="!newStakeholder.project_id || !newStakeholder.name.trim() || !newStakeholder.role.trim()" class="text-xs text-warning mt-1">
            Enter at least a name and role to enable save.
          </p>
        </div>

        <div class="px-6 py-4 border-t border-defaultborder/60 flex justify-end gap-3 bg-light/40 dark:bg-bgdark/40 rounded-b-xl">
          <button class="ti-btn ti-btn-light" type="button" @click="closeAddModal">
            Cancel
          </button>
          <button
            class="ti-btn ti-btn-primary"
            type="button"
            :disabled="newStakeholder.processing || !newStakeholder.project_id || !newStakeholder.name.trim() || !newStakeholder.role.trim()"
            @click="saveStakeholder"
          >
            Save Stakeholder
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

