<script setup>
import { onMounted, onBeforeUnmount } from 'vue'
import ApexCharts from 'apexcharts'
import PageHeader from '@/components/ui/PageHeader.vue'

let activityChart
onMounted(() => {
  activityChart = new ApexCharts(document.querySelector('#delivery-chart'), {
    chart: { type: 'area', height: 245, toolbar: { show: false }, fontFamily: 'DM Sans, sans-serif' },
    series: [{ name: 'Completed tasks', data: [12, 20, 18, 29, 35, 31, 43, 48] }],
    colors: ['#4f46e5'], stroke: { curve: 'smooth', width: 3 }, fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: .28, opacityTo: .02, stops: [0, 100] } },
    dataLabels: { enabled: false }, grid: { borderColor: '#edf0f5', strokeDashArray: 4, padding: { left: 0, right: 4 } },
    xaxis: { categories: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun', 'Today'], axisBorder: { show: false }, axisTicks: { show: false }, labels: { style: { colors: '#8b94a7', fontSize: '11px' } } },
    yaxis: { labels: { style: { colors: '#8b94a7', fontSize: '11px' } } }, tooltip: { theme: 'light' }
  })
  activityChart.render()
})
onBeforeUnmount(() => activityChart?.destroy())

const projects = [
  { name: 'Website Redesign', team: 'Marketing team', progress: 75, due: 'Dec 15', color: 'violet', avatars: ['JM', 'AR', 'SL'] },
  { name: 'Data Migration', team: 'Platform team', progress: 60, due: 'Dec 10', color: 'blue', avatars: ['TN', 'DK', 'YS'] },
  { name: 'Security Audit', team: 'Security team', progress: 10, due: 'Dec 25', color: 'amber', avatars: ['MA', 'RH'] }
]
const deadlines = [
  { day: '10', month: 'DEC', title: 'Data Migration', task: 'Production cutover checklist', tone: 'danger' },
  { day: '12', month: 'DEC', title: 'Website Redesign', task: 'Final design review', tone: 'violet' },
  { day: '15', month: 'DEC', title: 'CRM Integration', task: 'Integration test sign-off', tone: 'blue' }
]
</script>

<template>
  <div class="dashboard-view">
    <PageHeader title="Good morning, Alex" subtitle="Here’s what needs your attention across the workspace.">
      <template #actions><router-link to="/projects/create" class="ti-btn ti-btn-primary"><i class="ri-add-line me-1"></i> New project</router-link></template>
    </PageHeader>

    <section class="metric-grid">
      <article class="metric-card"><span class="metric-icon violet"><i class="ri-folder-3-line"></i></span><div><small>Active projects</small><b>12</b><p class="trend positive"><i class="ri-arrow-up-line"></i> 8.2% <em>vs. last month</em></p></div></article>
      <article class="metric-card"><span class="metric-icon blue"><i class="ri-checkbox-circle-line"></i></span><div><small>Open tasks</small><b>142</b><p class="trend positive"><i class="ri-arrow-down-line"></i> 4.7% <em>vs. last week</em></p></div></article>
      <article class="metric-card"><span class="metric-icon amber"><i class="ri-alarm-warning-line"></i></span><div><small>Due this week</small><b>18</b><p class="trend warning"><i class="ri-time-line"></i> 5 need attention</p></div></article>
      <article class="metric-card"><span class="metric-icon green"><i class="ri-line-chart-line"></i></span><div><small>On-time delivery</small><b>87<span>%</span></b><p class="trend positive"><i class="ri-arrow-up-line"></i> 2.4% <em>vs. last month</em></p></div></article>
    </section>

    <section class="dashboard-grid primary-grid">
      <article class="box delivery-card"><div class="box-header"><div><h5 class="box-title">Delivery momentum</h5><p>Completed tasks across your portfolio</p></div><button class="period-button">This week <i class="ri-arrow-down-s-line"></i></button></div><div class="box-body"><div id="delivery-chart"></div></div></article>
      <article class="box focus-card"><div class="box-header"><h5 class="box-title">Today’s focus</h5><span class="focus-count">4 tasks</span></div><div class="box-body"><label class="focus-item"><input type="checkbox"><span class="checkmark"></span><span><b>Review data migration checklist</b><small>Data Migration · High priority</small></span><i class="ri-more-2-fill"></i></label><label class="focus-item"><input type="checkbox"><span class="checkmark"></span><span><b>Approve homepage concept</b><small>Website Redesign · Due today</small></span><i class="ri-more-2-fill"></i></label><label class="focus-item"><input type="checkbox"><span class="checkmark"></span><span><b>Plan sprint retrospective</b><small>Mobile App · Tomorrow</small></span><i class="ri-more-2-fill"></i></label><router-link to="/tasks" class="view-link">View all tasks <i class="ri-arrow-right-line"></i></router-link></div></article>
    </section>

    <section class="dashboard-grid secondary-grid">
      <article class="box projects-card"><div class="box-header"><div><h5 class="box-title">Project health</h5><p>Progress across active initiatives</p></div><router-link to="/projects" class="view-link">View all <i class="ri-arrow-right-line"></i></router-link></div><div class="box-body project-list"><div v-for="project in projects" :key="project.name" class="project-row"><span class="project-symbol" :class="project.color"><i class="ri-folder-3-line"></i></span><div class="project-info"><b>{{ project.name }}</b><small>{{ project.team }}</small><div class="project-progress"><span><i :style="{ width: project.progress + '%' }"></i></span><em>{{ project.progress }}%</em></div></div><div class="project-meta"><div class="member-stack"><span v-for="avatar in project.avatars" :key="avatar">{{ avatar }}</span></div><small>Due {{ project.due }}</small></div></div></div></article>
      <article class="box deadlines-card"><div class="box-header"><div><h5 class="box-title">Upcoming deadlines</h5><p>Next milestones across projects</p></div><router-link to="/resources/milestones" class="view-link">Calendar <i class="ri-arrow-right-line"></i></router-link></div><div class="box-body deadline-list"><div v-for="item in deadlines" :key="item.title" class="deadline-row"><div class="deadline-date" :class="item.tone"><b>{{ item.day }}</b><small>{{ item.month }}</small></div><div><b>{{ item.title }}</b><small>{{ item.task }}</small></div><i class="ri-arrow-right-s-line"></i></div></div></article>
    </section>
  </div>
</template>
