<script setup>
import ResourceCrud from '@/Components/crud/ResourceCrud.vue'
defineProps({ budgetItems: { type: Array, default: () => [] }, projects: { type: Array, default: () => [] } })
const fields = [{ name: 'project_id', camelName: 'projectId', display: 'project', label: 'Project', type: 'select', source: 'projects', required: true }, { name: 'category', label: 'Category', required: true }, { name: 'allocated', label: 'Allocated', type: 'number', default: 0 }, { name: 'spent', label: 'Spent', type: 'number', default: 0 }, { name: 'status', label: 'Status', type: 'select', default: 'on-track', options: [{ value: 'on-track', label: 'On track' }, { value: 'over-budget', label: 'Over budget' }] }, { name: 'description', label: 'Description', type: 'textarea', full: true }]
</script>
<template><ResourceCrud title="Budget" subtitle="Plan and track project spending" endpoint="/budget-items" :items="budgetItems" :fields="fields" :projects="projects" label="Budget item" /></template>
