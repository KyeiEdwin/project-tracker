<script setup>
import { ref, onMounted, nextTick } from 'vue'
import { Link } from '@inertiajs/vue3'
import ApexCharts from 'apexcharts'
import MetricCard from '@/Components/ui/MetricCard.vue'
import StatsCard from '@/Components/ui/StatsCard.vue'
import Card from '@/Components/ui/Card.vue'
import Badge from '@/Components/ui/Badge.vue'
import ProgressBar from '@/Components/ui/ProgressBar.vue'
import Avatar from '@/Components/ui/Avatar.vue'
import Button from '@/Components/ui/Button.vue'

const charts = ref([])

onMounted(() => {
  nextTick(() => {
    initializeCharts()
  })
})

const initializeCharts = () => {
  const greenColor = 'rgb(22, 163, 74)'
  const emeraldColor = 'rgb(16, 185, 129)'
  
  // Project Statistics Chart
  const projectStatsElement = document.querySelector('#project-statistics')
  if (projectStatsElement) {
    const options = {
      series: [
        {
          name: 'Active Projects',
          type: 'area',
          data: [15, 28, 23, 23, 41, 58, 48, 50, 22, 31, 40, 45]
        },
        {
          name: 'Completed',
          type: 'bar',
          data: [20, 29, 37, 35, 44, 43, 50, 20, 20, 45, 45, 52]
        }
      ],
      chart: {
        type: 'area',
        height: 380,
        toolbar: { show: false },
        fontFamily: 'Poppins, sans-serif'
      },
      colors: [greenColor, emeraldColor],
      dataLabels: { enabled: false },
      grid: {
        borderColor: 'rgba(0,0,0,0.06)',
        strokeDashArray: 3,
        padding: { top: 0, right: 10, bottom: 0, left: 10 }
      },
      fill: {
        type: ['gradient', 'solid'],
        gradient: {
          shade: 'light',
          opacityFrom: 0.4,
          opacityTo: 0.1
        }
      },
      stroke: {
        curve: ['smooth', 'smooth'],
        width: [3, 2]
      },
      xaxis: {
        categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        labels: { style: { colors: '#6b7280', fontSize: '12px' } }
      },
      yaxis: {
        labels: { style: { colors: '#6b7280', fontSize: '12px' } }
      },
      legend: {
        show: true,
        position: 'top',
        horizontalAlign: 'right',
        fontSize: '13px',
        fontWeight: 500,
        markers: { radius: 12 }
      },
      plotOptions: {
        bar: {
          columnWidth: '35%',
          borderRadius: 6,
          borderRadiusApplication: 'end'
        }
      }
    }
    
    const chart = new ApexCharts(projectStatsElement, options)
    chart.render()
    charts.value.push(chart)
  }
  
  // Team Performance Chart
  const teamPerfElement = document.querySelector('#team-performance')
  if (teamPerfElement) {
    const options = {
      series: [
        {
          name: 'Tasks Completed',
          data: [44, 55, 57, 56, 61, 58, 63]
        }
      ],
      chart: {
        type: 'bar',
        height: 280,
        toolbar: { show: false }
      },
      plotOptions: {
        bar: {
          borderRadius: 10,
          dataLabels: { position: 'top' },
          columnWidth: '45%'
        }
      },
      dataLabels: {
        enabled: true,
        offsetY: -20,
        style: {
          fontSize: '12px',
          colors: ['#6b7280']
        }
      },
      colors: [greenColor],
      xaxis: {
        categories: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
        labels: { style: { colors: '#6b7280' } }
      },
      yaxis: {
        labels: { style: { colors: '#6b7280' } }
      },
      grid: {
        borderColor: 'rgba(0,0,0,0.06)',
        strokeDashArray: 3
      }
    }
    
    const chart = new ApexCharts(teamPerfElement, options)
    chart.render()
    charts.value.push(chart)
  }
}
</script>

