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

const isSearchOpen = ref(false)
const searchQuery = ref('')
const isProfileDropdownOpen = ref(false)
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

const toggleProfileDropdown = () => {
  isProfileDropdownOpen.value = !isProfileDropdownOpen.value
}

const logout = () => {
  logoutForm.post('/logout', {
    preserveScroll: true,
  })
}

const handleOutsideClick = (e) => {
  const profileDropdown = document.getElementById('headerProfileDropdown')
  if (profileDropdown && !profileDropdown.closest('.header-element')?.contains(e.target)) {
    isProfileDropdownOpen.value = false
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
  }
  // If scrolling up, show header
  else if (currentScrollY < lastScrollY.value) {
    isHeaderVisible.value = true
  }
  
  lastScrollY.value = currentScrollY
}

onMounted(() => {
  // Close dropdown when clicking outside
  document.addEventListener('click', handleOutsideClick)
  
  // Add scroll listener for navbar collapse behavior
  window.addEventListener('scroll', handleScroll, { passive: true })
  
  // Initialize scroll position
  lastScrollY.value = window.scrollY
})

onUnmounted(() => {
  document.removeEventListener('click', handleOutsideClick)
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
            :class="{ 'active': isSidebarOpen }"
            @click="$emit('toggle-sidebar')"
            aria-label="Toggle Sidebar"
          >
            <span></span>
            <span></span>
            <span></span>
          </button>
        </div>
        
        <!-- KEDEBAH Logo - Clickable to Homepage -->
        <div class="header-element logo-container">
          <Link class="header-logo" href="/" aria-label="Go to Homepage">
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
        
        <!-- Profile -->
        <li v-if="user" class="header-element ti-dropdown hs-dropdown">
          <a 
            class="header-link hs-dropdown-toggle ti-dropdown-toggle" 
            href="javascript:void(0);"
            id="headerProfileDropdown"
            @click="toggleProfileDropdown"
            :aria-expanded="isProfileDropdownOpen"
            aria-label="User Profile"
          >
            <div class="flex items-center">
              <span class="avatar avatar-sm bg-primary text-white">{{ user.name?.slice(0, 2).toUpperCase() }}</span>
            </div>
          </a>
          <ul 
            v-show="isProfileDropdownOpen"
            class="main-header-dropdown hs-dropdown-menu ti-dropdown-menu pt-0 overflow-hidden header-profile-dropdown"
            aria-labelledby="headerProfileDropdown"
          >
            <li>
              <div class="ti-dropdown-item text-center border-b block">
                <span>{{ user.name }}</span>
                <span class="block text-xs text-textmuted">{{ user.email }}</span>
              </div>
            </li>
            <li><a class="ti-dropdown-item flex items-center" href="javascript:void(0);"><i class="ri-user-line me-2"></i>Profile</a></li>
            <li><a class="ti-dropdown-item flex items-center" href="javascript:void(0);"><i class="ri-settings-3-line me-2"></i>Settings</a></li>
            <li class="border-t">
              <button type="button" class="ti-dropdown-item flex items-center w-full text-start" :disabled="logoutForm.processing" @click="logout">
                <i class="ri-logout-box-line me-2"></i>{{ logoutForm.processing ? 'Logging out...' : 'Log Out' }}
              </button>
            </li>
          </ul>
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
/* Header Base Styles */
.app-header {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  height: 64px;
  z-index: 100;
  background-color: #fff;
  border-bottom: 1px solid rgba(0, 0, 0, 0.1);
  transition: transform 0.3s ease-in-out;
  transform: translateY(0);
}

/* Header Collapse Animation */
.app-header.header-hidden {
  transform: translateY(-100%);
}

/* Dark Mode Header */
.dark .app-header {
  background-color: rgb(32, 41, 71);
  border-bottom-color: rgba(255, 255, 255, 0.1);
}

/* Hamburger Menu */
.hamburger-menu {
  display: flex;
  flex-direction: column;
  justify-content: space-around;
  width: 28px;
  height: 28px;
  background: transparent;
  border: none;
  cursor: pointer;
  padding: 4px;
  z-index: 10;
  transition: all 0.3s ease;
  color: #374151;
}

/* Dark Mode Hamburger */
.dark .hamburger-menu {
  color: #e5e7eb;
}

.hamburger-menu span {
  width: 100%;
  height: 2px;
  background-color: currentColor;
  border-radius: 2px;
  transition: all 0.3s ease;
  transform-origin: center;
}

.hamburger-menu:hover span {
  background-color: rgb(var(--primary));
}

.hamburger-menu.active span:nth-child(1) {
  transform: translateY(8px) rotate(45deg);
}

.hamburger-menu.active span:nth-child(2) {
  opacity: 0;
  transform: translateX(-10px);
}

.hamburger-menu.active span:nth-child(3) {
  transform: translateY(-8px) rotate(-45deg);
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

/* Header Icons */
.header-link-icon {
  font-size: 1.25rem !important;
  line-height: 1 !important;
  display: inline-flex !important;
  align-items: center !important;
  justify-content: center !important;
  color: #374151;
}

/* Dark Mode Icons */
.dark .header-link-icon {
  color: #e5e7eb;
}

.header-link {
  display: inline-flex !important;
  align-items: center !important;
  justify-content: center !important;
  padding: 0.5rem !important;
  border-radius: 0.375rem;
  transition: all 0.2s ease;
  color: #374151;
}

/* Dark Mode Header Links */
.dark .header-link {
  color: #e5e7eb;
}

.header-link:hover {
  background-color: rgba(var(--primary), 0.1);
  color: rgb(var(--primary));
}

.dark .header-link:hover {
  background-color: rgba(var(--primary), 0.15);
  color: rgb(var(--primary));
}

/* Avatar */
.avatar {
  display: inline-flex !important;
  align-items: center !important;
  justify-content: center !important;
}

/* Profile Dropdown */
.header-profile-dropdown {
  position: absolute;
  right: 0;
  top: 100%;
  z-index: 1000;
  min-width: 200px;
  background-color: white;
  border: 1px solid rgba(0, 0, 0, 0.1);
  border-radius: 0.5rem;
  box-shadow: 0 10px 40px rgba(0, 0, 0, 0.12);
  margin-top: 0.5rem;
}

.dark .header-profile-dropdown {
  background-color: rgb(32, 41, 71);
  border-color: rgba(255, 255, 255, 0.1);
}

/* Search Bar Dark Mode */
.dark .header-search-bar {
  background-color: rgba(0, 0, 0, 0.2);
  border-color: rgba(255, 255, 255, 0.1);
  color: #e5e7eb;
}

.dark .header-search-bar::placeholder {
  color: rgba(229, 231, 235, 0.6);
}

.dark .header-search-icon {
  color: #e5e7eb;
}

/* Notification Pulse - Visible in Dark Mode */
.header-icon-pulse {
  position: absolute;
  top: 8px;
  right: 8px;
  width: 8px;
  height: 8px;
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
