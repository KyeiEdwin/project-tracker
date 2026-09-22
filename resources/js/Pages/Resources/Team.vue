<script setup>
import ResourceCrud from '@/Components/crud/ResourceCrud.vue'
defineProps({ teamMembers: { type: Array, default: () => [] } })
const fields = [{ name: 'name', label: 'Name', required: true }, { name: 'email', label: 'Email', type: 'email', required: true }, { name: 'role', label: 'Role', required: true }, { name: 'department', label: 'Department' }, { name: 'availability', label: 'Availability %', type: 'number', default: 100 }, { name: 'status', label: 'Status', type: 'select', default: 'active', options: [{ value: 'active', label: 'Active' }, { value: 'inactive', label: 'Inactive' }] }]
</script>
<template><ResourceCrud title="Team Resources" subtitle="Manage team members and allocations" endpoint="/team-members" :items="teamMembers" :fields="fields" label="Member" /></template>