<template>
  <div>
    <!-- Page Header -->
    <div class="mb-8">
      <div class="flex items-center justify-between flex-wrap gap-4">
        <div>
          <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-50 tracking-tight mb-2">
            Dashboard
          </h1>
          <p class="text-gray-600 dark:text-gray-400">
            Welcome back! Here's what's happening with your projects today.
          </p>
        </div>
        <div class="flex items-center gap-3">
          <Button variant="secondary" size="md">
            <i class="ri-download-line mr-2"></i>
            Export
          </Button>
          <Button variant="primary" size="md">
            <i class="ri-add-line mr-2"></i>
            New Project
          </Button>
        </div>
      </div>
    </div>

    <!-- Metric Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8 pt-6 pb-4">
      <MetricCard
        title="Total Projects"
        value="248"
        icon="ri-folder-line"
        color="green"
        trend="up"
        trend-value="+12.5%"
        description="Active and completed"
      />
      
      <MetricCard
        title="Active Tasks"
        value="1,429"
        icon="ri-checkbox-circle-line"
        color="blue"
        trend="up"
        trend-value="+8.2%"
        description="In progress"
      />
      
      <MetricCard
        title="Team Members"
        value="64"
        icon="ri-team-line"
        color="purple"
        trend="up"
        trend-value="+5"
        description="Across all teams"
      />
      
      <MetricCard
        title="Completion Rate"
        value="87%"
        icon="ri-trophy-line"
        color="orange"
        trend="up"
        trend-value="+3.1%"
        description="This month"
      />
    </div>

    <!-- Stats Cards Row -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
      <StatsCard
        title="Pending Reviews"
        value="23"
        icon="ri-eye-line"
        icon-bg="bg-emerald-600"
        badge="+5 today"
        badge-class="badge-success"
        trend="up"
      />
      
      <StatsCard
        title="Overdue Tasks"
        value="8"
        icon="ri-alarm-warning-line"
        icon-bg="bg-red-600"
        badge="3 critical"
        badge-class="badge-danger"
        trend="down"
      />
      
      <StatsCard
        title="Milestones"
        value="12"
        icon="ri-flag-line"
        icon-bg="bg-blue-600"
        badge="2 upcoming"
        badge-class="badge-info"
      />
      
      <StatsCard
        title="Budget Used"
        value="68%"
        icon="ri-funds-line"
        icon-bg="bg-purple-600"
        badge="On track"
        badge-class="badge-success"
        subtitle="$340K of $500K"
      />
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
      <!-- Project Statistics -->
      <Card title="Project Statistics" subtitle="Monthly overview" class="lg:col-span-2">
        <template #actions>
          <select class="px-3 py-1.5 text-sm border border-gray-200 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300">
            <option>Last 12 months</option>
            <option>Last 6 months</option>
            <option>Last 3 months</option>
          </select>
        </template>
        <div id="project-statistics"></div>
      </Card>
      
      <!-- Team Performance -->
      <Card title="Team Performance" subtitle="This week">
        <div id="team-performance"></div>
        
        <div class="mt-6 space-y-4">
          <div>
            <div class="flex justify-between items-center mb-2">
              <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Productivity</span>
              <span class="text-sm font-semibold text-green-600">92%</span>
            </div>
            <ProgressBar :value="92" size="md" variant="primary" />
          </div>
          
          <div>
            <div class="flex justify-between items-center mb-2">
              <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Efficiency</span>
              <span class="text-sm font-semibold text-green-600">85%</span>
            </div>
            <ProgressBar :value="85" size="md" variant="success" />
          </div>
        </div>
      </Card>
    </div>

    <!-- Recent Activity & Team -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <!-- Recent Projects -->
      <Card title="Recent Projects" subtitle="Latest activity">
        <template #actions>
          <Link href="/projects" class="text-sm font-medium text-green-600 hover:text-green-700 dark:text-green-400">
            View All →
          </Link>
        </template>
        
        <div class="space-y-4">
          <div 
            v-for="project in [
              { name: 'ERP System Upgrade', status: 'In Progress', progress: 75, team: 8, color: 'green' },
              { name: 'Mobile App Development', status: 'Planning', progress: 25, team: 5, color: 'blue' },
              { name: 'Cloud Migration', status: 'In Progress', progress: 60, team: 12, color: 'purple' },
              { name: 'Security Audit', status: 'Review', progress: 90, team: 3, color: 'orange' }
            ]"
            :key="project.name"
            class="p-4 rounded-xl border border-gray-200 dark:border-gray-700 hover:border-green-300 dark:hover:border-green-700 transition-colors"
          >
            <div class="flex items-center justify-between mb-3">
              <div class="flex-1">
                <h4 class="font-semibold text-gray-900 dark:text-gray-100 mb-1">{{ project.name }}</h4>
                <div class="flex items-center gap-3">
                  <Badge :variant="project.color === 'green' ? 'success' : 'info'" size="sm">
                    {{ project.status }}
                  </Badge>
                  <span class="text-xs text-gray-500 dark:text-gray-400 flex items-center gap-1">
                    <i class="ri-team-line"></i>
                    {{ project.team }} members
                  </span>
                </div>
              </div>
            </div>
            <ProgressBar :value="project.progress" show-label>
              <template #label>
                <span class="text-sm text-gray-600 dark:text-gray-400">Progress</span>
              </template>
            </ProgressBar>
          </div>
        </div>
      </Card>

      <!-- Top Team Members -->
      <Card title="Top Performers" subtitle="This month">
        <template #actions>
          <Link href="/resources/team" class="text-sm font-medium text-green-600 hover:text-green-700 dark:text-green-400">
            View All →
          </Link>
        </template>
        
        <div class="space-y-4">
          <div 
            v-for="(member, index) in [
              { name: 'Sarah Johnson', role: 'Senior Developer', tasks: 47, avatar: '/images/2.jpg' },
              { name: 'Michael Chen', role: 'Project Lead', tasks: 42, avatar: '/images/3.jpg' },
              { name: 'Emma Davis', role: 'UI/UX Designer', tasks: 38, avatar: '/images/4.jpg' },
              { name: 'James Wilson', role: 'Backend Dev', tasks: 35, avatar: '/images/5.jpg' },
              { name: 'Olivia Brown', role: 'QA Engineer', tasks: 32, avatar: '/images/6.jpg' }
            ]"
            :key="member.name"
            class="flex items-center gap-4 p-3 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors"
          >
            <div class="flex items-center gap-1">
              <span class="text-lg font-bold text-gray-400 dark:text-gray-600 w-6">{{ index + 1 }}</span>
            </div>
            
            <Avatar 
              :src="member.avatar" 
              :name="member.name" 
              size="md" 
              status="online"
            />
            
            <div class="flex-1 min-w-0">
              <h4 class="font-semibold text-gray-900 dark:text-gray-100 truncate">{{ member.name }}</h4>
              <p class="text-sm text-gray-500 dark:text-gray-400 truncate">{{ member.role }}</p>
            </div>
            
            <div class="text-right">
              <div class="text-lg font-bold text-green-600">{{ member.tasks }}</div>
              <div class="text-xs text-gray-500 dark:text-gray-400">tasks</div>
            </div>
          </div>
        </div>
      </Card>
    </div>
  </div>
</template>

<style scoped>
/* Additional dashboard-specific styles */
.box {
  animation: fadeInUp 0.5s ease-out;
}

@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* Stagger animation for cards */
.grid > *:nth-child(1) { animation-delay: 0s; }
.grid > *:nth-child(2) { animation-delay: 0.1s; }
.grid > *:nth-child(3) { animation-delay: 0.2s; }
.grid > *:nth-child(4) { animation-delay: 0.3s; }
</style>
