<script setup>
import { ref } from 'vue'
import PageHeader from '@/components/ui/PageHeader.vue'

const testCases = ref([
  { id: 1, name: 'Login functionality', type: 'functional', status: 'passed', priority: 'high', lastRun: '2024-12-03' },
  { id: 2, name: 'Form validation', type: 'functional', status: 'passed', priority: 'medium', lastRun: '2024-12-03' },
  { id: 3, name: 'API response times', type: 'performance', status: 'failed', priority: 'high', lastRun: '2024-12-02' },
  { id: 4, name: 'Mobile responsiveness', type: 'ui', status: 'passed', priority: 'medium', lastRun: '2024-12-02' },
  { id: 5, name: 'Cross-browser compatibility', type: 'ui', status: 'pending', priority: 'low', lastRun: '-' }
])

const stats = ref({
  total: 45,
  passed: 38,
  failed: 4,
  pending: 3
})
</script>

<template>
  <div>
    <PageHeader title="QA & Testing" subtitle="Manage test cases and track quality metrics">
      <template #actions>
        <button class="ti-btn ti-btn-light">
          <i class="ri-play-line me-1"></i> Run All Tests
        </button>
        <button class="ti-btn ti-btn-primary">
          <i class="ri-add-line me-1"></i> New Test Case
        </button>
      </template>
    </PageHeader>

    <div class="grid grid-cols-12 gap-6">
      <!-- Stats -->
      <div class="col-span-12 md:col-span-6 xl:col-span-3">
        <div class="box">
          <div class="box-body text-center">
            <i class="ri-test-tube-line text-4xl text-primary mb-2"></i>
            <h4 class="text-2xl font-bold">{{ stats.total }}</h4>
            <p class="text-textmuted">Total Tests</p>
          </div>
        </div>
      </div>
      <div class="col-span-12 md:col-span-6 xl:col-span-3">
        <div class="box">
          <div class="box-body text-center">
            <i class="ri-checkbox-circle-line text-4xl text-success mb-2"></i>
            <h4 class="text-2xl font-bold text-success">{{ stats.passed }}</h4>
            <p class="text-textmuted">Passed</p>
          </div>
        </div>
      </div>
      <div class="col-span-12 md:col-span-6 xl:col-span-3">
        <div class="box">
          <div class="box-body text-center">
            <i class="ri-close-circle-line text-4xl text-danger mb-2"></i>
            <h4 class="text-2xl font-bold text-danger">{{ stats.failed }}</h4>
            <p class="text-textmuted">Failed</p>
          </div>
        </div>
      </div>
      <div class="col-span-12 md:col-span-6 xl:col-span-3">
        <div class="box">
          <div class="box-body text-center">
            <i class="ri-time-line text-4xl text-warning mb-2"></i>
            <h4 class="text-2xl font-bold text-warning">{{ stats.pending }}</h4>
            <p class="text-textmuted">Pending</p>
          </div>
        </div>
      </div>

      <!-- Test Cases -->
      <div class="col-span-12">
        <div class="box">
          <div class="box-header">
            <h5 class="box-title">Test Cases</h5>
          </div>
          <div class="box-body p-0">
            <table class="table table-hover whitespace-nowrap">
              <thead>
                <tr>
                  <th>Test Name</th>
                  <th>Type</th>
                  <th>Priority</th>
                  <th>Status</th>
                  <th>Last Run</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="test in testCases" :key="test.id">
                  <td class="font-medium">{{ test.name }}</td>
                  <td><span class="badge bg-secondary/10 text-secondary">{{ test.type }}</span></td>
                  <td>
                    <span class="badge" :class="{
                      'bg-danger/10 text-danger': test.priority === 'high',
                      'bg-warning/10 text-warning': test.priority === 'medium',
                      'bg-success/10 text-success': test.priority === 'low'
                    }">{{ test.priority }}</span>
                  </td>
                  <td>
                    <span class="badge" :class="{
                      'bg-success/10 text-success': test.status === 'passed',
                      'bg-danger/10 text-danger': test.status === 'failed',
                      'bg-warning/10 text-warning': test.status === 'pending'
                    }">{{ test.status }}</span>
                  </td>
                  <td class="text-textmuted">{{ test.lastRun }}</td>
                  <td>
                    <div class="flex gap-1">
                      <button class="ti-btn ti-btn-soft-primary ti-btn-icon ti-btn-sm"><i class="ri-play-line"></i></button>
                      <button class="ti-btn ti-btn-soft-info ti-btn-icon ti-btn-sm"><i class="ri-edit-line"></i></button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

