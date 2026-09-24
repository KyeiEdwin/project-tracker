<script setup>
import { Link, useForm } from '@inertiajs/vue3'
import AuthLayout from '@/Layouts/AuthLayout.vue'

defineOptions({ layout: AuthLayout })

const props = defineProps({
  teams: { type: Array, default: () => [] },
  jobRoles: { type: Array, default: () => [] },
})

const form = useForm({
  name: '',
  email: '',
  team_id: '',
  role: '',
  password: '',
  password_confirmation: '',
})

const submit = () => form.post('/register')
</script>

<template>
  <div>
    <div class="mb-6">
      <h1 class="text-2xl font-semibold">Create your account</h1>
      <p class="text-textmuted mt-1">Create your team-member account and start working on assigned projects.</p>
    </div>

    <form class="space-y-4" @submit.prevent="submit">
      <div>
        <label for="name" class="form-label">Full name</label>
        <input id="name" v-model="form.name" type="text" class="form-control" autocomplete="name" required>
        <p v-if="form.errors.name" class="text-danger text-sm mt-1">{{ form.errors.name }}</p>
      </div>

      <div>
        <label for="email" class="form-label">Email</label>
        <input id="email" v-model="form.email" type="email" class="form-control" autocomplete="email" required>
        <p v-if="form.errors.email" class="text-danger text-sm mt-1">{{ form.errors.email }}</p>
      </div>

      <div>
        <label for="team_id" class="form-label">Team</label>
        <select id="team_id" v-model="form.team_id" class="form-control" required>
          <option value="" disabled>Select a team</option>
          <option v-for="team in props.teams" :key="team.id" :value="team.id">{{ team.name }}</option>
        </select>
        <p v-if="form.errors.team_id" class="text-danger text-sm mt-1">{{ form.errors.team_id }}</p>
      </div>

      <div>
        <label for="role" class="form-label">Role</label>
        <select id="role" v-model="form.role" class="form-control" required>
          <option value="" disabled>Select a role</option>
          <option v-for="jobRole in props.jobRoles" :key="jobRole.value" :value="jobRole.value">{{ jobRole.label }}</option>
        </select>
        <p v-if="form.errors.role" class="text-danger text-sm mt-1">{{ form.errors.role }}</p>
      </div>

      <div>
        <label for="password" class="form-label">Password</label>
        <input id="password" v-model="form.password" type="password" class="form-control" autocomplete="new-password" required>
        <p v-if="form.errors.password" class="text-danger text-sm mt-1">{{ form.errors.password }}</p>
      </div>

      <div>
        <label for="password_confirmation" class="form-label">Confirm password</label>
        <input id="password_confirmation" v-model="form.password_confirmation" type="password" class="form-control" autocomplete="new-password" required>
      </div>

      <button type="submit" class="ti-btn ti-btn-primary w-full" :disabled="form.processing">
        {{ form.processing ? 'Creating account...' : 'Create account' }}
      </button>
    </form>

    <p class="text-center text-sm text-textmuted mt-5">
      Already registered?
      <Link href="/login" class="text-primary">Sign in</Link>
    </p>
  </div>
</template>
