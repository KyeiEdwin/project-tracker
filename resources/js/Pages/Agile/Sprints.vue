<script setup>
import ResourceCrud from '@/Components/crud/ResourceCrud.vue'
defineProps({ sprints: { type: Array, default: () => [] }, projects: { type: Array, default: () => [] } })
const fields = [{ name: 'project_id', camelName: 'projectId', display: 'project', label: 'Project', type: 'select', source: 'projects', required: true }, { name: 'name', label: 'Name', required: true }, { name: 'start_date', camelName: 'startDate', label: 'Start', type: 'date', required: true }, { name: 'end_date', camelName: 'endDate', label: 'End', type: 'date', required: true }, { name: 'status', label: 'Status', type: 'select', default: 'planned', options: [{ value: 'planned', label: 'Planned' }, { value: 'active', label: 'Active' }, { value: 'completed', label: 'Completed' }] }, { name: 'goal', label: 'Goal', type: 'textarea', full: true }]
</script>
<template><ResourceCrud title="Sprints" subtitle="Manage sprint cycles and iterations" endpoint="/sprints" :items="sprints" :fields="fields" :projects="projects" label="Sprint" /></template>
