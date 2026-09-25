<script setup>
import { ref, onMounted, watch, computed } from 'vue'
import axios from 'axios'

const props = defineProps({
  sprintId: { type: Number, required: true },
  autoRefresh: { type: Boolean, default: false }
})

const chartData = ref(null)
const loading = ref(true)
const error = ref(null)
const chartCanvas = ref(null)
let chartInstance = null

const fetchBurndownData = async () => {
  try {
    loading.value = true
    error.value = null
    const response = await axios.get(`/sprints/${props.sprintId}/burndown`)
    chartData.value = response.data.data
    renderChart()
  } catch (err) {
    error.value = 'Failed to load burndown data'
    console.error('Burndown fetch error:', err)
  } finally {
    loading.value = false
  }
}

const renderChart = () => {
  if (!chartData.value || !chartCanvas.value) return

  const ctx = chartCanvas.value.getContext('2d')
  const { dates, ideal, actual, planned_points } = chartData.value

  // Clear existing chart
  if (chartInstance) {
    chartInstance.destroy()
  }

  // Create gradient for actual line
  const actualGradient = ctx.createLinearGradient(0, 0, 0, 400)
  actualGradient.addColorStop(0, 'rgba(59, 130, 246, 0.5)')
  actualGradient.addColorStop(1, 'rgba(59, 130, 246, 0.0)')

  // Simple canvas-based chart (no external library needed)
  const canvas = chartCanvas.value
  const width = canvas.width = canvas.offsetWidth * 2 // For retina displays
  const height = canvas.height = canvas.offsetHeight * 2
  ctx.scale(2, 2)

  const padding = { top: 20, right: 20, bottom: 50, left: 60 }
  const chartWidth = canvas.offsetWidth - padding.left - padding.right
  const chartHeight = canvas.offsetHeight - padding.top - padding.bottom

  // Clear canvas
  ctx.clearRect(0, 0, width, height)

  // Draw background
  ctx.fillStyle = '#f9fafb'
  ctx.fillRect(0, 0, width / 2, height / 2)

  // Calculate scales
  const maxValue = Math.max(...ideal, ...actual, planned_points)
  const xScale = chartWidth / (dates.length - 1)
  const yScale = chartHeight / maxValue

  // Draw grid lines
  ctx.strokeStyle = '#e5e7eb'
  ctx.lineWidth = 1
  for (let i = 0; i <= 5; i++) {
    const y = padding.top + (chartHeight / 5) * i
    ctx.beginPath()
    ctx.moveTo(padding.left, y)
    ctx.lineTo(padding.left + chartWidth, y)
    ctx.stroke()

    // Y-axis labels
    const value = Math.round(maxValue - (maxValue / 5) * i)
    ctx.fillStyle = '#6b7280'
    ctx.font = '11px system-ui'
    ctx.textAlign = 'right'
    ctx.fillText(value.toString(), padding.left - 10, y + 4)
  }

  // Draw ideal line (dashed)
  ctx.strokeStyle = '#9ca3af'
  ctx.lineWidth = 2
  ctx.setLineDash([5, 5])
  ctx.beginPath()
  ideal.forEach((value, i) => {
    const x = padding.left + i * xScale
    const y = padding.top + chartHeight - value * yScale
    if (i === 0) {
      ctx.moveTo(x, y)
    } else {
      ctx.lineTo(x, y)
    }
  })
  ctx.stroke()
  ctx.setLineDash([])

  // Draw actual line (solid with fill)
  ctx.fillStyle = actualGradient
  ctx.strokeStyle = '#3b82f6'
  ctx.lineWidth = 3

  ctx.beginPath()
  ctx.moveTo(padding.left, padding.top + chartHeight)
  actual.forEach((value, i) => {
    const x = padding.left + i * xScale
    const y = padding.top + chartHeight - value * yScale
    if (i === 0) {
      ctx.lineTo(x, y)
    } else {
      ctx.lineTo(x, y)
    }
  })
  ctx.lineTo(padding.left + chartWidth, padding.top + chartHeight)
  ctx.closePath()
  ctx.fill()

  // Draw actual line border
  ctx.beginPath()
  actual.forEach((value, i) => {
    const x = padding.left + i * xScale
    const y = padding.top + chartHeight - value * yScale
    if (i === 0) {
      ctx.moveTo(x, y)
    } else {
      ctx.lineTo(x, y)
    }
  })
  ctx.stroke()

  // Draw data points on actual line
  ctx.fillStyle = '#3b82f6'
  actual.forEach((value, i) => {
    const x = padding.left + i * xScale
    const y = padding.top + chartHeight - value * yScale
    ctx.beginPath()
    ctx.arc(x, y, 4, 0, Math.PI * 2)
    ctx.fill()
  })

  // Draw X-axis labels (show every few dates)
  const labelInterval = Math.ceil(dates.length / 7)
  ctx.fillStyle = '#6b7280'
  ctx.font = '10px system-ui'
  ctx.textAlign = 'center'
  dates.forEach((date, i) => {
    if (i % labelInterval === 0 || i === dates.length - 1) {
      const x = padding.left + i * xScale
      const y = padding.top + chartHeight + 20
      const shortDate = new Date(date).toLocaleDateString('en', { month: 'short', day: 'numeric' })
      ctx.save()
      ctx.translate(x, y)
      ctx.rotate(-Math.PI / 4)
      ctx.fillText(shortDate, 0, 0)
      ctx.restore()
    }
  })

  // Draw legend
  const legendY = padding.top + chartHeight + 40
  
  // Ideal line legend
  ctx.strokeStyle = '#9ca3af'
  ctx.lineWidth = 2
  ctx.setLineDash([5, 5])
  ctx.beginPath()
  ctx.moveTo(padding.left, legendY)
  ctx.lineTo(padding.left + 30, legendY)
  ctx.stroke()
  ctx.setLineDash([])
  ctx.fillStyle = '#6b7280'
  ctx.textAlign = 'left'
  ctx.fillText('Ideal', padding.left + 40, legendY + 4)

  // Actual line legend
  ctx.strokeStyle = '#3b82f6'
  ctx.lineWidth = 3
  ctx.beginPath()
  ctx.moveTo(padding.left + 120, legendY)
  ctx.lineTo(padding.left + 150, legendY)
  ctx.stroke()
  ctx.fillStyle = '#6b7280'
  ctx.fillText('Actual', padding.left + 160, legendY + 4)

  // Title
  ctx.fillStyle = '#111827'
  ctx.font = 'bold 14px system-ui'
  ctx.textAlign = 'center'
  ctx.fillText('Sprint Burndown Chart', canvas.offsetWidth / 2, 15)
}

