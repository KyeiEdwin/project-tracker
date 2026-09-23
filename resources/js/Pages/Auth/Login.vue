<script setup>
import { useForm } from '@inertiajs/vue3'

const form = useForm({
  email: '',
  password: '',
  remember: false,
})

const submit = () => form.post('/login')
</script>

<template>
  <div class="min-h-screen flex items-center justify-center px-4 py-12 bg-light">
    <div class="w-full max-w-md box shadow-sm">
      <div class="box-body p-6 sm:p-8">
        <div class="mb-6">
          <h1 class="text-2xl font-semibold">Sign in</h1>
          <p class="text-textmuted mt-1">Sign in to access realtime project collaboration.</p>
        </div>

        <form class="space-y-4" @submit.prevent="submit">
          <div>
            <label for="email" class="form-label">Email</label>
            <input id="email" v-model="form.email" type="email" class="form-control" autocomplete="email" required>
            <p v-if="form.errors.email" class="text-danger text-sm mt-1">{{ form.errors.email }}</p>
          </div>

          <div>
            <label for="password" class="form-label">Password</label>
            <input id="password" v-model="form.password" type="password" class="form-control" autocomplete="current-password" required>
          </div>

          <label class="flex items-center gap-2 text-sm">
            <input v-model="form.remember" type="checkbox" class="form-check-input">
            Remember me
          </label>

          <button type="submit" class="ti-btn ti-btn-primary w-full" :disabled="form.processing">
            {{ form.processing ? 'Signing in...' : 'Sign in' }}
          </button>
        </form>
      </div>
    </div>
  </div>
</template>