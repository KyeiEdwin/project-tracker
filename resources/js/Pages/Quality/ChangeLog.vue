<script setup>
import ResourceCrud from '@/Components/crud/ResourceCrud.vue'
defineProps({ changes: { type: Array, default: () => [] }, projects: { type: Array, default: () => [] } })
const fields = [{ name: 'project_id', camelName: 'projectId', display: 'project', label: 'Project', type: 'select', source: 'projects', required: true }, { name: 'title', label: 'Title', required: true }, { name: 'type', label: 'Type', required: true }, { name: 'requestor', label: 'Requestor', required: true }, { name: 'requested_on', camelName: 'requestedOn', label: 'Requested on', type: 'date', required: true }, { name: 'status', label: 'Status', type: 'select', default: 'pending', options: [{ value: 'pending', label: 'Pending' }, { value: 'approved', label: 'Approved' }, { value: 'rejected', label: 'Rejected' }] }, { name: 'description', label: 'Description', type: 'textarea', full: true }]
</script>
<template><ResourceCrud title="Change Log" subtitle="Track change requests and approvals" endpoint="/change-logs" :items="changes" :fields="fields" :projects="projects" label="Change request" /></template>
