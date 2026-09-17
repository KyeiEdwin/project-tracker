<script setup>
import { ref, computed } from 'vue'
import { useRoute } from 'vue-router'

const route = useRoute()
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
    // Close other menus first, then open this one
    openMenus.value = [menuId]
  }
}

const closeMenus = () => {
  openMenus.value = []
}

const isMenuOpen = (menuId) => {
  return openMenus.value.includes(menuId)
}

const isActive = (path) => {
  return route.path === path
}

const isChildActive = (children) => {
  return children?.some(child => route.path === child.to)
}
</script>

<template>
  <aside class="app-sidebar sticky" id="sidebar">
    <div class="container-xl">
      <div class="main-sidebar" id="sidebar-scroll">
        <nav class="main-menu-container nav nav-pills sub-open">
          <!-- Slide Left Arrow -->
          <div class="slide-left" id="slide-left">
            <svg fill="#7b8191" height="24" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg">
              <path d="M13.293 6.293 7.586 12l5.707 5.707 1.414-1.414L10.414 12l4.293-4.293z"></path>
            </svg>
          </div>
          
          <!-- Menu Items -->
          <ul class="main-menu" style="display: flex; align-items: center; flex-wrap: wrap;">
            <li 
              v-for="item in menuItems" 
              :key="item.id"
              class="slide"
              :class="{ 
                'has-sub': item.children, 
                'open': isMenuOpen(item.id) || isChildActive(item.children),
                'active': isActive(item.to) || isChildActive(item.children)
              }"
              style="position: relative; display: block;"
            >
              <!-- Menu item with children (dropdown) -->
              <template v-if="item.children">
                <a 
                  class="side-menu__item" 
                  :class="{ 'active': isChildActive(item.children) }"
                  href="javascript:void(0);"
                  @click="toggleMenu(item.id)"
                  style="display: flex; align-items: center;"
                >
                  <i :class="[item.icon, 'side-menu__icon']"></i>
                  <span class="side-menu__label">{{ item.label }}</span>
                  <i class="ri-arrow-down-s-line side-menu__angle"></i>
                </a>
                <ul 
                  v-if="isMenuOpen(item.id)"
                  class="pm-dropdown-menu"
                >
                  <li v-for="child in item.children" :key="child.to">
                    <router-link 
                      :to="child.to" 
                      :class="{ 'active': isActive(child.to) }"
                      @click="closeMenus"
                    >
                      {{ child.label }}
                    </router-link>
                  </li>
                </ul>
              </template>
              
              <!-- Simple menu item (no children) -->
              <template v-else>
                <router-link 
                  :to="item.to" 
                  class="side-menu__item"
                  :class="{ 'active': isActive(item.to) }"
                >
                  <i :class="[item.icon, 'side-menu__icon']"></i>
                  <span class="side-menu__label">{{ item.label }}</span>
                </router-link>
              </template>
            </li>
          </ul>
          
          <!-- Slide Right Arrow -->
          <div class="slide-right" id="slide-right">
            <svg fill="#7b8191" height="24" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg">
              <path d="M10.707 17.707 16.414 12l-5.707-5.707-1.414 1.414L13.586 12l-4.293 4.293z"></path>
            </svg>
          </div>
        </nav>
      </div>
    </div>
  </aside>
</template>

<style>
/* Custom dropdown menu - NOT scoped to ensure override */
.pm-dropdown-menu {
  position: absolute !important;
  top: 100% !important;
  left: 0 !important;
  min-width: 200px !important;
  background-color: #fff !important;
  border: 1px solid rgba(0,0,0,0.1) !important;
  border-radius: 0.375rem !important;
  box-shadow: 0 10px 40px rgba(0,0,0,0.12) !important;
  padding: 0.5rem 0 !important;
  z-index: 1000 !important;
  margin: 0 !important;
  list-style: none !important;
  display: block !important;
}

.pm-dropdown-menu li {
  display: block !important;
}

.pm-dropdown-menu a {
  display: block !important;
  padding: 0.5rem 1rem !important;
  color: #374151 !important;
  white-space: nowrap !important;
  text-decoration: none !important;
}

.pm-dropdown-menu a:hover {
  background-color: rgba(92, 97, 242, 0.1) !important;
  color: #5c61f2 !important;
}

.pm-dropdown-menu a.active {
  color: #5c61f2 !important;
  background-color: rgba(92, 97, 242, 0.08) !important;
}
</style>

