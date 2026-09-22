<script setup>
import ResourceCrud from '@/Components/crud/ResourceCrud.vue'
defineProps({ workflows: { type: Array, default: () => [] }, projects: { type: Array, default: () => [] } })
const fields = [{ name: 'project_id', camelName: 'projectId', display: 'project', label: 'Project', type: 'select', source: 'projects' }, { name: 'name', label: 'Name', required: true }, { name: 'is_default', camelName: 'isDefault', label: 'Default', type: 'boolean', default: false }, { name: 'description', label: 'Description', type: 'textarea', full: true }]
</script>
<template><ResourceCrud title="Workflows" subtitle="Define task workflow states" endpoint="/workflows" :items="workflows" :fields="fields" :projects="projects" label="Workflow" /></template>
