<script setup>
import ResourceCrud from '@/Components/crud/ResourceCrud.vue'
defineProps({ timeEntries: { type: Array, default: () => [] }, projects: { type: Array, default: () => [] }, teamMembers: { type: Array, default: () => [] } })
const fields = [{ name: 'project_id', camelName: 'projectId', display: 'project', label: 'Project', type: 'select', source: 'projects', required: true }, { name: 'team_member_id', camelName: 'teamMemberId', display: 'teamMember', label: 'Member', type: 'select', source: 'teamMembers', required: true }, { name: 'work_date', camelName: 'workDate', label: 'Work Date', type: 'date', required: true }, { name: 'hours', label: 'Hours', type: 'number', required: true }, { name: 'description', label: 'Description', type: 'textarea', full: true }]
</script>
<template><ResourceCrud title="Time Tracking" subtitle="Track time spent on projects and tasks" endpoint="/time-entries" :items="timeEntries" :fields="fields" :projects="projects" :team-members="teamMembers" label="Time entry" /></template>
