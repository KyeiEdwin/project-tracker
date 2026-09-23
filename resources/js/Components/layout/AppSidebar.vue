<script setup>
import { ref, computed } from 'vue'
import { usePage, Link } from '@inertiajs/vue3'

const props = defineProps({
  isOpen: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['close'])

const page = usePage()
const openMenus = ref([])

const menuItems = [
  {
    id: 'dashboard',
    label: 'Dashboard',
    icon: 'ri-home-line',
    to: '/'
  },
  {
    id: 'projects',
    label: 'Projects',
    icon: 'ri-folder-line',
    children: [
      { label: 'Projects List', to: '/projects' },
      { label: 'Create Project', to: '/projects/create' },
      { label: 'Project Details', to: '/projects/1' }
    ]
  },
  {
    id: 'initiation',
    label: 'Initiation',
    icon: 'ri-rocket-line',
    children: [
      { label: 'Kick-Off', to: '/initiation/kickoff' },
      { label: 'Stakeholders', to: '/initiation/stakeholders' }
    ]
  },
  {
    id: 'agile',
    label: 'Agile',
    icon: 'ri-loop-left-line',
    children: [
      { label: 'Sprints', to: '/agile/sprints' },
      { label: 'Backlog', to: '/agile/backlog' },
      { label: 'DoR / DoD', to: '/agile/definitions' }
    ]
  },
  {
    id: 'tasks',
    label: 'Tasks',
    icon: 'ri-checkbox-circle-line',
    children: [
      { label: 'Task List', to: '/tasks' },
      { label: 'Kanban Board', to: '/tasks/kanban' },
      { label: 'Workflows', to: '/tasks/workflows' }
    ]
  },
  {
    id: 'resources',
    label: 'Resources',
    icon: 'ri-team-line',
    children: [
      { label: 'Team', to: '/resources/team' },
      { label: 'Time Tracking', to: '/resources/time-tracking' },
      { label: 'Budget', to: '/resources/budget' },
      { label: 'Milestones', to: '/resources/milestones' },
      { label: 'Gantt Chart', to: '/resources/gantt' }
    ]
  },
  {
    id: 'quality',
    label: 'Quality',
    icon: 'ri-shield-check-line',
    children: [
      { label: 'QA & Testing', to: '/quality/qa-testing' },
      { label: 'Risks & Issues', to: '/quality/risks' },
      { label: 'Change Log', to: '/quality/change-log' }
    ]
  },
  {
    id: 'reports',
    label: 'Reports',
    icon: 'ri-bar-chart-box-line',
    children: [
      { label: 'Analytics', to: '/reports/analytics' },
      { label: 'Documents', to: '/reports/documents' },
      { label: 'Lessons Learned', to: '/reports/lessons-learned' }
    ]
  },
  {
    id: 'charts',
    label: 'Charts',
    icon: 'ri-pie-chart-line',
    to: '/charts'
  },
  {
    id: 'chat',
    label: 'Chat',
    icon: 'ri-chat-3-line',
    to: '/chat'
  }
]

const toggleMenu = (menuId) => {
  const index = openMenus.value.indexOf(menuId)
  if (index > -1) {
    openMenus.value.splice(index, 1)
  } else {
    openMenus.value.push(menuId)
  }
}

const isMenuOpen = (menuId) => {
  return openMenus.value.includes(menuId)
}

const isActive = (path) => {
  return page.url === path
}

const isChildActive = (children) => {
  return children?.some(child => page.url === child.to || page.url.startsWith(child.to + '/'))
}

const handleLinkClick = () => {
  // Close sidebar on mobile when a link is clicked
  emit('close')
}
</script>

<template>
  <aside class="app-sidebar" :class="{ 'is-open': isOpen }">
    <div class="sidebar-content">
      <nav class="sidebar-nav">
        <ul class="sidebar-menu">
          <li 
            v-for="item in menuItems" 
            :key="item.id"
            class="sidebar-item"
            :class="{ 
              'has-submenu': item.children, 
              'submenu-open': isMenuOpen(item.id),
              'active': isActive(item.to) || isChildActive(item.children)
            }"
          >
            <!-- Menu item with children (dropdown) -->
            <template v-if="item.children">
              <button 
                class="sidebar-link" 
                :class="{ 'active': isChildActive(item.children) }"
                @click="toggleMenu(item.id)"
                type="button"
              >
                <i :class="[item.icon, 'sidebar-icon']"></i>
                <span class="sidebar-label">{{ item.label }}</span>
                <i class="ri-arrow-down-s-line sidebar-arrow" :class="{ 'rotated': isMenuOpen(item.id) }"></i>
              </button>
              <ul 
                v-show="isMenuOpen(item.id)" 
                class="sidebar-submenu"
              >
                <li v-for="child in item.children" :key="child.to" class="submenu-item">
                  <Link 
                    :href="child.to" 
                    class="submenu-link"
                    :class="{ 'active': isActive(child.to) }"
                    @click="handleLinkClick"
                  >
                    <span class="submenu-bullet"></span>
                    {{ child.label }}
                  </Link>
                </li>
              </ul>
            </template>
            
            <!-- Simple menu item (no children) -->
            <template v-else>
              <Link 
                :href="item.to" 
                class="sidebar-link"
                :class="{ 'active': isActive(item.to) }"
                @click="handleLinkClick"
              >
                <i :class="[item.icon, 'sidebar-icon']"></i>
                <span class="sidebar-label">{{ item.label }}</span>
              </Link>
            </template>
          </li>
        </ul>
      </nav>
    </div>
  </aside>
