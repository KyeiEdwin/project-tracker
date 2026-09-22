<script setup>
import { ref, computed } from 'vue'
import { usePage } from '@inertiajs/vue3'
import AppHeader from '../Components/layout/AppHeader.vue'
import AppSidebar from '../Components/layout/AppSidebar.vue'
import AppFooter from '../Components/layout/AppFooter.vue'
import ActionFeedback from '../Components/ui/ActionFeedback.vue'

const page = usePage()
const isDarkMode = ref(false)
const isSidebarOpen = ref(false)
const isAuthPage = computed(() => page.props.isAuthPage || false)
const toggleDarkMode = () => { isDarkMode.value = !isDarkMode.value; document.documentElement.classList.toggle('dark', isDarkMode.value) }
const handleWorkspaceAction = (event) => {
  const button = event.target.closest('button')
  if (!button || button.disabled || button.dataset.feedbackHandled === 'true' || button.type === 'submit') return
  const label = button.textContent.replace(/\s+/g, ' ').trim() || button.getAttribute('aria-label') || 'Action'
  window.dispatchEvent(new CustomEvent('workspace-action', { detail: { message: `${label} is ready.` } }))
}
</script>
<template>
  <div class="workspace-shell" :class="{ dark: isDarkMode }">
    <template v-if="!isAuthPage">
      <AppSidebar :open="isSidebarOpen" @navigate="isSidebarOpen = false" />
      <div v-if="isSidebarOpen" class="sidebar-scrim" @click="isSidebarOpen = false"></div>
      <main class="workspace-main"><AppHeader @toggle-dark="toggleDarkMode" @toggle-sidebar="isSidebarOpen = !isSidebarOpen" /><div class="workspace-content" @click.capture="handleWorkspaceAction"><slot /></div><AppFooter /><ActionFeedback /></main>
    </template>
    <slot v-else />
  </div>
</template>
