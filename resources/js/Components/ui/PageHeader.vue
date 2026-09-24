<script setup>
import { computed } from 'vue'
import { usePage, Link } from '@inertiajs/vue3'

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

const page = usePage()
const homeUrl = computed(() => page.props.auth?.teamMember ? '/team-member/dashboard' : '/')

const breadcrumbs = computed(() => {
  const crumbs = [{ label: 'Home', to: homeUrl.value }]
  
  // Build breadcrumbs from route path
  const currentUrl = page.url.split('?')[0]
  const pathParts = currentUrl.split('/').filter(Boolean)
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
  <div class="page-header mb-8">
    <div class="flex items-center justify-between flex-wrap gap-4">
      <div>
        <!-- Breadcrumbs -->
        <nav class="mb-3">
          <ol class="breadcrumb-modern">
            <li 
              v-for="(crumb, index) in breadcrumbs" 
              :key="index" 
              class="breadcrumb-item-modern"
            >
              <Link 
                v-if="crumb.to" 
                :href="crumb.to"
                class="text-gray-600 hover:text-green-600 dark:text-gray-400 dark:hover:text-green-400 transition-colors"
              >
                {{ crumb.label }}
              </Link>
              <span v-else class="text-gray-900 dark:text-gray-100 font-medium">
                {{ crumb.label }}
              </span>
              <i v-if="index < breadcrumbs.length - 1" class="ri-arrow-right-s-line text-gray-400 mx-1"></i>
            </li>
          </ol>
        </nav>
        
        <!-- Title -->
        <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-50 tracking-tight mb-2">
          {{ title }}
        </h1>
        
        <!-- Subtitle -->
        <p v-if="subtitle" class="text-gray-600 dark:text-gray-400 text-base">
          {{ subtitle }}
        </p>
      </div>
      
      <!-- Actions Slot -->
      <div v-if="$slots.actions" class="flex items-center gap-3">
        <slot name="actions"></slot>
      </div>
    </div>
  </div>
</template>

<style scoped>
.breadcrumb-modern {
  display: flex;
  align-items: center;
  flex-wrap: wrap;
  list-style: none;
  padding: 0;
  margin: 0;
  gap: 0.25rem;
}

.breadcrumb-item-modern {
  display: flex;
  align-items: center;
  font-size: 0.875rem;
}
</style>
