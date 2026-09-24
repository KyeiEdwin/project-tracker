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
const permissions = computed(() => page.props.auth?.permissions || [])

const can = (permission) => permissions.value.includes(permission)

const adminMenuItems = [
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
]

const menuItems = computed(() => {
  if (page.props.auth?.teamMember) {
    const items = []

    if (can('member.dashboard.view')) {
      items.push({
        id: 'team-member-dashboard',
        label: 'My Dashboard',
        icon: 'ri-home-line',
        to: '/team-member/dashboard',
      })
    }

    if (can('member.project.view')) {
      items.push({
        id: 'team-member-projects',
        label: 'Project List',
        icon: 'ri-folder-line',
        to: '/team-member/dashboard#projects',
      })
    }

    if (can('member.task.view')) {
      items.push({
        id: 'team-member-tasks',
        label: 'Task List',
        icon: 'ri-checkbox-circle-line',
        to: '/team-member/dashboard#tasks',
      })
    }

    if (can('dashboard.team_member.view')) {
      items.push({
        id: 'team-member-chat',
        label: 'Team Chat',
        icon: 'ri-chat-3-line',
        to: '/team-member/chat',
      })
    }

    if (can('profile.view')) {
      items.push({
        id: 'team-member-profile',
        label: 'Profile',
        icon: 'ri-user-line',
        to: '/team-member/profile',
      })
    }

    if (can('setting.view')) {
      items.push({
        id: 'team-member-settings',
        label: 'Settings',
        icon: 'ri-settings-3-line',
        to: '/team-member/settings',
      })
    }

    return items
  }

  return adminMenuItems
})

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
/* Sidebar - Modernized */
.app-sidebar {
  position: fixed;
  top: 0;
  left: 0;
  height: 100vh;
  width: 280px;
  background-color: rgba(255, 255, 255, 0.98);
  backdrop-filter: blur(12px);
  border-right: 1px solid rgba(0, 0, 0, 0.06);
  transform: translateX(-100%);
  transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  z-index: 50;
  overflow-y: auto;
  padding-top: 64px;
  box-shadow: 4px 0 16px rgba(0, 0, 0, 0.04);
}

.app-sidebar.is-open {
  transform: translateX(0);
}

@media (min-width: 992px) {
  .app-sidebar.is-open {
    transform: translateX(0);
  }
}

/* Dark Mode Sidebar - Enhanced */
.dark .app-sidebar {
  background-color: rgba(15, 23, 42, 0.98);
  border-right-color: rgba(255, 255, 255, 0.08);
  box-shadow: 4px 0 24px rgba(0, 0, 0, 0.3);
}

.sidebar-content {
  padding: 1.5rem 0;
}

.sidebar-menu {
  list-style: none;
  padding: 0;
  margin: 0;
}

.sidebar-item {
  margin-bottom: 0.375rem;
  padding: 0 1rem;
}

/* Sidebar Links - Modernized */
.sidebar-link {
  display: flex;
  align-items: center;
  padding: 0.875rem 1rem;
  color: #6b7280;
  text-decoration: none;
  transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
  cursor: pointer;
  border: none;
  background: transparent;
  width: 100%;
  text-align: left;
  font-size: 0.9375rem;
  position: relative;
  border-radius: 0.75rem;
  font-weight: 500;
}

.sidebar-link:hover {
  background-color: rgba(22, 163, 74, 0.08);
  color: rgb(22, 163, 74);
  transform: translateX(2px);
}

.sidebar-link.active {
  background: linear-gradient(135deg, rgba(22, 163, 74, 0.15), rgba(16, 185, 129, 0.1));
  color: rgb(22, 163, 74);
  font-weight: 600;
}

.sidebar-link.active::before {
  content: '';
  position: absolute;
  left: -1rem;
  top: 50%;
  transform: translateY(-50%);
  width: 4px;
  height: 60%;
  background: linear-gradient(180deg, rgb(22, 163, 74), rgb(16, 185, 129));
  border-radius: 0 4px 4px 0;
}

.sidebar-icon {
  font-size: 1.375rem;
  width: 1.75rem;
  height: 1.75rem;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  margin-right: 0.875rem;
  flex-shrink: 0;
  transition: transform 0.2s ease;
}

.sidebar-link:hover .sidebar-icon {
  transform: scale(1.1);
}

.sidebar-label {
  flex: 1;
  white-space: nowrap;
}

.sidebar-arrow {
  font-size: 1.125rem;
  margin-left: auto;
  transition: transform 0.2s ease;
  flex-shrink: 0;
}

.sidebar-arrow.rotated {
  transform: rotate(180deg);
}

/* Submenu - Modernized */
.sidebar-submenu {
  list-style: none;
  padding: 0.5rem 0 0.5rem 0;
  margin: 0.5rem 0 0 0;
  background-color: rgba(0, 0, 0, 0.02);
  border-radius: 0.75rem;
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
  padding: 0 0.5rem;
}

.submenu-link {
  display: flex;
  align-items: center;
  padding: 0.75rem 1rem 0.75rem 3rem;
  color: #6b7280;
  text-decoration: none;
  font-size: 0.875rem;
  transition: all 0.2s ease;
  position: relative;
  border-radius: 0.5rem;
}

.submenu-link:hover {
  background-color: rgba(22, 163, 74, 0.08);
  color: rgb(22, 163, 74);
}

.submenu-link.active {
  color: rgb(22, 163, 74);
  font-weight: 600;
  background-color: rgba(22, 163, 74, 0.1);
}

.submenu-bullet {
  position: absolute;
  left: 1.75rem;
  width: 6px;
  height: 6px;
  border-radius: 50%;
  background-color: currentColor;
  opacity: 0.5;
  transition: all 0.2s ease;
}

.submenu-link.active .submenu-bullet {
  opacity: 1;
  background-color: rgb(22, 163, 74);
  transform: scale(1.3);
}

/* Dark mode text colors - Enhanced */
.dark .sidebar-link,
.dark .submenu-link {
  color: #d1d5db;
}

.dark .sidebar-link:hover,
.dark .submenu-link:hover {
  color: rgb(134, 239, 172);
}

.dark .sidebar-link.active,
.dark .submenu-link.active {
  color: rgb(134, 239, 172);
}

.dark .sidebar-link.active {
  background: linear-gradient(135deg, rgba(134, 239, 172, 0.15), rgba(16, 185, 129, 0.1));
}

/* Custom scrollbar - Modernized */
.app-sidebar::-webkit-scrollbar {
  width: 6px;
}

.app-sidebar::-webkit-scrollbar-track {
  background: transparent;
}

.app-sidebar::-webkit-scrollbar-thumb {
  background: rgba(22, 163, 74, 0.2);
  border-radius: 3px;
}

.dark .app-sidebar::-webkit-scrollbar-thumb {
  background: rgba(134, 239, 172, 0.2);
}

.app-sidebar::-webkit-scrollbar-thumb:hover {
  background: rgba(22, 163, 74, 0.3);
}

.dark .app-sidebar::-webkit-scrollbar-thumb:hover {
  background: rgba(134, 239, 172, 0.3);
}
</style>
