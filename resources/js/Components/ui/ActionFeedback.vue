<script setup>
import { onBeforeUnmount, onMounted, ref } from 'vue'
const message = ref('')
const visible = ref(false)
let timer
const show = (event) => { message.value = event.detail?.message || 'Action completed.'; visible.value = true; clearTimeout(timer); timer = setTimeout(() => { visible.value = false }, 3200) }
onMounted(() => window.addEventListener('workspace-action', show))
onBeforeUnmount(() => { window.removeEventListener('workspace-action', show); clearTimeout(timer) })
</script>
<template><Transition name="action-toast"><div v-if="visible" class="action-toast" role="status" aria-live="polite"><i class="ri-checkbox-circle-line"></i><span>{{ message }}</span></div></Transition></template>
