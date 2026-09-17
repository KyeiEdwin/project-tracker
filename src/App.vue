<script setup>
import { ref, computed, onMounted, nextTick } from 'vue'
import { useRoute } from 'vue-router'
import AppHeader from './components/layout/AppHeader.vue'
import AppSidebar from './components/layout/AppSidebar.vue'
import AppFooter from './components/layout/AppFooter.vue'

const route = useRoute()
const isDarkMode = ref(false)

// Check if current route is an auth page (no layout needed)
const isAuthPage = computed(() => {
  return route.meta?.layout === 'auth'
})

const toggleDarkMode = () => {
  isDarkMode.value = !isDarkMode.value
  document.documentElement.classList.toggle('dark', isDarkMode.value)
}

onMounted(() => {
  // Set initial layout attributes
  document.documentElement.setAttribute('data-nav-layout', 'horizontal')
  document.documentElement.setAttribute('data-nav-style', 'menu-click')
  document.documentElement.setAttribute('data-menu-styles', 'light')
  document.documentElement.setAttribute('data-header-styles', 'light')
  
  // Initialize Preline for dropdowns after DOM is ready
  nextTick(() => {
    setTimeout(() => {
      // Preline should auto-initialize, but trigger it manually for Vue components
      if (window.HSStaticMethods && window.HSStaticMethods.autoInit) {
        window.HSStaticMethods.autoInit()
      }
    }, 200)
  })
})
</script>

<template>
  <div class="page" :class="{ 'dark': isDarkMode }">
    <!-- Main Layout -->
    <template v-if="!isAuthPage">
      <AppHeader @toggle-dark="toggleDarkMode" />
      <AppSidebar />
      <div class="main-content app-content">
        <div class="container-fluid">
          <router-view />
        </div>
      </div>
      <AppFooter />
    </template>
    
    <!-- Auth Layout (no header/sidebar) -->
    <template v-else>
      <router-view />
    </template>
  </div>
</template>

<style>
/* Global app styles */
</style>

