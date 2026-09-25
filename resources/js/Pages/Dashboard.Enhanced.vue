<script setup>
import { ref, computed, onMounted, nextTick } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import ApexCharts from 'apexcharts'
import MetricCard from '@/Components/ui/MetricCard.vue'
import StatsCard from '@/Components/ui/StatsCard.vue'
import Card from '@/Components/ui/Card.vue'
import Badge from '@/Components/ui/Badge.vue'
import ProgressBar from '@/Components/ui/ProgressBar.vue'
import Avatar from '@/Components/ui/Avatar.vue'
import Button from '@/Components/ui/Button.vue'
import { useProgressColor } from '@/composables/useProgressColor'

const props = defineProps({
  metrics: {
    type: Object,
    required: true,
    default: () => ({})
  }
})

const { getProgressColorClass, getProgressTextClass } = useProgressColor()

const charts = ref([])
const isRefreshing = ref(false)

// Computed values for metrics with defaults
const totalProjects = computed(() => props.metrics.totalProjects?.value ?? 0)
const activeTasks = computed(() => props.metrics.activeTasks?.value ?? 0)
const teamMembers = computed(() => props.metrics.teamMembers?.value ?? 0)
const completionRate = computed(() => props.metrics.completionRate?.value ?? 0)
const pendingReviews = computed(() => props.metrics.pendingReviews?.value ?? 0)
const overdueTasks = computed(() => props.metrics.overdueTasks?.value ?? 0)
const milestones = computed(() => props.metrics.milestones?.value ?? 0)
const budgetUsed = computed(() => props.metrics.budgetUsed?.value ?? 0)

// Computed values for new sections
const recentProjects = computed(() => props.metrics.recentProjects ?? [])
const topPerformers = computed(() => props.metrics.topPerformers ?? [])

// Format trend display
const formatTrend = (metric) => {
  if (!metric?.trend) return { direction: 'stable', value: '0%' }
  
  const percentage = metric.trend.percentage ?? 0
  const direction = metric.trend.direction ?? 'stable'
  
  return {
    direction: direction,
    value: metric.trend.formatted ?? `${percentage}%`
  }
}

