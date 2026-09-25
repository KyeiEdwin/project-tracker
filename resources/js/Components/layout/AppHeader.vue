<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { Link, useForm, usePage } from '@inertiajs/vue3'

const props = defineProps({
  isSidebarOpen: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['toggle-dark', 'toggle-sidebar'])
const page = usePage()
const logoutForm = useForm({})
const user = computed(() => page.props.auth?.user)
const teamMember = computed(() => page.props.auth?.teamMember)
const authenticatedAccount = computed(() => user.value || teamMember.value)
const isTeamMember = computed(() => Boolean(teamMember.value && !user.value))
const homeUrl = computed(() => isTeamMember.value ? '/team-member/dashboard' : '/')

const isSearchOpen = ref(false)
const searchQuery = ref('')
const isHeaderVisible = ref(true)
const lastScrollY = ref(0)

const toggleSearch = () => {
  isSearchOpen.value = !isSearchOpen.value
}

const toggleFullscreen = () => {
  if (!document.fullscreenElement) {
    document.documentElement.requestFullscreen()
  } else {
    document.exitFullscreen()
  }
}

const logout = () => {
  logoutForm.post(isTeamMember.value ? '/team-member/logout' : '/logout', {
    preserveScroll: true,
  })
}

const handleScroll = () => {
  const currentScrollY = window.scrollY
  
  // If at the top of the page, always show header
  if (currentScrollY <= 10) {
    isHeaderVisible.value = true
  }
  // If scrolling down, hide header
  else if (currentScrollY > lastScrollY.value && currentScrollY > 100) {
    isHeaderVisible.value = false
  }
  // If scrolling up, show header
  else if (currentScrollY < lastScrollY.value) {
    isHeaderVisible.value = true
  }
  
  lastScrollY.value = currentScrollY
}

onMounted(() => {
  // Add scroll listener for navbar collapse behavior
  window.addEventListener('scroll', handleScroll, { passive: true })
  
  // Initialize scroll position
  lastScrollY.value = window.scrollY
})

onUnmounted(() => {
  window.removeEventListener('scroll', handleScroll)
})
</script>

<template>
  <header 
    class="app-header" 
    :class="{ 'header-hidden': !isHeaderVisible }"
    id="header"
  >
    <div class="main-header-container container-fluid">
      <!-- Header Content Left -->
      <div class="header-content-left">
        <!-- Hamburger Menu Button -->
        <div class="header-element">
          <button 
            class="hamburger-menu header-link"
            @click="$emit('toggle-sidebar')"
            aria-label="Toggle Sidebar"
          >
            <i 
              v-if="!isSidebarOpen" 
              class="ri-menu-line text-xl"
            ></i>
            <i 
              v-else 
              class="ri-close-line text-xl"
            ></i>
          </button>
        </div>
        
        <!-- KEDEBAH Logo - Clickable to Homepage -->
        <div class="header-element logo-container">
          <Link class="header-logo" :href="homeUrl" aria-label="Go to Homepage">
            <img 
              alt="KEDEBAH ERP Logo" 
              class="kedebah-logo" 
              src="/images/Kedebah Logo.png"
            />
          </Link>
        </div>
        
        <!-- Desktop Search -->
        <div class="header-element header-search md:!block !hidden my-auto">
          <input 
            v-model="searchQuery"
            autocomplete="off" 
            class="header-search-bar form-control" 
            placeholder="Search anything here ..." 
            type="text"
          />
          <a class="header-search-icon border-0" href="javascript:void(0);">
            <i class="ri-search-line"></i>
          </a>
        </div>
      </div>
      
      <!-- Header Content Right -->
      <ul class="header-content-right">
        <!-- Mobile Search -->
        <li class="header-element md:!hidden block">
          <a class="header-link" href="javascript:void(0);" @click="toggleSearch">
            <i class="bi bi-search header-link-icon"></i>
          </a>
        </li>
        
        <!-- Dark Mode Toggle -->
        <li class="header-element">
          <a class="header-link" href="javascript:void(0);" @click="$emit('toggle-dark')" aria-label="Toggle Dark Mode">
            <i class="ri-moon-line header-link-icon"></i>
          </a>
        </li>
        
        <!-- Fullscreen -->
        <li class="header-element header-fullscreen">
          <a class="header-link" href="javascript:void(0);" @click="toggleFullscreen" aria-label="Toggle Fullscreen">
            <i class="ri-fullscreen-line header-link-icon"></i>
          </a>
        </li>
        
        <!-- Notifications -->
        <li class="header-element notifications-dropdown">
          <a class="header-link" href="javascript:void(0);" aria-label="Notifications">
            <i class="ri-notification-3-line header-link-icon"></i>
            <span class="header-icon-pulse bg-primary rounded pulse pulse-secondary"></span>
          </a>
        </li>
        
        <!-- User Info (No Dropdown) -->
        <li v-if="authenticatedAccount" class="header-element">
          <div class="flex items-center gap-3 px-3">
            <span class="avatar avatar-sm bg-primary text-white font-semibold">
              {{ authenticatedAccount.name?.slice(0, 2).toUpperCase() }}
            </span>
            <div class="hidden lg:block text-left">
              <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                {{ authenticatedAccount.name?.split(' ')[0] }}
              </div>
              <div class="text-xs text-gray-500 dark:text-gray-400">
                {{ isTeamMember ? 'Team Member' : 'Admin' }}
              </div>
            </div>
          </div>
        </li>
        
        <!-- Sign Out Button -->
        <li v-if="authenticatedAccount" class="header-element">
          <button 
            type="button"
            @click="logout"
            :disabled="logoutForm.processing"
            class="ti-btn ti-btn-danger ti-btn-sm flex items-center gap-2"
            aria-label="Sign Out"
          >
            <i class="ri-logout-box-line"></i>
            <span class="hidden md:inline">{{ logoutForm.processing ? 'Signing out...' : 'Sign Out' }}</span>
          </button>
        </li>
        <li v-else class="header-element">
          <Link class="ti-btn ti-btn-primary ti-btn-sm" href="/login">
            <i class="ri-login-box-line me-1"></i>Sign in
          </Link>
        </li>
      </ul>
    </div>
  </header>
</template>

<style scoped>
/* Header Base Styles - Modernized */
.app-header {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  height: 64px;
  z-index: 100;
  background-color: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(12px);
  border-bottom: 1px solid rgba(0, 0, 0, 0.06);
  transition: all 0.3s ease-in-out;
  transform: translateY(0);
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
}

/* Header Collapse Animation */
.app-header.header-hidden {
  transform: translateY(-100%);
}

/* Dark Mode Header - Enhanced */
.dark .app-header {
  background-color: rgba(15, 23, 42, 0.95);
  border-bottom-color: rgba(255, 255, 255, 0.08);
  box-shadow: 0 4px 16px rgba(0, 0, 0, 0.3);
}

/* Hamburger Menu - Simplified with Icons */
.hamburger-menu {
  display: flex;
  align-items: center;
  justify-content: center;
  background: transparent;
  border: none;
  cursor: pointer;
  padding: 0.5rem;
  z-index: 10;
  transition: all 0.2s ease;
  color: #374151;
}

/* Dark Mode Hamburger */
.dark .hamburger-menu {
  color: #d1d5db;
}

.hamburger-menu i {
  transition: all 0.2s ease;
}

.hamburger-menu:hover i {
  color: rgb(22, 163, 74);
  transform: scale(1.1);
}

.dark .hamburger-menu:hover i {
  color: rgb(134, 239, 172);
}

/* Logo Container */
.logo-container {
  margin-left: 0.5rem;
}

.header-logo {
  display: flex;
  align-items: center;
  text-decoration: none;
  transition: opacity 0.2s ease;
}

.header-logo:hover {
  opacity: 0.85;
}

.kedebah-logo {
  max-height: 42px;
  height: auto;
  width: auto;
  object-fit: contain;
  display: block;
}

/* Header Icons - Modernized */
.header-link-icon {
  font-size: 1.25rem !important;
  line-height: 1 !important;
  display: inline-flex !important;
  align-items: center !important;
  justify-content: center !important;
  color: #374151;
}

/* Dark Mode Icons - Enhanced */
.dark .header-link-icon {
  color: #d1d5db;
}

.header-link {
  display: inline-flex !important;
  align-items: center !important;
  justify-content: center !important;
  padding: 0.625rem !important;
  border-radius: 0.75rem;
  transition: all 0.2s ease;
  color: #374151;
  position: relative;
}

/* Dark Mode Header Links - Enhanced */
.dark .header-link {
  color: #d1d5db;
}

.header-link:hover {
  background-color: rgba(22, 163, 74, 0.1);
  color: rgb(22, 163, 74);
  transform: scale(1.05);
}

.dark .header-link:hover {
  background-color: rgba(134, 239, 172, 0.15);
  color: rgb(134, 239, 172);
}

/* Avatar - Enhanced */
.avatar {
  display: inline-flex !important;
  align-items: center !important;
  justify-content: center !important;
  transition: transform 0.2s ease;
  width: 2rem;
  height: 2rem;
  border-radius: 0.5rem;
  font-size: 0.875rem;
}

.avatar-sm {
  width: 2rem;
  height: 2rem;
}

/* Search Bar Dark Mode - Enhanced */
.dark .header-search-bar {
  background-color: rgba(255, 255, 255, 0.05);
  border-color: rgba(255, 255, 255, 0.1);
  color: #e5e7eb;
}

.dark .header-search-bar::placeholder {
  color: rgba(229, 231, 235, 0.5);
}

.dark .header-search-icon {
  color: #d1d5db;
}

/* Enhanced Search Bar Styling */
.header-search-bar {
  padding-left: 3rem !important;
  padding-right: 1rem !important;
  padding-top: 0.75rem !important;
  padding-bottom: 0.75rem !important;
  border-radius: 0.75rem;
  border: 1px solid #e5e7eb;
  transition: all 0.2s ease;
}

.header-search-bar:focus {
  outline: none;
  border-color: rgb(22, 163, 74);
  box-shadow: 0 0 0 3px rgba(22, 163, 74, 0.1);
  transform: translateY(-1px);
}

.dark .header-search-bar:focus {
  border-color: rgb(134, 239, 172);
  box-shadow: 0 0 0 3px rgba(134, 239, 172, 0.1);
}

.header-search-icon {
  position: absolute;
  left: 1rem;
  top: 50%;
  transform: translateY(-50%);
  font-size: 1.125rem;
  color: #9ca3af;
  pointer-events: none;
}

.header-search {
  position: relative;
  width: 100%;
  max-width: 400px;
}

/* Notification Pulse - Enhanced Visibility in Dark Mode */
.header-icon-pulse {
  position: absolute;
  top: 8px;
  right: 8px;
  width: 8px;
  height: 8px;
  animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

@keyframes pulse {
  0%, 100% {
    opacity: 1;
  }
  50% {
    opacity: .5;
  }
}

.dark .header-icon-pulse {
  box-shadow: 0 0 8px rgba(34, 197, 94, 0.6);
}

/* Ensure header content is properly aligned */
.main-header-container {
  display: flex;
  align-items: center;
  justify-content: space-between;
  width: 100%;
  padding: 0 1rem;
  height: 100%;
}

.header-content-left {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.header-content-right {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  list-style: none;
  margin: 0;
  padding: 0;
}

.header-content-right > li {
  display: flex;
  align-items: center;
}

/* Mobile Adjustments */
@media (max-width: 768px) {
  .logo-container {
    margin-left: 0.25rem;
  }
  
  .kedebah-logo {
    max-height: 36px;
  }
  
  .header-content-left {
    gap: 0.5rem;
  }
}
</style>
