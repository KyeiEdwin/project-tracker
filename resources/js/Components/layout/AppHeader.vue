<script setup>
import { ref, computed, onMounted, onUnmounted, nextTick } from 'vue'
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
const isProfileDropdownOpen = ref(false)
const isHeaderVisible = ref(true)
const lastScrollY = ref(0)
const dropdownPosition = ref({ top: 0, right: 0 })

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

const calculateDropdownPosition = () => {
  const profileButton = document.getElementById('headerProfileDropdown')
  if (profileButton) {
    const rect = profileButton.getBoundingClientRect()
    dropdownPosition.value = {
      top: rect.bottom + 8, // 8px gap below the button
      right: window.innerWidth - rect.right
    }
  }
}

const toggleProfileDropdown = () => {
  isProfileDropdownOpen.value = !isProfileDropdownOpen.value
  if (isProfileDropdownOpen.value) {
    nextTick(() => {
      calculateDropdownPosition()
    })
  }
}

const logout = () => {
  isProfileDropdownOpen.value = false
  logoutForm.post(isTeamMember.value ? '/team-member/logout' : '/logout', {
    preserveScroll: true,
  })
}

const handleOutsideClick = (e) => {
  // Only close if dropdown is open
  if (!isProfileDropdownOpen.value) return
  
  const profileButton = document.getElementById('headerProfileDropdown')
  const dropdownMenu = document.querySelector('.header-profile-dropdown')
  
  // Check if click is outside both button and dropdown menu
  if (profileButton && dropdownMenu) {
    const isClickOnButton = profileButton.contains(e.target)
    const isClickOnDropdown = dropdownMenu.contains(e.target)
    
    if (!isClickOnButton && !isClickOnDropdown) {
      isProfileDropdownOpen.value = false
    }
  }
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
    // Close dropdown when header hides
    isProfileDropdownOpen.value = false
  }
  // If scrolling up, show header
  else if (currentScrollY < lastScrollY.value) {
    isHeaderVisible.value = true
  }
  
  lastScrollY.value = currentScrollY
  
  // Recalculate dropdown position if it's open
  if (isProfileDropdownOpen.value) {
    calculateDropdownPosition()
  }
}

onMounted(() => {
  // Close dropdown when clicking outside
  document.addEventListener('click', handleOutsideClick)
  
  // Add scroll listener for navbar collapse behavior
  window.addEventListener('scroll', handleScroll, { passive: true })
  
  // Recalculate dropdown position on window resize
  window.addEventListener('resize', () => {
    if (isProfileDropdownOpen.value) {
      calculateDropdownPosition()
    }
  })
  
  // Initialize scroll position
  lastScrollY.value = window.scrollY
})

