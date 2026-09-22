<script setup>
import { computed } from 'vue'
import { router, useForm } from '@inertiajs/vue3'
import PageHeader from '@/Components/ui/PageHeader.vue'

const props = defineProps({ project: { type: Object, default: null }, formMode: { type: String, default: 'create' } })

const form = useForm({
  projectType: props.project?.projectType ?? '',
  name: props.project?.name ?? '',
  description: props.project?.description ?? '',
  startDate: props.project?.startDate ?? '',
  endDate: props.project?.endDate ?? '',
  budget: props.project?.budget ?? '',
  priority: props.project?.priority ?? 'medium',
  status: props.project?.status ?? 'planning',
  team: props.project?.team ?? '',
  client: props.project?.client ?? '',
  // Predictive fields
  phases: '',
  milestones: '',
  deliverables: '',
  // Agile fields
  sprintDuration: '',
  sprintGoal: '',
  velocity: '',
  // Hybrid fields
  methodology: '',
  sprintLength: '',
  phaseCount: ''
})

const handleSubmit = () => {
  if (props.formMode === 'edit' && props.project) {
    form.put(`/projects/${props.project.id}`)
    return
  }

  form.post('/projects')
}

const handleCancel = () => {
  router.visit('/projects')
}

// Computed property to check if project type is selected
const showPredictiveFields = computed(() => form.projectType === 'predictive')
const showAgileFields = computed(() => form.projectType === 'agile')
const showHybridFields = computed(() => form.projectType === 'hybrid')
</script>

