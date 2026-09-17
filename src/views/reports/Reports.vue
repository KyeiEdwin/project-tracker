<script setup>
import { ref } from 'vue'
import PageHeader from '@/components/ui/PageHeader.vue'

const reportTypes = ref([
  { id: 1, name: 'Project Status Report', description: 'Overall project health and progress', icon: 'ri-bar-chart-line', color: 'primary' },
  { id: 2, name: 'Sprint Report', description: 'Sprint velocity and burndown', icon: 'ri-speed-line', color: 'success' },
  { id: 3, name: 'Resource Utilization', description: 'Team allocation and availability', icon: 'ri-user-line', color: 'info' },
  { id: 4, name: 'Budget Report', description: 'Budget vs actual spending', icon: 'ri-money-dollar-circle-line', color: 'warning' },
  { id: 5, name: 'Risk Report', description: 'Active risks and mitigation status', icon: 'ri-alert-line', color: 'danger' },
  { id: 6, name: 'Time Tracking Report', description: 'Hours logged by project/task', icon: 'ri-time-line', color: 'secondary' }
])

const selectedReport = ref('')
const dateRange = ref({ start: '', end: '' })
</script>

<template>
  <div>
    <PageHeader title="Reports & Analytics" subtitle="Generate and view project reports">
      <template #actions>
        <button class="ti-btn ti-btn-primary">
          <i class="ri-download-line me-1"></i> Export All
        </button>
      </template>
    </PageHeader>

    <div class="grid grid-cols-12 gap-6">
      <!-- Report Types -->
      <div class="col-span-12 xl:col-span-8">
        <div class="grid grid-cols-12 gap-4">
          <div v-for="report in reportTypes" :key="report.id" class="col-span-12 md:col-span-6 lg:col-span-4">
            <div class="box h-full cursor-pointer hover:shadow-lg transition-shadow" @click="selectedReport = report.name">
              <div class="box-body text-center">
                <span class="avatar avatar-lg mb-3" :class="`bg-${report.color}/10 text-${report.color}`">
                  <i :class="report.icon" class="text-2xl"></i>
                </span>
                <h6 class="font-medium mb-1">{{ report.name }}</h6>
                <p class="text-sm text-textmuted">{{ report.description }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Report Configuration -->
      <div class="col-span-12 xl:col-span-4">
        <div class="box">
          <div class="box-header">
            <h5 class="box-title">Generate Report</h5>
          </div>
          <div class="box-body">
            <div class="mb-4">
              <label class="ti-form-label">Report Type</label>
              <select v-model="selectedReport" class="ti-form-select">
                <option value="">Select a report</option>
                <option v-for="report in reportTypes" :key="report.id" :value="report.name">{{ report.name }}</option>
              </select>
            </div>
            <div class="mb-4">
              <label class="ti-form-label">Date Range</label>
              <div class="grid grid-cols-2 gap-2">
                <input v-model="dateRange.start" type="date" class="ti-form-control" placeholder="Start">
                <input v-model="dateRange.end" type="date" class="ti-form-control" placeholder="End">
              </div>
            </div>
            <div class="mb-4">
              <label class="ti-form-label">Project</label>
              <select class="ti-form-select">
                <option>All Projects</option>
                <option>Website Redesign</option>
                <option>Mobile App</option>
                <option>CRM Integration</option>
              </select>
            </div>
            <div class="mb-4">
              <label class="ti-form-label">Format</label>
              <div class="flex gap-2">
                <button class="ti-btn ti-btn-light flex-1">PDF</button>
                <button class="ti-btn ti-btn-light flex-1">Excel</button>
                <button class="ti-btn ti-btn-primary flex-1">Preview</button>
              </div>
            </div>
            <button class="ti-btn ti-btn-primary w-full">
              <i class="ri-file-chart-line me-1"></i> Generate Report
            </button>
          </div>
        </div>

        <!-- Recent Reports -->
        <div class="box">
          <div class="box-header">
            <h5 class="box-title">Recent Reports</h5>
          </div>
          <div class="box-body p-0">
            <ul class="list-group list-group-flush">
              <li class="list-group-item flex items-center justify-between">
                <div>
                  <span class="font-medium block">Sprint 11 Report</span>
                  <span class="text-xs text-textmuted">Dec 1, 2024</span>
                </div>
                <button class="ti-btn ti-btn-soft-primary ti-btn-sm ti-btn-icon"><i class="ri-download-line"></i></button>
              </li>
              <li class="list-group-item flex items-center justify-between">
                <div>
                  <span class="font-medium block">November Budget</span>
                  <span class="text-xs text-textmuted">Nov 30, 2024</span>
                </div>
                <button class="ti-btn ti-btn-soft-primary ti-btn-sm ti-btn-icon"><i class="ri-download-line"></i></button>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

