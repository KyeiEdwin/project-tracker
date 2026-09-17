<script setup>
import { ref } from 'vue'
import PageHeader from '@/components/ui/PageHeader.vue'

const dorItems = ref([
  { id: 1, text: 'User story is clearly defined with acceptance criteria', checked: true },
  { id: 2, text: 'Story has been estimated by the team', checked: true },
  { id: 3, text: 'Dependencies have been identified and resolved', checked: false },
  { id: 4, text: 'Design mockups are approved (if applicable)', checked: true },
  { id: 5, text: 'Technical approach has been discussed', checked: false }
])

const dodItems = ref([
  { id: 1, text: 'Code has been peer reviewed', checked: true },
  { id: 2, text: 'Unit tests written and passing', checked: true },
  { id: 3, text: 'Integration tests passing', checked: false },
  { id: 4, text: 'Documentation updated', checked: false },
  { id: 5, text: 'Deployed to staging environment', checked: true },
  { id: 6, text: 'QA testing completed', checked: false },
  { id: 7, text: 'Product owner has approved', checked: false }
])

const completionRate = (items) => {
  const completed = items.filter(i => i.checked).length
  return Math.round((completed / items.length) * 100)
}
</script>

<template>
  <div>
    <PageHeader title="DoR / DoD Framework" subtitle="Definition of Ready and Definition of Done checklists">
      <template #actions>
        <button class="ti-btn ti-btn-primary">
          <i class="ri-settings-3-line me-1"></i> Configure
        </button>
      </template>
    </PageHeader>

    <div class="grid grid-cols-12 gap-6">
      <!-- Definition of Ready -->
      <div class="col-span-12 xl:col-span-6">
        <div class="box">
          <div class="box-header bg-primary/10">
            <div class="flex items-center justify-between">
              <h5 class="box-title text-primary">
                <i class="ri-checkbox-circle-line me-2"></i>
                Definition of Ready (DoR)
              </h5>
              <span class="badge bg-primary">{{ completionRate(dorItems) }}%</span>
            </div>
          </div>
          <div class="box-body">
            <p class="text-textmuted mb-4">Criteria that must be met before a story can be taken into a sprint.</p>
            <ul class="space-y-3">
              <li v-for="item in dorItems" :key="item.id" class="flex items-start gap-3 p-3 bg-light rounded-lg">
                <input type="checkbox" class="ti-form-check-input mt-1" v-model="item.checked">
                <span :class="{ 'line-through text-textmuted': item.checked }">{{ item.text }}</span>
              </li>
            </ul>
          </div>
          <div class="box-footer">
            <button class="ti-btn ti-btn-primary ti-btn-sm">
              <i class="ri-add-line me-1"></i> Add Criteria
            </button>
          </div>
        </div>
      </div>

      <!-- Definition of Done -->
      <div class="col-span-12 xl:col-span-6">
        <div class="box">
          <div class="box-header bg-success/10">
            <div class="flex items-center justify-between">
              <h5 class="box-title text-success">
                <i class="ri-check-double-line me-2"></i>
                Definition of Done (DoD)
              </h5>
              <span class="badge bg-success">{{ completionRate(dodItems) }}%</span>
            </div>
          </div>
          <div class="box-body">
            <p class="text-textmuted mb-4">Criteria that must be met before a story is considered complete.</p>
            <ul class="space-y-3">
              <li v-for="item in dodItems" :key="item.id" class="flex items-start gap-3 p-3 bg-light rounded-lg">
                <input type="checkbox" class="ti-form-check-input mt-1" v-model="item.checked">
                <span :class="{ 'line-through text-textmuted': item.checked }">{{ item.text }}</span>
              </li>
            </ul>
          </div>
          <div class="box-footer">
            <button class="ti-btn ti-btn-success ti-btn-sm">
              <i class="ri-add-line me-1"></i> Add Criteria
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

