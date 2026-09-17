<script setup>
import { ref } from 'vue'
import PageHeader from '@/components/ui/PageHeader.vue'

const teamMembers = ref([
  { id: 1, name: 'John Doe', role: 'Frontend Developer', email: 'john@example.com', availability: 100, projects: 3 },
  { id: 2, name: 'Jane Smith', role: 'Backend Developer', email: 'jane@example.com', availability: 80, projects: 2 },
  { id: 3, name: 'Mike Johnson', role: 'UI/UX Designer', email: 'mike@example.com', availability: 100, projects: 4 },
  { id: 4, name: 'Sarah Wilson', role: 'QA Engineer', email: 'sarah@example.com', availability: 60, projects: 2 },
  { id: 5, name: 'David Brown', role: 'DevOps Engineer', email: 'david@example.com', availability: 50, projects: 5 }
])
</script>

<template>
  <div>
    <PageHeader title="Team Resources" subtitle="Manage team members and allocations">
      <template #actions>
        <button class="ti-btn ti-btn-primary">
          <i class="ri-user-add-line me-1"></i> Add Member
        </button>
      </template>
    </PageHeader>

    <div class="grid grid-cols-12 gap-6">
      <!-- Team Stats -->
      <div class="col-span-12 xl:col-span-3">
        <div class="box">
          <div class="box-body text-center">
            <span class="avatar avatar-lg bg-primary/10 text-primary mb-3">
              <i class="ri-team-line text-2xl"></i>
            </span>
            <h4 class="text-2xl font-bold">{{ teamMembers.length }}</h4>
            <p class="text-textmuted">Team Members</p>
          </div>
        </div>
      </div>
      <div class="col-span-12 xl:col-span-3">
        <div class="box">
          <div class="box-body text-center">
            <span class="avatar avatar-lg bg-success/10 text-success mb-3">
              <i class="ri-check-double-line text-2xl"></i>
            </span>
            <h4 class="text-2xl font-bold">{{ teamMembers.filter(m => m.availability === 100).length }}</h4>
            <p class="text-textmuted">Fully Available</p>
          </div>
        </div>
      </div>
      <div class="col-span-12 xl:col-span-3">
        <div class="box">
          <div class="box-body text-center">
            <span class="avatar avatar-lg bg-warning/10 text-warning mb-3">
              <i class="ri-time-line text-2xl"></i>
            </span>
            <h4 class="text-2xl font-bold">{{ teamMembers.filter(m => m.availability < 100 && m.availability > 50).length }}</h4>
            <p class="text-textmuted">Partially Allocated</p>
          </div>
        </div>
      </div>
      <div class="col-span-12 xl:col-span-3">
        <div class="box">
          <div class="box-body text-center">
            <span class="avatar avatar-lg bg-danger/10 text-danger mb-3">
              <i class="ri-user-unfollow-line text-2xl"></i>
            </span>
            <h4 class="text-2xl font-bold">{{ teamMembers.filter(m => m.availability <= 50).length }}</h4>
            <p class="text-textmuted">Over-allocated</p>
          </div>
        </div>
      </div>

      <!-- Team List -->
      <div class="col-span-12">
        <div class="box">
          <div class="box-header">
            <h5 class="box-title">Team Members</h5>
          </div>
          <div class="box-body p-0">
            <table class="table table-hover whitespace-nowrap">
              <thead>
                <tr>
                  <th>Member</th>
                  <th>Role</th>
                  <th>Email</th>
                  <th>Availability</th>
                  <th>Projects</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="member in teamMembers" :key="member.id">
                  <td>
                    <div class="flex items-center gap-3">
                      <span class="avatar avatar-sm bg-primary/10 text-primary avatar-rounded">
                        {{ member.name.split(' ').map(n => n[0]).join('') }}
                      </span>
                      <span class="font-medium">{{ member.name }}</span>
                    </div>
                  </td>
                  <td>{{ member.role }}</td>
                  <td class="text-textmuted">{{ member.email }}</td>
                  <td>
                    <div class="flex items-center gap-2">
                      <div class="progress progress-xs flex-1 max-w-[80px]">
                        <div class="progress-bar" :class="{
                          'bg-success': member.availability === 100,
                          'bg-warning': member.availability < 100 && member.availability > 50,
                          'bg-danger': member.availability <= 50
                        }" :style="{ width: member.availability + '%' }"></div>
                      </div>
                      <span class="text-xs">{{ member.availability }}%</span>
                    </div>
                  </td>
                  <td>{{ member.projects }}</td>
                  <td>
                    <div class="flex gap-1">
                      <button class="ti-btn ti-btn-soft-primary ti-btn-icon ti-btn-sm"><i class="ri-eye-line"></i></button>
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

