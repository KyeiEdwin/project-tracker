<script setup>
import PageHeader from '@/components/ui/PageHeader.vue'

defineProps({
  charts: { type: Array, default: () => [] },
  pagination: { type: Object, default: () => ({}) },
})
</script>

<template>
  <div>
    <PageHeader title="Charts" subtitle="Saved project visualizations">
      <template #actions>
        <a href="/charts/create" class="ti-btn ti-btn-primary">
          <i class="ri-add-line me-1"></i> New Chart
        </a>
      </template>
    </PageHeader>

    <div class="box">
      <div class="box-header">
        <h5 class="box-title">Chart library</h5>
      </div>
      <div class="box-body p-0">
        <table class="table table-hover whitespace-nowrap">
          <thead>
            <tr>
              <th>Title</th>
              <th>Type</th>
              <th>Project</th>
              <th>Source</th>
              <th>Public</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="!charts.length">
              <td colspan="5" class="text-center text-textmuted py-8">No charts saved yet.</td>
            </tr>
            <tr v-for="chart in charts" :key="chart.id">
              <td class="font-medium">{{ chart.title }}</td>
              <td><span class="badge bg-primary/10 text-primary">{{ chart.chartType }}</span></td>
              <td class="text-textmuted">{{ chart.project || 'All projects' }}</td>
              <td>{{ chart.dataSource || '—' }}</td>
              <td>{{ chart.isPublic ? 'Yes' : 'No' }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>
