<script setup>
import { ref } from 'vue'
import PageHeader from '@/components/ui/PageHeader.vue'

const timeEntries = ref([
  { id: 1, task: 'Frontend Development', project: 'Website Redesign', user: 'John Doe', date: '2024-12-03', hours: 6 },
  { id: 2, task: 'API Integration', project: 'Mobile App', user: 'Jane Smith', date: '2024-12-03', hours: 8 },
  { id: 3, task: 'Design Review', project: 'Website Redesign', user: 'Mike Johnson', date: '2024-12-03', hours: 4 },
  { id: 4, task: 'Bug Fixing', project: 'CRM Integration', user: 'Sarah Wilson', date: '2024-12-02', hours: 5 },
  { id: 5, task: 'Database Setup', project: 'Data Migration', user: 'David Brown', date: '2024-12-02', hours: 7 }
])

const weeklyTotal = ref(30)
const monthlyTotal = ref(156)
</script>

<template>
  <div>
    <PageHeader title="Time Tracking" subtitle="Track time spent on projects and tasks">
      <template #actions>
        <button class="ti-btn ti-btn-primary">
          <i class="ri-time-line me-1"></i> Log Time
        </button>
      </template>
    </PageHeader>

    <div class="grid grid-cols-12 gap-6">
      <!-- Stats -->
      <div class="col-span-12 md:col-span-6 xl:col-span-3">
        <div class="box">
          <div class="box-body">
            <div class="flex items-center gap-4">
              <span class="avatar avatar-lg bg-primary/10 text-primary">
                <i class="ri-time-line text-2xl"></i>
              </span>
              <div>
                <p class="text-textmuted text-sm">Today</p>
                <h4 class="text-xl font-bold">6.5 hrs</h4>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-span-12 md:col-span-6 xl:col-span-3">
        <div class="box">
          <div class="box-body">
            <div class="flex items-center gap-4">
              <span class="avatar avatar-lg bg-success/10 text-success">
                <i class="ri-calendar-check-line text-2xl"></i>
              </span>
              <div>
                <p class="text-textmuted text-sm">This Week</p>
                <h4 class="text-xl font-bold">{{ weeklyTotal }} hrs</h4>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-span-12 md:col-span-6 xl:col-span-3">
        <div class="box">
          <div class="box-body">
            <div class="flex items-center gap-4">
              <span class="avatar avatar-lg bg-info/10 text-info">
                <i class="ri-calendar-line text-2xl"></i>
              </span>
              <div>
                <p class="text-textmuted text-sm">This Month</p>
                <h4 class="text-xl font-bold">{{ monthlyTotal }} hrs</h4>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-span-12 md:col-span-6 xl:col-span-3">
        <div class="box">
          <div class="box-body">
            <div class="flex items-center gap-4">
              <span class="avatar avatar-lg bg-warning/10 text-warning">
                <i class="ri-funds-line text-2xl"></i>
              </span>
              <div>
                <p class="text-textmuted text-sm">Billable</p>
                <h4 class="text-xl font-bold">85%</h4>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Time Entries -->
      <div class="col-span-12">
        <div class="box">
          <div class="box-header">
            <h5 class="box-title">Recent Time Entries</h5>
          </div>
          <div class="box-body p-0">
            <table class="table table-hover whitespace-nowrap">
              <thead>
                <tr>
                  <th>Task</th>
                  <th>Project</th>
                  <th>Team Member</th>
                  <th>Date</th>
                  <th>Hours</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="entry in timeEntries" :key="entry.id">
                  <td class="font-medium">{{ entry.task }}</td>
                  <td class="text-textmuted">{{ entry.project }}</td>
                  <td>{{ entry.user }}</td>
                  <td>{{ entry.date }}</td>
                  <td><span class="badge bg-primary/10 text-primary">{{ entry.hours }} hrs</span></td>
                  <td>
                    <div class="flex gap-1">
                      <button class="ti-btn ti-btn-soft-info ti-btn-icon ti-btn-sm"><i class="ri-edit-line"></i></button>
                      <button class="ti-btn ti-btn-soft-danger ti-btn-icon ti-btn-sm"><i class="ri-delete-bin-line"></i></button>
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

