<script setup>
import { ref, computed, onMounted, nextTick } from 'vue'
import { usePage } from '@inertiajs/vue3'
import AppHeader from '../Components/layout/AppHeader.vue'
import AppSidebar from '../Components/layout/AppSidebar.vue'
import AppFooter from '../Components/layout/AppFooter.vue'

const page = usePage()
const isDarkMode = ref(false)

// Check if current route is an auth page (no layout needed)
const isAuthPage = computed(() => {
  return page.props.isAuthPage || false
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
          <slot />
        </div>
      </div>
      <AppFooter />
    </template>
    
    <!-- Auth Layout (no header/sidebar) -->
    <template v-else>
      <slot />
    </template>
  </div>
</template>

<style>
/* Global app styles */
</style>
