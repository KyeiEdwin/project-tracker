<script setup>
import { computed } from 'vue'
import { useForm, usePage } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import PageHeader from '@/Components/ui/PageHeader.vue'

defineOptions({ layout: AppLayout })

const props = defineProps({
  member: { type: Object, required: true },
  team: { type: Object, default: null },
})

const page = usePage()
const form = useForm({
  name: props.member.name || '',
  email: props.member.email || '',
})
const successMessage = computed(() => page.props.flash?.success)

const submit = () => form.put('/team-member/profile', { preserveScroll: true })
</script>

<template>
  <div>
    <PageHeader title="My Profile" subtitle="Manage your personal account information" />

    <div v-if="successMessage" class="alert alert-success mb-6">{{ successMessage }}</div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
      <section class="box lg:col-span-2">
        <div class="box-header"><h2 class="box-title">Profile details</h2></div>
        <form class="box-body space-y-4" @submit.prevent="submit">
          <div>
            <label for="profile-name" class="form-label">Full name</label>
            <input id="profile-name" v-model="form.name" type="text" class="form-control" required>
            <p v-if="form.errors.name" class="mt-1 text-sm text-danger">{{ form.errors.name }}</p>
          </div>
          <div>
            <label for="profile-email" class="form-label">Email</label>
            <input id="profile-email" v-model="form.email" type="email" class="form-control" required>
            <p v-if="form.errors.email" class="mt-1 text-sm text-danger">{{ form.errors.email }}</p>
          </div>
          <div class="flex justify-end">
            <button type="submit" class="ti-btn ti-btn-primary" :disabled="form.processing">
              {{ form.processing ? 'Saving...' : 'Save changes' }}
            </button>
          </div>
        </form>
      </section>

      <section class="box">
        <div class="box-header"><h2 class="box-title">Work details</h2></div>
        <dl class="box-body space-y-4">
          <div><dt class="text-sm text-textmuted">Team</dt><dd class="font-medium">{{ team?.name || 'Not assigned' }}</dd></div>
          <div><dt class="text-sm text-textmuted">Role</dt><dd class="font-medium capitalize">{{ member.role }}</dd></div>
          <div><dt class="text-sm text-textmuted">Status</dt><dd class="font-medium capitalize">{{ member.status }}</dd></div>
        </dl>
      </section>
    </div>
  </div>
</template>
