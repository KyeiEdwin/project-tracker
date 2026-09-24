<script setup>
import { computed, ref } from 'vue'
import { router, useForm } from '@inertiajs/vue3'
import PageHeader from '@/Components/ui/PageHeader.vue'

const props = defineProps({
  title: { type: String, required: true },
  subtitle: { type: String, default: '' },
  endpoint: { type: String, required: true },
  items: { type: Array, default: () => [] },
  fields: { type: Array, required: true },
  label: { type: String, default: 'Record' },
  projects: { type: Array, default: () => [] },
  teamMembers: { type: Array, default: () => [] },
})

const isOpen = ref(false)
const editing = ref(null)
const message = ref('')
const defaults = () => Object.fromEntries(props.fields.map((field) => [field.name, field.default ?? (field.multiple ? [] : '')]))
const form = useForm(defaults())
const columns = computed(() => props.fields.filter((field) => field.list !== false).slice(0, 6))

const inputOptions = (field) => {
  if (field.options) return field.options
  if (field.source === 'projects') return props.projects.map((project) => ({ value: project.id, label: project.name }))
  if (field.source === 'teamMembers') return props.teamMembers.map((member) => ({ value: member.id, label: member.name }))
  return []
}

const display = (item, field) => {
  const value = item[field.display ?? field.name] ?? item[field.camelName]
  if (field.type === 'boolean') return value ? 'Yes' : 'No'
  if (Array.isArray(value)) return value.length ? value.join(', ') : '—'
  return value === null || value === undefined || value === '' ? '—' : value
}

const openCreate = () => {
  editing.value = null
  form.clearErrors()
  form.defaults(defaults())
  form.reset()
  isOpen.value = true
}

const openEdit = (item) => {
  editing.value = item
  form.clearErrors()
  const values = Object.fromEntries(props.fields.map((field) => [field.name, item[field.name] ?? item[field.camelName] ?? field.default ?? '']))
  form.defaults(values)
  form.reset()
  isOpen.value = true
}

const close = () => {
  if (!form.processing) isOpen.value = false
}

const submit = () => {
  const options = { preserveScroll: true, onSuccess: () => { message.value = `${props.label} ${editing.value ? 'updated' : 'created'}.`; isOpen.value = false } }
  if (editing.value) form.put(`${props.endpoint}/${editing.value.id}`, options)
  else form.post(props.endpoint, options)
}

const destroy = (item) => {
  if (!window.confirm(`Delete this ${props.label.toLowerCase()}?`)) return
  router.delete(`${props.endpoint}/${item.id}`, { preserveScroll: true, onSuccess: () => { message.value = `${props.label} deleted.` } })
}
</script>

<template>
  <div>
    <PageHeader :title="title" :subtitle="subtitle">
      <template #actions>
        <button class="ti-btn ti-btn-primary" type="button" @click="openCreate"><i class="ri-add-line me-1"></i>New {{ label }}</button>
      </template>
    </PageHeader>

    <p v-if="message" class="mb-4 rounded bg-success/10 px-4 py-3 text-success">{{ message }}</p>
    <div class="box"><div class="box-body p-0"><div class="table-responsive"><table class="table table-hover whitespace-nowrap">
      <thead><tr><th v-for="field in columns" :key="field.name">{{ field.label }}</th><th>Actions</th></tr></thead>
      <tbody>
        <tr v-if="!items.length"><td :colspan="columns.length + 1" class="py-8 text-center text-textmuted">No {{ label.toLowerCase() }} records yet.</td></tr>
        <tr v-for="item in items" :key="item.id"><td v-for="field in columns" :key="field.name">{{ display(item, field) }}</td><td><div class="flex gap-1"><button class="ti-btn ti-btn-soft-info ti-btn-sm" type="button" @click="openEdit(item)">Edit</button><button class="ti-btn ti-btn-soft-danger ti-btn-sm" type="button" @click="destroy(item)">Delete</button></div></td></tr>
      </tbody>
    </table></div></div></div>

    <div v-if="isOpen" class="fixed inset-0 z-[80] flex items-center justify-center bg-black/40 p-4"><form class="w-full max-w-2xl rounded-xl bg-white shadow-xl dark:bg-bgdark" @submit.prevent="submit">
      <div class="flex items-center justify-between border-b px-6 py-4"><h3 class="font-semibold">{{ editing ? `Edit ${label}` : `New ${label}` }}</h3><button class="ti-btn ti-btn-sm ti-btn-light" type="button" :disabled="form.processing" @click="close">Close</button></div>
      <div class="grid max-h-[65vh] grid-cols-1 gap-4 overflow-y-auto px-6 py-5 md:grid-cols-2">
        <label v-for="field in fields" :key="field.name" :class="field.full ? 'md:col-span-2' : ''" class="block"><span class="ti-form-label">{{ field.label }}<span v-if="field.required" class="text-danger"> *</span></span>
          <textarea v-if="field.type === 'textarea'" v-model="form[field.name]" class="ti-form-control" :rows="3" />
          <select v-else-if="field.type === 'select'" v-model="form[field.name]" class="ti-form-select" :multiple="field.multiple"><option v-if="!field.multiple" value="">Select {{ field.label }}</option><option v-for="option in inputOptions(field)" :key="option.value" :value="option.value">{{ option.label }}</option></select>
          <input v-else-if="field.type === 'boolean'" v-model="form[field.name]" class="ti-form-check-input" type="checkbox">
          <input v-else v-model="form[field.name]" class="ti-form-control" :type="field.type || 'text'">
          <span v-if="form.errors[field.name]" class="mt-1 block text-xs text-danger">{{ form.errors[field.name] }}</span>
        </label>
      </div>
      <div class="flex justify-end gap-3 border-t px-6 py-4"><button class="ti-btn ti-btn-light" type="button" :disabled="form.processing" @click="close">Cancel</button><button class="ti-btn ti-btn-primary" type="submit" :disabled="form.processing">{{ form.processing ? 'Saving…' : 'Save' }}</button></div>
    </form></div>
  </div>
</template>
