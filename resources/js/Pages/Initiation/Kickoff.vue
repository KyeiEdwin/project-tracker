<script setup>
import { ref, watch } from 'vue'
import { router, useForm } from '@inertiajs/vue3'
import PageHeader from '@/components/ui/PageHeader.vue'

const props = defineProps({
  kickoffs: { type: Array, default: () => [] },
  objectives: { type: Array, default: () => [] },
  projects: { type: Array, default: () => [] },
})

const kickoffs = ref([...props.kickoffs])
const objectives = ref([...props.objectives])

watch(() => props.kickoffs, (value) => { kickoffs.value = [...value] })
watch(() => props.objectives, (value) => { objectives.value = [...value] })

// Schedule Kick-Off modal state
const showScheduleModal = ref(false)
const newKickoff = useForm({ project_id: '', scheduled_on: '', attendees_count: 5, status: 'scheduled' })

const openScheduleModal = () => {
  showScheduleModal.value = true
}

const closeScheduleModal = () => {
  showScheduleModal.value = false
  newKickoff.reset()
  newKickoff.status = 'scheduled'
}

const saveKickoff = () => {
  newKickoff.post('/kickoffs', { preserveScroll: true, onSuccess: closeScheduleModal })
}

const deleteKickoff = (kickoff) => {
  if (window.confirm(`Delete the kick-off for ${kickoff.project}?`)) router.delete(`/kickoffs/${kickoff.id}`, { preserveScroll: true })
}
</script>

<template>
  <div>
    <PageHeader title="Project Kick-Off" subtitle="Initialize and launch new projects">
      <template #actions>
        <button class="ti-btn ti-btn-primary" @click="openScheduleModal">
          <i class="ri-add-line me-1"></i> Schedule Kick-Off
        </button>
      </template>
    </PageHeader>

    <div class="grid grid-cols-12 gap-6">
      <div class="col-span-12 xl:col-span-8">
        <div class="box">
          <div class="box-header">
            <h5 class="box-title">Kick-Off Meetings</h5>
          </div>
          <div class="box-body p-0">
            <table class="table table-hover whitespace-nowrap">
              <thead>
                <tr>
                  <th>Project</th>
                  <th>Date</th>
                  <th>Attendees</th>
                  <th>Status</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="kickoff in kickoffs" :key="kickoff.id">
                  <td class="font-medium">{{ kickoff.project }}</td>
                  <td>{{ kickoff.date }}</td>
                  <td>{{ kickoff.attendees }} people</td>
                  <td>
                    <span
                      class="badge"
                      :class="kickoff.status === 'completed' ? 'bg-success/10 text-success' : 'bg-warning/10 text-warning'"
                    >
                      {{ kickoff.status }}
                    </span>
                  </td>
                  <td>
                    <div class="flex gap-1">
                      <button class="ti-btn ti-btn-soft-danger ti-btn-sm" @click="deleteKickoff(kickoff)">Delete</button>
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
            <h5 class="box-title">Kick-Off Checklist</h5>
          </div>
          <div class="box-body">
            <ul class="space-y-3">
              <li v-for="obj in objectives" :key="obj.id" class="flex items-center gap-3">
                <input type="checkbox" class="ti-form-check-input" :checked="obj.completed">
                <span :class="{ 'line-through text-textmuted': obj.completed }">{{ obj.text }}</span>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>

    <!-- Schedule Kick-Off Modal -->
    <div
      v-if="showScheduleModal"
      class="fixed inset-0 z-[80] flex items-center justify-center bg-black/40"
    >
      <div class="bg-white dark:bg-bgdark rounded-xl shadow-xl w-full max-w-lg mx-4 max-h-[calc(100vh-6rem)] overflow-y-auto">
        <div class="px-6 py-4 border-b border-defaultborder/60 flex items-center justify-between">
          <h3 class="text-base font-semibold">Schedule Kick-Off</h3>
          <button
            class="ti-btn ti-btn-sm ti-btn-icon ti-btn-light"
            type="button"
            @click="closeScheduleModal"
          >
            <i class="ri-close-line"></i>
          </button>
        </div>

        <div class="px-6 py-5 space-y-4">
          <div>
            <label class="ti-form-label text-sm mb-1">Project <span class="text-danger">*</span></label>
            <select v-model="newKickoff.project_id" class="ti-form-select"><option value="">Select project</option><option v-for="project in projects" :key="project.id" :value="project.id">{{ project.name }}</option></select>
            <p v-if="newKickoff.errors.project_id" class="text-xs text-danger mt-1">{{ newKickoff.errors.project_id }}</p>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="ti-form-label text-sm mb-1">Kick-Off Date <span class="text-danger">*</span></label>
              <input
              v-model="newKickoff.scheduled_on"
                type="date"
                class="ti-form-control"
              >
            </div>
            <div>
              <label class="ti-form-label text-sm mb-1">Expected Attendees</label>
              <input
              v-model="newKickoff.attendees_count"
                type="number"
                min="1"
                class="ti-form-control"
              >
            </div>
          </div>

          <div>
            <label class="ti-form-label text-sm mb-1">Status</label>
            <select v-model="newKickoff.status" class="ti-form-select">
              <option value="scheduled">Scheduled</option>
              <option value="completed">Completed</option>
            </select>
          </div>

          <p v-if="!newKickoff.project_id || !newKickoff.scheduled_on" class="text-xs text-warning mt-1">
            Enter a project name and date to enable save.
          </p>
        </div>

        <div class="px-6 py-4 border-t border-defaultborder/60 flex justify-end gap-3 bg-light/40 dark:bg-bgdark/40 rounded-b-xl">
          <button class="ti-btn ti-btn-light" type="button" @click="closeScheduleModal">
            Cancel
          </button>
          <button
            class="ti-btn ti-btn-primary"
            type="button"
            :disabled="newKickoff.processing || !newKickoff.project_id || !newKickoff.scheduled_on"
            @click="saveKickoff"
          >
            Save Kick-Off
          </button>
        </div>
      </div>
    </div>
  </div>
</template>


