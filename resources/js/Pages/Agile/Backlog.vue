<script setup>
import { ref } from 'vue'
import PageHeader from '@/components/ui/PageHeader.vue'

const backlogItems = ref([
  { id: 1, title: 'User authentication system', type: 'epic', priority: 'high', points: 21, status: 'in-progress' },
  { id: 2, title: 'Dashboard redesign', type: 'feature', priority: 'medium', points: 13, status: 'ready' },
  { id: 3, title: 'API rate limiting', type: 'story', priority: 'high', points: 5, status: 'ready' },
  { id: 4, title: 'Mobile responsive layout', type: 'story', priority: 'medium', points: 8, status: 'backlog' },
  { id: 5, title: 'Performance optimization', type: 'epic', priority: 'low', points: 34, status: 'backlog' },
  { id: 6, title: 'Email notifications', type: 'feature', priority: 'medium', points: 13, status: 'ready' }
])

const getTypeClass = (type) => ({
  'epic': 'bg-purple-500/10 text-purple-500',
  'feature': 'bg-primary/10 text-primary',
  'story': 'bg-info/10 text-info'
})[type]

const getPriorityClass = (priority) => ({
  'high': 'bg-danger/10 text-danger',
  'medium': 'bg-warning/10 text-warning',
  'low': 'bg-success/10 text-success'
})[priority]
</script>

<template>
  <div>
    <PageHeader title="Product Backlog" subtitle="Prioritize and manage backlog items">
      <template #actions>
        <button class="ti-btn ti-btn-primary">
          <i class="ri-add-line me-1"></i> Add Item
        </button>
      </template>
    </PageHeader>

    <div class="grid grid-cols-12 gap-6">
      <!-- Stats -->
      <div class="col-span-12 xl:col-span-3">
        <div class="box">
          <div class="box-body text-center">
            <i class="ri-stack-line text-4xl text-primary mb-2"></i>
            <h4 class="text-2xl font-bold">{{ backlogItems.length }}</h4>
            <p class="text-textmuted">Total Items</p>
          </div>
        </div>
      </div>
      <div class="col-span-12 xl:col-span-3">
        <div class="box">
          <div class="box-body text-center">
            <i class="ri-checkbox-circle-line text-4xl text-success mb-2"></i>
            <h4 class="text-2xl font-bold">{{ backlogItems.filter(i => i.status === 'ready').length }}</h4>
            <p class="text-textmuted">Ready for Sprint</p>
          </div>
        </div>
      </div>
      <div class="col-span-12 xl:col-span-3">
        <div class="box">
          <div class="box-body text-center">
            <i class="ri-fire-line text-4xl text-danger mb-2"></i>
            <h4 class="text-2xl font-bold">{{ backlogItems.filter(i => i.priority === 'high').length }}</h4>
            <p class="text-textmuted">High Priority</p>
          </div>
        </div>
      </div>
      <div class="col-span-12 xl:col-span-3">
        <div class="box">
          <div class="box-body text-center">
            <i class="ri-bar-chart-horizontal-line text-4xl text-info mb-2"></i>
            <h4 class="text-2xl font-bold">{{ backlogItems.reduce((sum, i) => sum + i.points, 0) }}</h4>
            <p class="text-textmuted">Total Points</p>
          </div>
        </div>
      </div>

      <!-- Backlog List -->
      <div class="col-span-12">
        <div class="box">
          <div class="box-header flex items-center justify-between">
            <h5 class="box-title">Backlog Items</h5>
            <div class="flex gap-2">
              <select class="ti-form-select form-select-sm w-auto">
                <option>All Types</option>
                <option>Epic</option>
                <option>Feature</option>
                <option>Story</option>
              </select>
              <select class="ti-form-select form-select-sm w-auto">
                <option>All Priorities</option>
                <option>High</option>
                <option>Medium</option>
                <option>Low</option>
              </select>
            </div>
          </div>
          <div class="box-body p-0">
            <table class="table table-hover whitespace-nowrap">
              <thead>
                <tr>
                  <th class="w-8"></th>
                  <th>Title</th>
                  <th>Type</th>
                  <th>Priority</th>
                  <th>Points</th>
                  <th>Status</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="item in backlogItems" :key="item.id" class="cursor-move">
                  <td><i class="ri-draggable text-textmuted"></i></td>
                  <td class="font-medium">{{ item.title }}</td>
                  <td><span class="badge" :class="getTypeClass(item.type)">{{ item.type }}</span></td>
                  <td><span class="badge" :class="getPriorityClass(item.priority)">{{ item.priority }}</span></td>
                  <td>{{ item.points }}</td>
                  <td>
                    <span class="badge" :class="{
                      'bg-primary/10 text-primary': item.status === 'in-progress',
                      'bg-success/10 text-success': item.status === 'ready',
                      'bg-secondary/10 text-secondary': item.status === 'backlog'
                    }">{{ item.status }}</span>
                  </td>
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

