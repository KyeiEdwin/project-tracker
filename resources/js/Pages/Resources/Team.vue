<script setup>
import { ref } from 'vue'
import { useForm } from '@inertiajs/vue3'
import ResourceCrud from '@/Components/crud/ResourceCrud.vue'
import PageHeader from '@/Components/ui/PageHeader.vue'

const props = defineProps({
	teamMembers: { type: Array, default: () => [] },
	teams: { type: Array, default: () => [] },
	projects: { type: Array, default: () => [] },
})

const memberFields = [
	{ name: 'name', label: 'Name', required: true },
	{ name: 'email', label: 'Email', type: 'email', required: true },
	{ name: 'password', label: 'Login Password', type: 'password', required: true },
	{ name: 'password_confirmation', label: 'Confirm Password', type: 'password', required: true },
	{ name: 'role', label: 'Role', type: 'select', required: true, options: [
		{ value: 'manager', label: 'Manager' }, { value: 'developer', label: 'Developer' },
		{ value: 'designer', label: 'Designer' }, { value: 'tester', label: 'Tester' }, { value: 'analyst', label: 'Analyst' },
	] },
	{ name: 'team_id', camelName: 'teamId', label: 'Team', type: 'select', options: props.teams.map((team) => ({ value: team.id, label: team.name })) },
	{ name: 'project_ids', camelName: 'projectIds', label: 'Projects', type: 'select', multiple: true, source: 'projects' },
	{ name: 'department', label: 'Department' },
	{ name: 'availability', label: 'Availability %', type: 'number', default: 100 },
	{ name: 'status', label: 'Status', type: 'select', default: 'active', options: [{ value: 'active', label: 'Active' }, { value: 'inactive', label: 'Inactive' }] },
]

const showTeamForm = ref(false)
const teamForm = useForm({ name: '', slug: '', description: '', status: 'active' })

const createTeam = () => teamForm.post('/teams', {
	preserveScroll: true,
	onSuccess: () => {
		teamForm.reset()
		teamForm.status = 'active'
		showTeamForm.value = false
	},
})
</script>

<template>
	<div>
		<PageHeader title="Team Resources" subtitle="Create teams, onboard members, and manage allocations">
			<template #actions>
				<button type="button" class="ti-btn ti-btn-primary" @click="showTeamForm = true">
					<i class="ri-team-line me-1"></i> New Team
				</button>
			</template>
		</PageHeader>

		<div class="box mb-6">
			<div class="box-header flex items-center justify-between">
				<h2 class="box-title">Teams</h2>
				<span class="text-sm text-textmuted">{{ teams.length }} teams</span>
			</div>
			<div class="box-body p-0">
				<div class="table-responsive">
					<table class="table table-hover whitespace-nowrap">
						<thead><tr><th>Name</th><th>Slug</th><th>Members</th><th>Status</th></tr></thead>
						<tbody>
							<tr v-if="!teams.length"><td colspan="4" class="py-8 text-center text-textmuted">No teams created yet.</td></tr>
							<tr v-for="team in teams" :key="team.id">
								<td class="font-medium">{{ team.name }}</td>
								<td>{{ team.slug }}</td>
								<td>{{ team.membersCount }}</td>
								<td><span class="badge bg-success/10 text-success">{{ team.status }}</span></td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>

		<ResourceCrud title="Team Members" subtitle="Manage member credentials, roles, and project allocations" endpoint="/team-members" :items="teamMembers" :fields="memberFields" label="Member" />

		<div v-if="showTeamForm" class="fixed inset-0 z-[80] flex items-center justify-center bg-black/40 p-4">
			<form class="w-full max-w-2xl rounded-xl bg-white shadow-xl dark:bg-bgdark" @submit.prevent="createTeam">
				<div class="flex items-center justify-between border-b px-6 py-4">
					<h3 class="font-semibold">Create Team</h3>
					<button type="button" class="ti-btn ti-btn-sm ti-btn-light" :disabled="teamForm.processing" @click="showTeamForm = false">Close</button>
				</div>
				<div class="grid grid-cols-1 gap-4 px-6 py-5 md:grid-cols-2">
					<label class="block"><span class="ti-form-label">Team Name *</span><input v-model="teamForm.name" class="ti-form-control" required><span v-if="teamForm.errors.name" class="mt-1 block text-xs text-danger">{{ teamForm.errors.name }}</span></label>
					<label class="block"><span class="ti-form-label">Slug</span><input v-model="teamForm.slug" class="ti-form-control" placeholder="web-development-team"><span v-if="teamForm.errors.slug" class="mt-1 block text-xs text-danger">{{ teamForm.errors.slug }}</span></label>
					<label class="block md:col-span-2"><span class="ti-form-label">Description</span><textarea v-model="teamForm.description" class="ti-form-control" rows="4"></textarea><span v-if="teamForm.errors.description" class="mt-1 block text-xs text-danger">{{ teamForm.errors.description }}</span></label>
					<label class="block"><span class="ti-form-label">Status</span><select v-model="teamForm.status" class="ti-form-select"><option value="active">Active</option><option value="inactive">Inactive</option></select></label>
				</div>
				<div class="flex justify-end gap-3 border-t px-6 py-4"><button type="button" class="ti-btn ti-btn-light" :disabled="teamForm.processing" @click="showTeamForm = false">Cancel</button><button type="submit" class="ti-btn ti-btn-primary" :disabled="teamForm.processing">{{ teamForm.processing ? 'Creating...' : 'Create Team' }}</button></div>
			</form>
		</div>
	</div>
</template>