onMounted(() => {
  fetchBurndownData()

  // Auto-refresh if enabled
  if (props.autoRefresh) {
    const interval = setInterval(fetchBurndownData, 300000) // 5 minutes
    onUnmounted(() => clearInterval(interval))
  }
})

watch(() => props.sprintId, () => {
  fetchBurndownData()
})

const statusMessage = computed(() => {
  if (!chartData.value) return ''
  const { current_remaining, planned_points } = chartData.value
  const ahead = current_remaining < (planned_points / 2)
  return ahead ? 'Sprint is ahead of schedule! 🎉' : 'Keep up the great work! 💪'
})
</script>

<template>
  <div class="burndown-chart bg-white rounded-lg shadow-lg p-6 border border-gray-200">
    <!-- Header -->
    <div class="flex items-center justify-between mb-4">
      <div>
        <h3 class="text-xl font-bold text-gray-900">Burndown Chart</h3>
        <p class="text-sm text-gray-600 mt-1">Track remaining work over time</p>
      </div>
      <button
        @click="fetchBurndownData"
        :disabled="loading"
        class="px-4 py-2 text-sm border border-gray-300 rounded-lg hover:bg-gray-50 transition disabled:opacity-50"
      >
        <span v-if="loading">Refreshing...</span>
        <span v-else>Refresh</span>
      </button>
    </div>

    <!-- Chart -->
    <div v-if="!loading && !error" class="relative">
      <canvas
        ref="chartCanvas"
        class="w-full"
        style="height: 400px;"
      ></canvas>
      
      <!-- Status Message -->
      <div v-if="chartData" class="mt-4 p-3 bg-blue-50 border border-blue-200 rounded-lg text-center">
        <p class="text-sm font-medium text-blue-900">{{ statusMessage }}</p>
        <p class="text-xs text-blue-700 mt-1">
          Current Remaining: {{ chartData.current_remaining }} points
        </p>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="flex items-center justify-center py-24">
      <div class="text-center">
        <div class="inline-block animate-spin rounded-full h-12 w-12 border-4 border-gray-300 border-t-blue-600"></div>
        <div class="mt-4 text-sm text-gray-600">Loading burndown data...</div>
      </div>
    </div>

    <!-- Error State -->
    <div v-if="error" class="flex items-center justify-center py-24">
      <div class="text-center">
        <div class="text-4xl mb-4">⚠️</div>
        <div class="text-lg font-medium text-gray-900 mb-2">{{ error }}</div>
        <button
          @click="fetchBurndownData"
          class="mt-4 px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition"
        >
          Retry
        </button>
      </div>
    </div>
  </div>
</template>

<style scoped>
.burndown-chart canvas {
  display: block;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

.animate-spin {
  animation: spin 1s linear infinite;
}
</style>
