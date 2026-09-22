<script setup>
import { computed, nextTick, ref } from 'vue'
import { router } from '@inertiajs/vue3'

const emit = defineEmits(['toggle-dark', 'toggle-sidebar'])
const profileOpen = ref(false)
const notificationsOpen = ref(false)
const query = ref('')
const searchInput = ref(null)
const searchItems = [
  { label: 'Projects', detail: 'Browse active projects', href: '/projects' },
  { label: 'Task List', detail: 'Find and manage tasks', href: '/tasks' },
  { label: 'Team Resources', detail: 'People and allocations', href: '/resources/team' },
  { label: 'QA & Testing', detail: 'Test cases and results', href: '/quality/qa-testing' },
]
const results = computed(() => {
  const term = query.value.trim().toLowerCase()
  return term ? searchItems.filter(item => `${item.label} ${item.detail}`.toLowerCase().includes(term)) : []
})
const chooseResult = (item) => { query.value = ''; router.visit(item.href) }
const focusSearch = async () => { await nextTick(); searchInput.value?.focus() }
</script>
<template>
  <header class="topbar">
    <button class="icon-button mobile-menu" aria-label="Open navigation" @click="emit('toggle-sidebar')"><i class="ri-menu-2-line"></i></button>
    <div class="topbar-search" @click="focusSearch">
      <i class="ri-search-line"></i><input ref="searchInput" v-model="query" aria-label="Search workspace" placeholder="Search projects, tasks, or people..." @keydown.escape="query = ''" />
      <div v-if="query" class="search-results" role="listbox">
        <button v-for="item in results" :key="item.href" type="button" @click.stop="chooseResult(item)"><b>{{ item.label }}</b><small>{{ item.detail }}</small></button>
        <p v-if="!results.length">No matching workspace sections.</p>
      </div>
    </div>
    <div class="topbar-actions">
      <button class="icon-button desktop-only" aria-label="Toggle theme" @click="emit('toggle-dark')"><i class="ri-moon-line"></i></button>
      <div class="menu-anchor"><button class="icon-button notification-button" aria-label="Notifications" @click="notificationsOpen = !notificationsOpen"><i class="ri-notification-3-line"></i><span></span></button><div v-if="notificationsOpen" class="popover notifications-popover"><p class="popover-title">Notifications <small>2 new</small></p><a href="#"><i class="ri-checkbox-circle-line"></i><span><b>Design review completed</b><small>Website Redesign · 12m ago</small></span></a><a href="#"><i class="ri-calendar-event-line"></i><span><b>Deadline approaching</b><small>Data Migration · Tomorrow</small></span></a></div></div>
      <div class="menu-anchor"><button class="profile-button" @click="profileOpen = !profileOpen"><span class="profile-avatar">PM</span><span class="profile-copy desktop-only"><b>Project Manager</b><small>Workspace admin</small></span><i class="ri-arrow-down-s-line desktop-only"></i></button><div v-if="profileOpen" class="popover profile-popover"><a href="#"><i class="ri-user-line"></i>My profile</a><a href="#"><i class="ri-settings-3-line"></i>Workspace settings</a><a href="#" class="danger-link"><i class="ri-logout-box-r-line"></i>Sign out</a></div></div>
    </div>
  </header>
</template>
