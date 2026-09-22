<script setup>
import { ref, computed } from 'vue'
import { useRoute } from 'vue-router'
import AppHeader from './components/layout/AppHeader.vue'
import AppSidebar from './components/layout/AppSidebar.vue'
import AppFooter from './components/layout/AppFooter.vue'

const route = useRoute()
const isDarkMode = ref(false)
const isSidebarOpen = ref(false)
const isAuthPage = computed(() => route.meta?.layout === 'auth')

const toggleDarkMode = () => {
  isDarkMode.value = !isDarkMode.value
  document.documentElement.classList.toggle('dark', isDarkMode.value)
}
</script>

<template>
  <div class="workspace-shell" :class="{ 'dark': isDarkMode }">
    <template v-if="!isAuthPage">
      <AppSidebar :open="isSidebarOpen" @navigate="isSidebarOpen = false" />
      <div v-if="isSidebarOpen" class="sidebar-scrim" @click="isSidebarOpen = false"></div>
      <main class="workspace-main">
        <AppHeader @toggle-dark="toggleDarkMode" @toggle-sidebar="isSidebarOpen = !isSidebarOpen" />
        <div class="workspace-content">
          <router-view />
        </div>
        <AppFooter />
      </main>
    </template>
    <router-view v-else />
  </div>
</template>