</template>

<style scoped>
.app-sidebar {
  position: fixed;
  top: 0;
  left: 0;
  height: 100vh;
  width: 260px;
  background-color: #fff;
  border-right: 1px solid rgba(0, 0, 0, 0.1);
  transform: translateX(-100%);
  transition: transform 0.3s ease;
  z-index: 50;
  overflow-y: auto;
  padding-top: 64px; /* Header height */
}

.app-sidebar.is-open {
  transform: translateX(0);
}

@media (min-width: 992px) {
  .app-sidebar.is-open {
    transform: translateX(0);
  }
}

.dark .app-sidebar {
  background-color: rgb(32, 41, 71);
  border-right-color: rgba(255, 255, 255, 0.1);
}

.sidebar-content {
  padding: 1rem 0;
}

.sidebar-menu {
  list-style: none;
  padding: 0;
  margin: 0;
}

.sidebar-item {
  margin-bottom: 0.25rem;
}

.sidebar-link {
  display: flex;
  align-items: center;
  padding: 0.75rem 1.5rem;
  color: #6b7280;
  text-decoration: none;
  transition: all 0.2s ease;
  cursor: pointer;
  border: none;
  background: transparent;
  width: 100%;
  text-align: left;
  font-size: 0.9375rem;
  position: relative;
}

.sidebar-link:hover {
  background-color: rgba(var(--primary), 0.05);
  color: rgb(var(--primary));
}

.sidebar-link.active {
  background-color: rgba(var(--primary), 0.1);
  color: rgb(var(--primary));
  font-weight: 500;
}

.sidebar-link.active::before {
  content: '';
  position: absolute;
  left: 0;
  top: 0;
  bottom: 0;
  width: 3px;
  background-color: rgb(var(--primary));
}

.sidebar-icon {
  font-size: 1.25rem;
  width: 1.5rem;
  height: 1.5rem;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  margin-right: 0.75rem;
  flex-shrink: 0;
}

.sidebar-label {
  flex: 1;
  white-space: nowrap;
}

.sidebar-arrow {
  font-size: 1rem;
  margin-left: auto;
  transition: transform 0.2s ease;
  flex-shrink: 0;
}

.sidebar-arrow.rotated {
  transform: rotate(180deg);
}

.sidebar-submenu {
  list-style: none;
  padding: 0;
  margin: 0;
  background-color: rgba(0, 0, 0, 0.02);
  max-height: 0;
  overflow: hidden;
  animation: slideDown 0.3s ease forwards;
}

@keyframes slideDown {
  to {
    max-height: 500px;
  }
}

.dark .sidebar-submenu {
  background-color: rgba(0, 0, 0, 0.2);
}

.submenu-item {
  margin: 0;
}

.submenu-link {
  display: flex;
  align-items: center;
  padding: 0.625rem 1.5rem 0.625rem 3.5rem;
  color: #6b7280;
  text-decoration: none;
  font-size: 0.875rem;
  transition: all 0.2s ease;
  position: relative;
}

.submenu-link:hover {
  background-color: rgba(var(--primary), 0.05);
  color: rgb(var(--primary));
}

.submenu-link.active {
  color: rgb(var(--primary));
  font-weight: 500;
}

.submenu-bullet {
  position: absolute;
  left: 2.25rem;
  width: 6px;
  height: 6px;
  border-radius: 50%;
  background-color: currentColor;
  opacity: 0.5;
}

.submenu-link.active .submenu-bullet {
  opacity: 1;
  background-color: rgb(var(--primary));
}

/* Dark mode text colors */
.dark .sidebar-link,
.dark .submenu-link {
  color: #a2a6b9;
}

.dark .sidebar-link:hover,
.dark .submenu-link:hover {
  color: #fff;
}

.dark .sidebar-link.active,
.dark .submenu-link.active {
  color: #fff;
}

/* Custom scrollbar */
.app-sidebar::-webkit-scrollbar {
  width: 6px;
}

.app-sidebar::-webkit-scrollbar-track {
  background: transparent;
}

.app-sidebar::-webkit-scrollbar-thumb {
  background: rgba(0, 0, 0, 0.2);
  border-radius: 3px;
}

.dark .app-sidebar::-webkit-scrollbar-thumb {
  background: rgba(255, 255, 255, 0.2);
}

.app-sidebar::-webkit-scrollbar-thumb:hover {
  background: rgba(0, 0, 0, 0.3);
}

.dark .app-sidebar::-webkit-scrollbar-thumb:hover {
  background: rgba(255, 255, 255, 0.3);
}
</style>
