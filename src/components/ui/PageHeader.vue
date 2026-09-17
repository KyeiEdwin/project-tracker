<script setup>
import { computed } from 'vue'
import { useRoute } from 'vue-router'

const props = defineProps({
  title: {
    type: String,
    required: true
  },
  subtitle: {
    type: String,
    default: ''
  }
})

const route = useRoute()

const breadcrumbs = computed(() => {
  const crumbs = [{ label: 'Home', to: '/' }]
  
  // Build breadcrumbs from route path
  const pathParts = route.path.split('/').filter(Boolean)
  let currentPath = ''
  
  pathParts.forEach((part, index) => {
    currentPath += `/${part}`
    const isLast = index === pathParts.length - 1
    crumbs.push({
      label: part.charAt(0).toUpperCase() + part.slice(1).replace(/-/g, ' '),
      to: isLast ? null : currentPath
    })
  })
  
  return crumbs
})
</script>

<template>
  <div class="flex items-center justify-between page-header-breadcrumb flex-wrap gap-2">
    <div>
      <nav>
        <ol class="breadcrumb mb-1">
          <li 
            v-for="(crumb, index) in breadcrumbs" 
            :key="index" 
            class="breadcrumb-item"
            :class="{ 'active': !crumb.to }"
            :aria-current="!crumb.to ? 'page' : undefined"
          >
            <router-link v-if="crumb.to" :to="crumb.to">{{ crumb.label }}</router-link>
            <span v-else>{{ crumb.label }}</span>
          </li>
        </ol>
      </nav>
      <h1 class="page-title font-medium text-lg mb-0">{{ title }}</h1>
      <p v-if="subtitle" class="text-textmuted dark:text-textmuted/50 text-sm mt-1">{{ subtitle }}</p>
    </div>
    <div class="btn-list">
      <slot name="actions"></slot>
    </div>
  </div>
</template>

