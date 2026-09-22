<script setup>
import { ref } from 'vue'
const emit = defineEmits(['toggle-dark', 'toggle-sidebar'])
const profileOpen = ref(false)
const notificationsOpen = ref(false)
</script>

<template>
  <header class="topbar">
    <button class="icon-button mobile-menu" aria-label="Open navigation" @click="emit('toggle-sidebar')"><i class="ri-menu-2-line"></i></button>
    <div class="topbar-search"><i class="ri-search-line"></i><input aria-label="Search workspace" placeholder="Search projects, tasks, or people…" /></div>
    <div class="topbar-actions">
      <button class="icon-button desktop-only" aria-label="Toggle theme" @click="emit('toggle-dark')"><i class="ri-moon-line"></i></button>
      <div class="menu-anchor">
        <button class="icon-button notification-button" aria-label="Notifications" @click="notificationsOpen = !notificationsOpen"><i class="ri-notification-3-line"></i><span></span></button>
        <div v-if="notificationsOpen" class="popover notifications-popover">
          <p class="popover-title">Notifications <small>2 new</small></p>
          <a href="javascript:void(0)"><i class="ri-checkbox-circle-line"></i><span><b>Design review completed</b><small>Website Redesign · 12m ago</small></span></a>
          <a href="javascript:void(0)"><i class="ri-calendar-event-line"></i><span><b>Deadline approaching</b><small>Data Migration · Tomorrow</small></span></a>
        </div>
      </div>
      <div class="menu-anchor">
        <button class="profile-button" @click="profileOpen = !profileOpen"><span class="profile-avatar">PM</span><span class="profile-copy desktop-only"><b>Project Manager</b><small>Workspace admin</small></span><i class="ri-arrow-down-s-line desktop-only"></i></button>
        <div v-if="profileOpen" class="popover profile-popover"><a href="javascript:void(0)"><i class="ri-user-line"></i>My profile</a><a href="javascript:void(0)"><i class="ri-settings-3-line"></i>Workspace settings</a><a href="javascript:void(0)" class="danger-link"><i class="ri-logout-box-r-line"></i>Sign out</a></div>
      </div>
    </div>
  </header>
</template>
