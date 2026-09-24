<script setup>
import { Link, useForm, usePage } from '@inertiajs/vue3'
import AuthLayout from '@/Layouts/AuthLayout.vue'

defineOptions({ layout: AuthLayout })

const page = usePage()
const form = useForm({})
const resend = () => form.post('/email/verification-notification')
</script>

<template>
  <div class="text-center">
    <h1 class="text-2xl font-semibold">Verify your email</h1>
    <p class="text-textmuted mt-2">
      We sent a verification link to {{ page.props.auth?.user?.email }}. Follow it to activate your team account.
    </p>

    <div v-if="page.props.flash?.success" class="alert alert-success mt-5">
      {{ page.props.flash.success }}
    </div>

    <form class="mt-6" @submit.prevent="resend">
      <button type="submit" class="ti-btn ti-btn-primary w-full" :disabled="form.processing">
        {{ form.processing ? 'Sending...' : 'Resend verification email' }}
      </button>
    </form>

    <Link href="/logout" method="post" as="button" class="text-primary text-sm mt-5">Sign out</Link>
  </div>
</template>
