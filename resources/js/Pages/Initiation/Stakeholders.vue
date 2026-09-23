<script setup>
import ResourceCrud from '@/Components/crud/ResourceCrud.vue'

defineProps({ 
  stakeholders: { type: Array, default: () => [] }, 
  projects: { type: Array, default: () => [] },
  currentProject: { type: Object, default: null },
  filters: { type: Object, default: () => ({}) }
})

const levels = [
  { value: 'low', label: 'Low' }, 
  { value: 'medium', label: 'Medium' }, 
  { value: 'high', label: 'High' }
]

const fields = [
  { name: 'project_id', camelName: 'projectId', display: 'project', label: 'Project', type: 'select', source: 'projects', required: true }, 
  { name: 'name', label: 'Name', required: true }, 
  { name: 'email', label: 'Email', type: 'email' }, 
  { name: 'role', label: 'Role', required: true }, 
  { name: 'department', label: 'Department' }, 
  { name: 'organization', label: 'Organization' },
  { name: 'influence', label: 'Influence', type: 'select', options: levels, default: 'medium', required: true }, 
  { name: 'interest', label: 'Interest', type: 'select', options: levels, default: 'medium', required: true },
  { name: 'notes', label: 'Notes', type: 'textarea', full: true }
]
</script>

<template>
  <ResourceCrud 
    title="Stakeholders" 
    subtitle="Manage project stakeholders and communication" 
    endpoint="/stakeholders" 
    :items="stakeholders" 
    :fields="fields" 
    :projects="projects" 
    :current-project="currentProject"
    :filters="filters"
    label="Stakeholder" 
  />
</template>
