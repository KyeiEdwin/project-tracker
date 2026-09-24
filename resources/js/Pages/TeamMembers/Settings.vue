<script setup>
import { computed } from 'vue'
import { useForm, usePage } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import PageHeader from '@/Components/ui/PageHeader.vue'

defineOptions({ layout: AppLayout })

const props = defineProps({
  member: { type: Object, required: true },
})

const page = usePage()
const form = useForm({
  current_password: '',
  password: '',
  password_confirmation: '',
})
const successMessage = computed(() => page.props.flash?.success)

const submit = () => form.patch('/team-member/settings/password', {
  preserveScroll: true,
  onSuccess: () => form.reset(),
})
</script>

<template>
  <div>
    <PageHeader title="Settings" subtitle="Manage your account security" />

    <div v-if="successMessage" class="alert alert-success mb-6">{{ successMessage }}</div>

    <section class="box max-w-2xl">
      <div class="box-header"><h2 class="box-title">Change password</h2></div>
      <form class="box-body space-y-4" @submit.prevent="submit">
        <div>
          <label for="current-password" class="form-label">Current password</label>
          <input id="current-password" v-model="form.current_password" type="password" class="form-control" autocomplete="current-password" required>
          <p v-if="form.errors.current_password" class="mt-1 text-sm text-danger">{{ form.errors.current_password }}</p>
        </div>
        <div>
          <label for="new-password" class="form-label">New password</label>
          <input id="new-password" v-model="form.password" type="password" class="form-control" autocomplete="new-password" required>
          <p v-if="form.errors.password" class="mt-1 text-sm text-danger">{{ form.errors.password }}</p>
        </div>
        <div>
          <label for="password-confirmation" class="form-label">Confirm new password</label>
          <input id="password-confirmation" v-model="form.password_confirmation" type="password" class="form-control" autocomplete="new-password" required>
        </div>
        <div class="flex justify-end">
          <button type="submit" class="ti-btn ti-btn-primary" :disabled="form.processing">
            {{ form.processing ? 'Updating...' : 'Update password' }}
          </button>
        </div>
      </form>
    </section>
  </div>
</template>
