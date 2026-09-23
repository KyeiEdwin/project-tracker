<script setup>
import { ref, computed, onMounted, nextTick, onUnmounted } from 'vue'
import { usePage } from '@inertiajs/vue3'
import AppHeader from '../Components/layout/AppHeader.vue'
import AppSidebar from '../Components/layout/AppSidebar.vue'
import AppFooter from '../Components/layout/AppFooter.vue'

const page = usePage()
const isDarkMode = ref(false)
const isSidebarOpen = ref(false)
const isMobile = ref(false)

// Check if current route is an auth page (no layout needed)
const isAuthPage = computed(() => {
  return page.props.isAuthPage || false
})

const toggleDarkMode = () => {
  isDarkMode.value = !isDarkMode.value
  document.documentElement.classList.toggle('dark', isDarkMode.value)
}

const toggleSidebar = () => {
  isSidebarOpen.value = !isSidebarOpen.value
}

const closeSidebar = () => {
  isSidebarOpen.value = false
}

const checkMobile = () => {
  isMobile.value = window.innerWidth < 992
  // Auto-close sidebar on mobile
  if (isMobile.value && isSidebarOpen.value) {
    isSidebarOpen.value = false
  }
}

onMounted(() => {
  // Set initial layout attributes
  document.documentElement.setAttribute('data-nav-layout', 'vertical')
  document.documentElement.setAttribute('data-nav-style', 'menu-click')
  document.documentElement.setAttribute('data-menu-styles', 'light')
  document.documentElement.setAttribute('data-header-styles', 'light')
  
  // Check initial screen size
  checkMobile()
  
  // Add resize listener
  window.addEventListener('resize', checkMobile)
  
  // Initialize Preline for dropdowns after DOM is ready
  nextTick(() => {
    setTimeout(() => {
      if (window.HSStaticMethods && window.HSStaticMethods.autoInit) {
        window.HSStaticMethods.autoInit()
      }
    }, 200)
  })
})

onUnmounted(() => {
  window.removeEventListener('resize', checkMobile)
})
</script>

<template>
  <div class="page" :class="{ 'dark': isDarkMode, 'sidebar-open': isSidebarOpen }">
    <!-- Main Layout -->
    <template v-if="!isAuthPage">
      <AppHeader 
        @toggle-dark="toggleDarkMode"
        @toggle-sidebar="toggleSidebar"
        :is-sidebar-open="isSidebarOpen"
      />
      <AppSidebar 
        :is-open="isSidebarOpen"
        @close="closeSidebar"
      />
      
      <!-- Overlay for mobile -->
      <div 
        v-if="isSidebarOpen && isMobile"
        class="sidebar-overlay"
        @click="closeSidebar"
      ></div>
      
      <div class="main-content app-content" :class="{ 'sidebar-expanded': isSidebarOpen && !isMobile }">
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

<style scoped>
/* Global app styles */
.sidebar-overlay {
  position: fixed;
  inset: 0;
  background-color: rgba(0, 0, 0, 0.5);
  z-index: 40;
  transition: opacity 0.3s ease;
}

.main-content {
  transition: margin-left 0.3s ease;
}

@media (min-width: 992px) {
  .main-content.sidebar-expanded {
    margin-left: 260px;
  }
}
</style>