<template>
  <div>
    <PageHeader :title="formMode === 'edit' ? 'Edit Project' : 'Create New Project'" subtitle="Add a new project to your portfolio">
      <template #actions>
        <button @click="handleCancel" class="ti-btn ti-btn-light">Cancel</button>
        <button @click="handleSubmit" class="ti-btn ti-btn-primary" :disabled="form.processing">
          <i class="ri-save-line me-1"></i> Save Project
        </button>
      </template>
    </PageHeader>

    <div class="grid grid-cols-12 gap-6">
      <div class="col-span-12 xl:col-span-8">
        <div class="box">
          <div class="box-header">
            <h5 class="box-title">Project Information</h5>
          </div>
          <div class="box-body">
            <div class="grid grid-cols-12 gap-4">
              <div class="col-span-12">
                <label class="ti-form-label">Project Type *</label>
                <select v-model="form.projectType" class="ti-form-select">
                  <option value="">Select Project Type</option>
                  <option value="hybrid">Hybrid</option>
                  <option value="predictive">Predictive</option>
                  <option value="agile">Agile</option>
                </select>
                <p class="text-xs text-textmuted mt-1">Select the project methodology to customize form fields</p>
              </div>
              
              <div class="col-span-12">
                <label class="ti-form-label">Project Name *</label>
                <input v-model="form.name" type="text" class="ti-form-control" placeholder="Enter project name">
                <p v-if="form.errors.name" class="text-xs text-danger mt-1">{{ form.errors.name }}</p>
              </div>
              <div class="col-span-12">
                <label class="ti-form-label">Description</label>
                <textarea v-model="form.description" class="ti-form-control" rows="4" placeholder="Enter project description"></textarea>
              </div>
              <div class="col-span-12 md:col-span-6">
                <label class="ti-form-label">Start Date *</label>
                <input v-model="form.startDate" type="date" class="ti-form-control">
              </div>
              <div class="col-span-12 md:col-span-6">
                <label class="ti-form-label">End Date *</label>
                <input v-model="form.endDate" type="date" class="ti-form-control">
              </div>
              <div class="col-span-12 md:col-span-6">
                <label class="ti-form-label">Budget</label>
                <div class="input-group">
                  <span class="input-group-text">$</span>
                  <input v-model="form.budget" type="number" class="ti-form-control" placeholder="0.00">
                </div>
              </div>
              <div class="col-span-12 md:col-span-6">
                <label class="ti-form-label">Priority</label>
                <select v-model="form.priority" class="ti-form-select">
                  <option value="low">Low</option>
                  <option value="medium">Medium</option>
                  <option value="high">High</option>
                </select>
              </div>
              <div class="col-span-12 md:col-span-6">
                <label class="ti-form-label">Status</label>
                <select v-model="form.status" class="ti-form-select">
                  <option value="planning">Planning</option>
                  <option value="in-progress">In Progress</option>
                  <option value="on-hold">On Hold</option>
                </select>
              </div>
              <div class="col-span-12 md:col-span-6">
                <label class="ti-form-label">Assigned Team</label>
                <select v-model="form.team" class="ti-form-select">
                  <option value="">Select Team</option>
                  <option value="development">Development Team</option>
                  <option value="marketing">Marketing Team</option>
                  <option value="design">Design Team</option>
                  <option value="qa">QA Team</option>
                </select>
              </div>

              <!-- Predictive Project Type Fields -->
              <template v-if="showPredictiveFields">
                <div class="col-span-12 mt-4 pt-4 border-t border-defaultborder">
                  <h6 class="text-sm font-semibold mb-3 text-primary">Predictive Project Settings</h6>
                </div>
                <div class="col-span-12 md:col-span-6">
                  <label class="ti-form-label">Number of Phases</label>
                  <input v-model="form.phases" type="number" class="ti-form-control" placeholder="e.g. 5" min="1">
                </div>
                <div class="col-span-12 md:col-span-6">
                  <label class="ti-form-label">Key Milestones</label>
                  <input v-model="form.milestones" type="text" class="ti-form-control" placeholder="e.g. Requirements, Design, Development">
                </div>
                <div class="col-span-12">
                  <label class="ti-form-label">Major Deliverables</label>
                  <textarea v-model="form.deliverables" class="ti-form-control" rows="3" placeholder="List major deliverables separated by commas"></textarea>
                </div>
              </template>

              <!-- Agile Project Type Fields -->
              <template v-if="showAgileFields">
                <div class="col-span-12 mt-4 pt-4 border-t border-defaultborder">
                  <h6 class="text-sm font-semibold mb-3 text-primary">Agile Project Settings</h6>
                </div>
                <div class="col-span-12 md:col-span-6">
                  <label class="ti-form-label">Sprint Duration (weeks)</label>
                  <select v-model="form.sprintDuration" class="ti-form-select">
                    <option value="">Select Duration</option>
                    <option value="1">1 week</option>
                    <option value="2">2 weeks</option>
                    <option value="3">3 weeks</option>
                    <option value="4">4 weeks</option>
                  </select>
                </div>
                <div class="col-span-12 md:col-span-6">
                  <label class="ti-form-label">Initial Velocity (story points)</label>
                  <input v-model="form.velocity" type="number" class="ti-form-control" placeholder="e.g. 20" min="1">
                </div>
                <div class="col-span-12">
                  <label class="ti-form-label">Sprint Goal</label>
                  <textarea v-model="form.sprintGoal" class="ti-form-control" rows="2" placeholder="Describe the primary goal for sprints"></textarea>
                </div>
              </template>

              <!-- Hybrid Project Type Fields -->
              <template v-if="showHybridFields">
                <div class="col-span-12 mt-4 pt-4 border-t border-defaultborder">
                  <h6 class="text-sm font-semibold mb-3 text-primary">Hybrid Project Settings</h6>
                </div>
                <div class="col-span-12 md:col-span-6">
                  <label class="ti-form-label">Primary Methodology</label>
                  <select v-model="form.methodology" class="ti-form-select">
                    <option value="">Select Methodology</option>
                    <option value="agile-first">Agile-First Hybrid</option>
                    <option value="predictive-first">Predictive-First Hybrid</option>
                    <option value="balanced">Balanced Hybrid</option>
                  </select>
                </div>
                <div class="col-span-12 md:col-span-6">
                  <label class="ti-form-label">Sprint Length (weeks)</label>
                  <select v-model="form.sprintLength" class="ti-form-select">
                    <option value="">Select Length</option>
                    <option value="1">1 week</option>
                    <option value="2">2 weeks</option>
                    <option value="3">3 weeks</option>
                    <option value="4">4 weeks</option>
                  </select>
                </div>
                <div class="col-span-12">
                  <label class="ti-form-label">Number of Phases</label>
                  <input v-model="form.phaseCount" type="number" class="ti-form-control" placeholder="e.g. 3" min="1">
                </div>
              </template>
            </div>
          </div>
        </div>
      </div>

      <div class="col-span-12 xl:col-span-4">
        <div class="box">
          <div class="box-header">
            <h5 class="box-title">Client Information</h5>
          </div>
          <div class="box-body">
            <div class="mb-4">
              <label class="ti-form-label">Client Name</label>
              <input v-model="form.client" type="text" class="ti-form-control" placeholder="Enter client name">
            </div>
            <div class="mb-4">
              <label class="ti-form-label">Project Documents</label>
              <div class="border-2 border-dashed rounded-lg p-6 text-center">
                <i class="ri-upload-cloud-2-line text-4xl text-textmuted mb-2"></i>
                <p class="text-textmuted mb-0">Drag & drop files here or click to browse</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

