<script setup>
import { ref } from 'vue'
import { usePage, Link } from '@inertiajs/vue3'
defineProps({ open: Boolean })
const emit = defineEmits(['navigate'])
const page = usePage(); const openMenus = ref([])
const menuItems = [
  { id:'dashboard', label:'Overview', icon:'ri-layout-grid-line', to:'/' },
  { id:'projects', label:'Projects', icon:'ri-folder-3-line', children:[{label:'All projects',to:'/projects'},{label:'Create project',to:'/projects/create'}] },
  { id:'initiation', label:'Initiation', icon:'ri-rocket-2-line', children:[{label:'Kick-off',to:'/initiation/kickoff'},{label:'Stakeholders',to:'/initiation/stakeholders'}] },
  { id:'agile', label:'Agile delivery', icon:'ri-flashlight-line', children:[{label:'Sprints',to:'/agile/sprints'},{label:'Backlog',to:'/agile/backlog'},{label:'Definitions',to:'/agile/definitions'}] },
  { id:'tasks', label:'Tasks', icon:'ri-checkbox-circle-line', children:[{label:'Task list',to:'/tasks'},{label:'Kanban board',to:'/tasks/kanban'},{label:'Workflows',to:'/tasks/workflows'}] },
  { id:'resources', label:'Resources', icon:'ri-team-line', children:[{label:'Team',to:'/resources/team'},{label:'Time tracking',to:'/resources/time-tracking'},{label:'Budget',to:'/resources/budget'},{label:'Milestones',to:'/resources/milestones'},{label:'Gantt chart',to:'/resources/gantt'}] },
  { id:'quality', label:'Quality', icon:'ri-shield-check-line', children:[{label:'QA & testing',to:'/quality/qa-testing'},{label:'Risks & issues',to:'/quality/risks'},{label:'Change log',to:'/quality/change-log'}] },
  { id:'reports', label:'Reports', icon:'ri-pie-chart-2-line', children:[{label:'Analytics',to:'/reports/analytics'},{label:'Documents',to:'/reports/documents'},{label:'Lessons learned',to:'/reports/lessons-learned'}] },
  { id:'chat', label:'Team chat', icon:'ri-message-3-line', to:'/chat' }
]
const isActive = path => page.url.split('?')[0] === path
const isChildActive = children => children?.some(child => isActive(child.to))
const menuOpen = item => openMenus.value.includes(item.id) || isChildActive(item.children)
const toggle = item => { openMenus.value = menuOpen(item) ? openMenus.value.filter(id => id !== item.id) : [item.id] }
</script>
<template>
  <aside class="workspace-sidebar" :class="{ 'is-open': open }">
    <Link href="/" class="brand" @click="emit('navigate')"><span class="brand-mark"><i class="ri-focus-3-line"></i></span><span>Northstar<small>PROJECTS</small></span></Link>
    <nav class="sidebar-nav" aria-label="Main navigation"><template v-for="item in menuItems" :key="item.id"><button v-if="item.children" class="nav-item nav-toggle" :class="{active:isChildActive(item.children)}" @click="toggle(item)"><i :class="item.icon"></i><span>{{ item.label }}</span><i class="ri-arrow-down-s-line nav-chevron" :class="{rotated:menuOpen(item)}"></i></button><div v-if="item.children && menuOpen(item)" class="nav-children"><Link v-for="child in item.children" :key="child.to" :href="child.to" :class="{active:isActive(child.to)}" @click="emit('navigate')">{{ child.label }}</Link></div><Link v-else :href="item.to" class="nav-item" :class="{active:isActive(item.to)}" @click="emit('navigate')"><i :class="item.icon"></i><span>{{ item.label }}</span></Link></template></nav>
    <div class="sidebar-help"><span class="help-icon"><i class="ri-lightbulb-flash-line"></i></span><div><b>Need a hand?</b><small>Explore workspace guides</small></div></div>
  </aside>
</template>
