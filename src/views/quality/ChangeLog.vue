<script setup>
import { ref } from 'vue'
import PageHeader from '@/components/ui/PageHeader.vue'

const changes = ref([
  { id: 1, title: 'Add user profile page', type: 'feature', requestor: 'John Smith', status: 'approved', impact: 'medium', date: '2024-12-01' },
  { id: 2, title: 'Extend deadline by 2 weeks', type: 'schedule', requestor: 'Jane Doe', status: 'pending', impact: 'high', date: '2024-12-02' },
  { id: 3, title: 'Remove legacy API support', type: 'scope', requestor: 'Mike Johnson', status: 'approved', impact: 'low', date: '2024-11-28' },
  { id: 4, title: 'Increase budget for testing', type: 'budget', requestor: 'Sarah Wilson', status: 'rejected', impact: 'medium', date: '2024-11-25' },
  { id: 5, title: 'Add mobile push notifications', type: 'feature', requestor: 'David Brown', status: 'pending', impact: 'high', date: '2024-12-03' }
])
</script>

<template>
  <div>
    <PageHeader title="Change Log" subtitle="Track change requests and approvals">
      <template #actions>
        <button class="ti-btn ti-btn-primary">
          <i class="ri-add-line me-1"></i> New Change Request
        </button>
      </template>
    </PageHeader>

    <div class="grid grid-cols-12 gap-6">
      <!-- Stats -->
      <div class="col-span-12 md:col-span-4">
        <div class="box">
          <div class="box-body text-center">
            <span class="badge bg-warning/10 text-warning mb-2">Pending</span>
            <h4 class="text-2xl font-bold">{{ changes.filter(c => c.status === 'pending').length }}</h4>
          </div>
        </div>
      </div>
      <div class="col-span-12 md:col-span-4">
        <div class="box">
          <div class="box-body text-center">
            <span class="badge bg-success/10 text-success mb-2">Approved</span>
            <h4 class="text-2xl font-bold">{{ changes.filter(c => c.status === 'approved').length }}</h4>
          </div>
        </div>
      </div>
      <div class="col-span-12 md:col-span-4">
        <div class="box">
          <div class="box-body text-center">
            <span class="badge bg-danger/10 text-danger mb-2">Rejected</span>
            <h4 class="text-2xl font-bold">{{ changes.filter(c => c.status === 'rejected').length }}</h4>
          </div>
        </div>
      </div>

      <!-- Change List -->
      <div class="col-span-12">
        <div class="box">
          <div class="box-header">
            <h5 class="box-title">Change Requests</h5>
          </div>
          <div class="box-body p-0">
            <table class="table table-hover whitespace-nowrap">
              <thead>
                <tr>
                  <th>Change Request</th>
                  <th>Type</th>
                  <th>Requestor</th>
                  <th>Impact</th>
                  <th>Status</th>
                  <th>Date</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="change in changes" :key="change.id">
                  <td class="font-medium">{{ change.title }}</td>
                  <td><span class="badge bg-secondary/10 text-secondary">{{ change.type }}</span></td>
                  <td class="text-textmuted">{{ change.requestor }}</td>
                  <td>
                    <span class="badge" :class="{
                      'bg-danger/10 text-danger': change.impact === 'high',
                      'bg-warning/10 text-warning': change.impact === 'medium',
                      'bg-success/10 text-success': change.impact === 'low'
                    }">{{ change.impact }}</span>
                  </td>
                  <td>
                    <span class="badge" :class="{
                      'bg-warning/10 text-warning': change.status === 'pending',
                      'bg-success/10 text-success': change.status === 'approved',
                      'bg-danger/10 text-danger': change.status === 'rejected'
                    }">{{ change.status }}</span>
                  </td>
                  <td class="text-textmuted">{{ change.date }}</td>
                  <td>
                    <div class="flex gap-1">
                      <button class="ti-btn ti-btn-soft-success ti-btn-icon ti-btn-sm" title="Approve"><i class="ri-check-line"></i></button>
                      <button class="ti-btn ti-btn-soft-danger ti-btn-icon ti-btn-sm" title="Reject"><i class="ri-close-line"></i></button>
                      <button class="ti-btn ti-btn-soft-primary ti-btn-icon ti-btn-sm" title="View"><i class="ri-eye-line"></i></button>
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