onUnmounted(() => {
  document.removeEventListener('click', handleOutsideClick)
  window.removeEventListener('scroll', handleScroll)
  window.removeEventListener('resize', calculateDropdownPosition)
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
            class="header-search-bar form-control pl-10" 
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
        
        <!-- Profile -->
        <li v-if="authenticatedAccount" class="header-element ti-dropdown relative">
          <a 
            class="header-link ti-dropdown-toggle" 
            href="javascript:void(0);"
            id="headerProfileDropdown"
            @click="toggleProfileDropdown"
            :aria-expanded="isProfileDropdownOpen"
            aria-label="User Profile"
          >
            <div class="flex items-center gap-2">
              <span class="avatar avatar-sm bg-primary text-white font-semibold">
                {{ authenticatedAccount.name?.slice(0, 2).toUpperCase() }}
              </span>
              <div class="hidden xl:block text-left">
                <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                  {{ authenticatedAccount.name?.split(' ')[0] }}
                </div>
                <div class="text-xs text-gray-500 dark:text-gray-400">
                  {{ isTeamMember ? 'Team Member' : 'Admin' }}
                </div>
              </div>
              <i class="ri-arrow-down-s-line text-sm opacity-70"></i>
            </div>
          </a>
        </li>
        <li v-else class="header-element">
          <Link class="ti-btn ti-btn-primary ti-btn-sm" href="/login">
            <i class="ri-login-box-line me-1"></i>Sign in
          </Link>
        </li>
      </ul>
    </div>
    
    <!-- Profile Dropdown using Teleport to render outside header container -->
    <Teleport to="body">
      <ul 
        v-if="isProfileDropdownOpen && authenticatedAccount"
        class="header-profile-dropdown"
        :style="{
          position: 'fixed',
          top: dropdownPosition.top + 'px',
          right: dropdownPosition.right + 'px',
          zIndex: 9999
        }"
        aria-labelledby="headerProfileDropdown"
        @click.stop
      >
        <li>
          <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-700">
            <div class="flex items-center gap-3">
              <span class="avatar bg-primary text-white font-semibold">
                {{ authenticatedAccount.name?.slice(0, 2).toUpperCase() }}
              </span>
              <div class="flex-1 min-w-0">
                <div class="text-sm font-semibold text-gray-900 dark:text-gray-100 truncate">
                  {{ authenticatedAccount.name }}
                </div>
                <div class="text-xs text-gray-500 dark:text-gray-400 truncate">
                  {{ authenticatedAccount.email }}
                </div>
                <span 
                  v-if="isTeamMember" 
                  class="inline-flex items-center mt-1 px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400"
                >
                  Team Member
                </span>
                <span 
                  v-else 
                  class="inline-flex items-center mt-1 px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400"
                >
                  Administrator
                </span>
              </div>
            </div>
          </div>
        </li>
        <li>
          <Link 
            class="ti-dropdown-item flex items-center" 
            :href="isTeamMember ? '/team-member/profile' : '/profile'" 
            @click="isProfileDropdownOpen = false"
          >
            <i class="ri-user-line me-2"></i>
            <span>My Profile</span>
          </Link>
        </li>
        <li>
          <Link 
            class="ti-dropdown-item flex items-center" 
            :href="isTeamMember ? '/team-member/settings' : '/settings'" 
            @click="isProfileDropdownOpen = false"
          >
            <i class="ri-settings-3-line me-2"></i>
            <span>Settings</span>
          </Link>
        </li>
        <li v-if="!isTeamMember">
          <Link 
            class="ti-dropdown-item flex items-center" 
            href="/sessions" 
            @click="isProfileDropdownOpen = false"
          >
            <i class="ri-shield-user-line me-2"></i>
            <span>Active Sessions</span>
          </Link>
        </li>
        <li class="border-t border-gray-100 dark:border-gray-700">
          <button 
            type="button" 
            class="ti-dropdown-item flex items-center w-full text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20" 
            :disabled="logoutForm.processing" 
            @click="logout"
          >
            <i class="ri-logout-box-line me-2"></i>
            <span>{{ logoutForm.processing ? 'Logging out...' : 'Sign Out' }}</span>
          </button>
        </li>
      </ul>
    </Teleport>
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

.header-link:hover .avatar {
  transform: scale(1.05);
}

/* Ensure relative positioning for dropdown parent */
.ti-dropdown {
  position: relative;
}

/* Profile Dropdown - Modernized with Teleport */
.header-profile-dropdown {
  min-width: 280px;
  max-width: 320px;
  background-color: white;
  border: 1px solid rgba(0, 0, 0, 0.06);
  border-radius: 1rem;
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15), 0 0 0 1px rgba(0, 0, 0, 0.02);
  list-style: none;
  padding: 0;
  margin: 0;
  animation: dropdownSlideIn 0.2s cubic-bezier(0.16, 1, 0.3, 1);
  transform-origin: top right;
}

.header-profile-dropdown.profile-dropdown-open {
  opacity: 1;
}

@keyframes dropdownSlideIn {
  from {
    opacity: 0;
    transform: translateY(-8px) scale(0.96);
  }
  to {
    opacity: 1;
    transform: translateY(0) scale(1);
  }
}

.dark .header-profile-dropdown {
  background-color: rgb(30, 41, 59);
  border-color: rgba(255, 255, 255, 0.08);
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.6), 0 0 0 1px rgba(255, 255, 255, 0.05);
}

/* Dropdown Items */
.header-profile-dropdown > li {
  list-style: none;
  margin: 0;
  padding: 0;
}

.ti-dropdown-item {
  display: block;
  padding: 0.75rem 1rem;
  color: #374151;
  text-decoration: none;
  transition: all 0.2s ease;
  cursor: pointer;
  border: none;
  background: transparent;
  width: 100%;
  text-align: left;
  font-size: 0.875rem;
}

.dark .ti-dropdown-item {
  color: #e5e7eb;
}

.ti-dropdown-item:hover {
  background-color: rgba(22, 163, 74, 0.08);
  color: rgb(22, 163, 74);
}

.dark .ti-dropdown-item:hover {
  background-color: rgba(134, 239, 172, 0.1);
  color: rgb(134, 239, 172);
}

.ti-dropdown-item i {
  font-size: 1.125rem;
  width: 1.25rem;
}

.ti-dropdown-item:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.ti-dropdown-item:disabled:hover {
  background-color: transparent;
  color: inherit;
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
