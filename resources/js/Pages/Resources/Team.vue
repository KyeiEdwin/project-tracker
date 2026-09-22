<script setup>
import { ref } from 'vue'
import { router, useForm } from '@inertiajs/vue3'
import PageHeader from '@/components/ui/PageHeader.vue'
import { useActionFeedback } from '@/composables/useActionFeedback'

const props = defineProps({
  teamMembers: { type: Array, default: () => [] },
  projects: { type: Array, default: () => [] },
})

const { notify } = useActionFeedback()

const modal = ref(null)
const selected = ref(null)

const form = useForm({
  name: '', email: '', role: '', department: '', availability: 100, status: 'active',
})

const openAdd = () => {
  selected.value = null
  form.clearErrors()
  form.defaults({ name: '', email: '', role: '', department: '', availability: 100, status: 'active' })
  form.reset()
  modal.value = 'edit'
}

const edit = m => {
  selected.value = m
  form.clearErrors()
  form.defaults({
    name: m.name ?? '', email: m.email ?? '', role: m.role ?? '',
    department: m.department ?? '', availability: m.availability ?? 100, status: m.status ?? 'active',
  })
  form.reset()
  modal.value = 'edit'
}

const save = () => {
  const options = {
    preserveScroll: true,
    onSuccess: () => { modal.value = null; notify(selected.value ? 'Team member updated.' : 'Team member added.') },
  }
  if (selected.value) form.put(`/team-members/${selected.value.id}`, options)
  else form.post('/team-members', options)
}

const remove = m => {
  router.delete(`/team-members/${m.id}`, {
    preserveScroll: true,
    onSuccess: () => { modal.value = null; notify('Team member removed.') },
  })
}
</script>

<template>
  <div>
    <PageHeader title="Team Resources" subtitle="Manage team members and allocations">
      <template #actions>
        <button class="ti-btn ti-btn-primary" data-feedback-handled="true" @click="openAdd"><i class="ri-user-add-line me-1"></i> Add Member</button>
      </template>
    </PageHeader>
    <div class="grid grid-cols-12 gap-6">
      <div class="col-span-12">
        <div class="box">
          <div class="box-header"><h5 class="box-title">Team Members</h5></div>
          <div class="box-body p-0">
            <table class="table table-hover whitespace-nowrap">
              <thead><tr><th>Member</th><th>Role</th><th>Email</th><th>Availability</th><th>Projects</th><th>Actions</th></tr></thead>
              <tbody>
                <tr v-if="!teamMembers.length"><td colspan="6" class="py-8 text-center text-textmuted">No team members yet.</td></tr>
                <tr v-for="member in teamMembers" :key="member.id">
                  <td class="font-medium">{{ member.name }}</td>
                  <td>{{ member.role }}</td>
                  <td class="text-textmuted">{{ member.email }}</td>
                  <td>
                    <div class="flex items-center gap-2">
                      <div class="progress progress-xs flex-1 max-w-[80px]"><div class="progress-bar bg-primary" :style="{ width: member.availability + '%' }"></div></div>
                      {{ member.availability }}%
                    </div>
                  </td>
                  <td>{{ member.projects }}</td>
                  <td>
                    <div class="flex gap-1">
                      <button class="ti-btn ti-btn-soft-primary ti-btn-icon ti-btn-sm" data-feedback-handled="true" aria-label="View member" @click="selected=member;modal='view'"><i class="ri-eye-line"></i></button>
                      <button class="ti-btn ti-btn-soft-info ti-btn-icon ti-btn-sm" data-feedback-handled="true" aria-label="Edit member" @click="edit(member)"><i class="ri-edit-line"></i></button>
                      <button class="ti-btn ti-btn-soft-danger ti-btn-icon ti-btn-sm" data-feedback-handled="true" aria-label="Remove member" @click="selected=member;modal='delete'"><i class="ri-delete-bin-line"></i></button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <div v-if="modal" class="fixed inset-0 z-[80] flex items-center justify-center bg-black/40" @click.self="modal=null">
      <div class="bg-white dark:bg-bgdark rounded-xl shadow-xl w-full max-w-md mx-4">
        <div class="px-6 py-4 border-b flex justify-between">
          <h3 class="font-semibold">{{ modal==='edit' ? (selected ? 'Edit Member' : 'Add Member') : modal==='view' ? 'Member details' : 'Remove Member' }}</h3>
          <button class="ti-btn ti-btn-sm ti-btn-light" :disabled="form.processing" @click="modal=null">Close</button>
        </div>
        <div v-if="modal==='edit'" class="p-6">
          <label class="ti-form-label">Name</label>
          <input v-model="form.name" class="ti-form-control mb-1">
          <span v-if="form.errors.name" class="mb-2 block text-xs text-danger">{{ form.errors.name }}</span>

          <label class="ti-form-label">Role</label>
          <input v-model="form.role" class="ti-form-control mb-1">
          <span v-if="form.errors.role" class="mb-2 block text-xs text-danger">{{ form.errors.role }}</span>

          <label class="ti-form-label">Email</label>
          <input v-model="form.email" type="email" class="ti-form-control mb-1">
          <span v-if="form.errors.email" class="mb-2 block text-xs text-danger">{{ form.errors.email }}</span>

          <label class="ti-form-label">Department</label>
          <input v-model="form.department" class="ti-form-control mb-3">

          <label class="ti-form-label">Availability</label>
          <input v-model.number="form.availability" type="number" min="0" max="100" class="ti-form-control mb-3">

          <label class="ti-form-label">Status</label>
          <select v-model="form.status" class="ti-form-select">
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
          </select>
        </div>
        <div v-else-if="modal==='view'" class="p-6">
          <p><b>{{ selected.name }}</b> â€” {{ selected.role }}</p>
          <p class="text-textmuted">{{ selected.email }} Â· {{ selected.availability }}% available</p>
        </div>
        <div v-else class="p-6">Remove <b>{{ selected.name }}</b> from the team?</div>
        <div class="px-6 py-4 border-t flex justify-end gap-2">
          <button class="ti-btn ti-btn-light" :disabled="form.processing" @click="modal=null">Cancel</button>
          <button v-if="modal==='edit'" class="ti-btn ti-btn-primary" :disabled="form.processing" @click="save">{{ form.processing ? 'Savingâ€¦' : 'Save Member' }}</button>
          <button v-if="modal==='delete'" class="ti-btn ti-btn-danger" @click="remove(selected)">Remove</button>
        </div>
      </div>
    </div>
  </div>
</template>