// Manual refresh function
const refreshMetrics = async () => {
  isRefreshing.value = true
  
  try {
    const response = await fetch('/dashboard/refresh', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
      }
    })
    
    const data = await response.json()
    
    if (data.success) {
      // Reload the page with fresh data
      router.reload({ only: ['metrics'] })
    }
  } catch (error) {
    console.error('Failed to refresh metrics:', error)
  } finally {
    setTimeout(() => {
      isRefreshing.value = false
    }, 500)
  }
}

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
          <Button 
            variant="secondary" 
            size="md"
            @click="refreshMetrics"
            :disabled="isRefreshing"
          >
            <i :class="['ri-refresh-line mr-2', { 'animate-spin': isRefreshing }]"></i>
            {{ isRefreshing ? 'Refreshing...' : 'Refresh' }}
          </Button>
          <Button variant="secondary" size="md">
            <i class="ri-download-line mr-2"></i>
            Export
          </Button>
        </div>
      </div>
    </div>

    <!-- Metric Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8 pt-6 pb-4">
      <MetricCard
        title="Total Projects"
        :value="totalProjects.toString()"
        icon="ri-folder-line"
        color="green"
        :trend="formatTrend(metrics.totalProjects).direction"
        :trend-value="formatTrend(metrics.totalProjects).value"
        description="Active and completed"
      />
      
      <MetricCard
        title="Active Tasks"
        :value="activeTasks.toLocaleString()"
        icon="ri-checkbox-circle-line"
        color="blue"
        :trend="formatTrend(metrics.activeTasks).direction"
        :trend-value="formatTrend(metrics.activeTasks).value"
        description="In progress"
      />
      
      <MetricCard
        title="Team Members"
        :value="teamMembers.toString()"
        icon="ri-team-line"
        color="purple"
        :trend="formatTrend(metrics.teamMembers).direction"
        :trend-value="formatTrend(metrics.teamMembers).value"
        description="Across all teams"
      />
      
      <MetricCard
        title="Completion Rate"
        :value="`${completionRate}%`"
        icon="ri-trophy-line"
        color="orange"
        :trend="formatTrend(metrics.completionRate).direction"
        :trend-value="formatTrend(metrics.completionRate).value"
        description="Project completion"
      />
    </div>

    <!-- Stats Cards Row -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
      <StatsCard
        title="Pending Reviews"
        :value="pendingReviews.toString()"
        icon="ri-eye-line"
        icon-bg="bg-emerald-600"
        :badge="`${metrics.pendingReviews?.metadata?.today ?? 0} today`"
        badge-class="badge-success"
        :trend="formatTrend(metrics.pendingReviews).direction"
      />
      
      <StatsCard
        title="Overdue Tasks"
        :value="overdueTasks.toString()"
        icon="ri-alarm-warning-line"
        icon-bg="bg-red-600"
        :badge="`${metrics.overdueTasks?.metadata?.critical ?? 0} critical`"
        badge-class="badge-danger"
        :trend="formatTrend(metrics.overdueTasks).direction"
      />
      
      <StatsCard
        title="Milestones"
        :value="milestones.toString()"
        icon="ri-flag-line"
        icon-bg="bg-blue-600"
        :badge="`${metrics.milestones?.metadata?.upcoming ?? 0} upcoming`"
        badge-class="badge-info"
      />
      
      <StatsCard
        title="Budget Used"
        :value="`${budgetUsed}%`"
        icon="ri-funds-line"
        icon-bg="bg-purple-600"
        :badge="budgetUsed > 80 ? 'High usage' : 'On track'"
        :badge-class="budgetUsed > 80 ? 'badge-warning' : 'badge-success'"
        :subtitle="metrics.budgetUsed?.metadata?.currencyFormatted ? 
          `${metrics.budgetUsed.metadata.currencyFormatted.spent} of ${metrics.budgetUsed.metadata.currencyFormatted.budget}` : 
          ''"
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
        
        <div v-if="recentProjects.length > 0" class="space-y-4">
          <div 
            v-for="project in recentProjects"
            :key="project.id"
            class="p-4 rounded-xl border border-gray-200 dark:border-gray-700 hover:border-green-300 dark:hover:border-green-700 transition-colors"
          >
            <div class="flex items-center justify-between mb-3">
              <div class="flex-1">
                <Link :href="`/projects/${project.id}`" class="font-semibold text-gray-900 dark:text-gray-100 mb-1 hover:text-green-600">
                  {{ project.name }}
                </Link>
                <div class="flex items-center gap-3 mt-1">
                  <Badge :variant="project.color === 'green' ? 'success' : project.color === 'blue' ? 'info' : project.color === 'orange' ? 'warning' : 'info'" size="sm">
                    {{ project.status }}
                  </Badge>
                  <span class="text-xs text-gray-500 dark:text-gray-400 flex items-center gap-1">
                    <i class="ri-team-line"></i>
                    {{ project.team }} members
                  </span>
                  <span class="text-xs text-gray-500 dark:text-gray-400">
                    {{ project.updatedAt }}
                  </span>
                </div>
              </div>
            </div>
            <div class="relative">
              <ProgressBar 
                :value="project.progress" 
                show-label
                auto-color
              >
                <template #label>
                  <span class="text-sm text-gray-600 dark:text-gray-400">Progress</span>
                </template>
              </ProgressBar>
            </div>
          </div>
        </div>
        
        <div v-else class="text-center py-8 text-gray-500 dark:text-gray-400">
          <i class="ri-folder-open-line text-4xl mb-2"></i>
          <p>No recent projects</p>
        </div>
      </Card>

      <!-- Top Team Members -->
      <Card title="Top Performers" subtitle="This month">
        <template #actions>
          <Link href="/resources/team" class="text-sm font-medium text-green-600 hover:text-green-700 dark:text-green-400">
            View All →
          </Link>
        </template>
        
        <div v-if="topPerformers.length > 0" class="space-y-4">
          <div 
            v-for="member in topPerformers"
            :key="member.id"
            class="flex items-center gap-4 p-3 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors"
          >
            <div class="flex items-center gap-1">
              <span class="text-lg font-bold text-gray-400 dark:text-gray-600 w-6">{{ member.rank }}</span>
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
        
        <div v-else class="text-center py-8 text-gray-500 dark:text-gray-400">
          <i class="ri-team-line text-4xl mb-2"></i>
          <p>No performance data available</p>
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
