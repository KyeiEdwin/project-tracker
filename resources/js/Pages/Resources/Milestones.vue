<script setup>
import ResourceCrud from '@/Components/crud/ResourceCrud.vue'
defineProps({ milestones: { type: Array, default: () => [] }, projects: { type: Array, default: () => [] } })
const fields = [{ name: 'project_id', camelName: 'projectId', display: 'project', label: 'Project', type: 'select', source: 'projects', required: true }, { name: 'name', label: 'Name', required: true }, { name: 'due_date', camelName: 'dueDate', label: 'Due Date', type: 'date', required: true }, { name: 'status', label: 'Status', type: 'select', default: 'upcoming', options: [{ value: 'upcoming', label: 'Upcoming' }, { value: 'completed', label: 'Completed' }, { value: 'overdue', label: 'Overdue' }] }, { name: 'description', label: 'Description', type: 'textarea', full: true }]
</script>
<template><ResourceCrud title="Milestones" subtitle="Track project milestones and deliverables" endpoint="/milestones" :items="milestones" :fields="fields" :projects="projects" label="Milestone" /></template>
