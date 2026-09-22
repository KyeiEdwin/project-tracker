<script setup>
import ResourceCrud from '@/Components/crud/ResourceCrud.vue'
defineProps({ risks: { type: Array, default: () => [] }, projects: { type: Array, default: () => [] } })
const levels = [{ value: 'low', label: 'Low' }, { value: 'medium', label: 'Medium' }, { value: 'high', label: 'High' }]
const fields = [{ name: 'project_id', camelName: 'projectId', display: 'project', label: 'Project', type: 'select', source: 'projects', required: true }, { name: 'title', label: 'Title', required: true }, { name: 'category', label: 'Category', required: true }, { name: 'probability', label: 'Probability', type: 'select', options: levels, required: true }, { name: 'impact', label: 'Impact', type: 'select', options: levels, required: true }, { name: 'status', label: 'Status', type: 'select', default: 'open', options: [{ value: 'open', label: 'Open' }, { value: 'mitigating', label: 'Mitigating' }, { value: 'closed', label: 'Closed' }] }, { name: 'mitigation', label: 'Mitigation', type: 'textarea', full: true }]
</script>
<template><ResourceCrud title="Risks & Issues" subtitle="Track and manage project risks" endpoint="/risks" :items="risks" :fields="fields" :projects="projects" label="Risk" /></template>
