<script setup>
import { ref } from 'vue'
import PageHeader from '@/components/ui/PageHeader.vue'

const stakeholders = ref([
  { id: 1, name: 'John Smith', role: 'Project Sponsor', department: 'Executive', influence: 'high', interest: 'high' },
  { id: 2, name: 'Sarah Johnson', role: 'Product Owner', department: 'Product', influence: 'high', interest: 'high' },
  { id: 3, name: 'Mike Williams', role: 'Technical Lead', department: 'Engineering', influence: 'medium', interest: 'high' },
  { id: 4, name: 'Emily Davis', role: 'QA Manager', department: 'Quality', influence: 'medium', interest: 'medium' },
  { id: 5, name: 'David Brown', role: 'End User Rep', department: 'Operations', influence: 'low', interest: 'high' }
])

const getInfluenceClass = (level) => ({
  'high': 'bg-danger/10 text-danger',
  'medium': 'bg-warning/10 text-warning',
  'low': 'bg-success/10 text-success'
})[level]

// Add Stakeholder modal state
const showAddModal = ref(false)
const newStakeholder = ref({
  name: '',
  role: '',
  department: '',
  influence: 'medium',
  interest: 'medium'
})

const openAddModal = () => {
  showAddModal.value = true
}

const closeAddModal = () => {
  showAddModal.value = false
  newStakeholder.value = {
    name: '',
    role: '',
    department: '',
    influence: 'medium',
    interest: 'medium'
  }
}

const saveStakeholder = () => {
  if (!newStakeholder.value.name.trim() || !newStakeholder.value.role.trim()) return

  const nextId = stakeholders.value.length ? Math.max(...stakeholders.value.map(s => s.id)) + 1 : 1

  stakeholders.value.push({
    id: nextId,
    name: newStakeholder.value.name.trim(),
    role: newStakeholder.value.role.trim(),
    department: newStakeholder.value.department.trim() || 'N/A',
    influence: newStakeholder.value.influence,
    interest: newStakeholder.value.interest
  })

  closeAddModal()
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

          <p v-if="!newStakeholder.name.trim() || !newStakeholder.role.trim()" class="text-xs text-warning mt-1">
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
            :disabled="!newStakeholder.name.trim() || !newStakeholder.role.trim()"
            @click="saveStakeholder"
          >
            Save Stakeholder
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

