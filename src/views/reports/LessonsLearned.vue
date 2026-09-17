<script setup>
import { ref } from 'vue'
import PageHeader from '@/components/ui/PageHeader.vue'

const lessons = ref([
  { id: 1, title: 'Early stakeholder involvement improves outcomes', category: 'process', project: 'Website Redesign', impact: 'positive', date: '2024-11-30' },
  { id: 2, title: 'Underestimated API integration complexity', category: 'technical', project: 'CRM Integration', impact: 'negative', date: '2024-11-25' },
  { id: 3, title: 'Daily standups improved team communication', category: 'team', project: 'Mobile App', impact: 'positive', date: '2024-11-20' },
  { id: 4, title: 'Scope changes without impact analysis caused delays', category: 'scope', project: 'Data Migration', impact: 'negative', date: '2024-11-15' },
  { id: 5, title: 'Automated testing reduced bug count by 60%', category: 'quality', project: 'Website Redesign', impact: 'positive', date: '2024-11-10' }
])

const newLesson = ref({ title: '', category: '', description: '' })
</script>

<template>
  <div>
    <PageHeader title="Lessons Learned" subtitle="Capture and share project insights">
      <template #actions>
        <button class="ti-btn ti-btn-primary">
          <i class="ri-add-line me-1"></i> Add Lesson
        </button>
      </template>
    </PageHeader>

    <div class="grid grid-cols-12 gap-6">
      <!-- Lessons List -->
      <div class="col-span-12 xl:col-span-8">
        <div class="box">
          <div class="box-header">
            <h5 class="box-title">Recorded Lessons</h5>
          </div>
          <div class="box-body space-y-4">
            <div v-for="lesson in lessons" :key="lesson.id" class="p-4 border rounded-lg">
              <div class="flex items-start justify-between mb-2">
                <h6 class="font-medium">{{ lesson.title }}</h6>
                <span class="badge" :class="lesson.impact === 'positive' ? 'bg-success/10 text-success' : 'bg-danger/10 text-danger'">
                  {{ lesson.impact }}
                </span>
              </div>
              <div class="flex flex-wrap gap-2 text-sm text-textmuted">
                <span><i class="ri-folder-line me-1"></i>{{ lesson.project }}</span>
                <span><i class="ri-price-tag-3-line me-1"></i>{{ lesson.category }}</span>
                <span><i class="ri-calendar-line me-1"></i>{{ lesson.date }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Add New / Stats -->
      <div class="col-span-12 xl:col-span-4">
        <div class="box">
          <div class="box-header">
            <h5 class="box-title">Quick Add</h5>
          </div>
          <div class="box-body">
            <div class="mb-3">
              <label class="ti-form-label">Title</label>
              <input v-model="newLesson.title" type="text" class="ti-form-control" placeholder="What did you learn?">
            </div>
            <div class="mb-3">
              <label class="ti-form-label">Category</label>
              <select v-model="newLesson.category" class="ti-form-select">
                <option value="">Select category</option>
                <option value="process">Process</option>
                <option value="technical">Technical</option>
                <option value="team">Team</option>
                <option value="scope">Scope</option>
                <option value="quality">Quality</option>
              </select>
            </div>
            <div class="mb-3">
              <label class="ti-form-label">Description</label>
              <textarea v-model="newLesson.description" class="ti-form-control" rows="3" placeholder="Describe the lesson in detail..."></textarea>
            </div>
            <button class="ti-btn ti-btn-primary w-full">Save Lesson</button>
          </div>
        </div>

        <!-- Stats -->
        <div class="box">
          <div class="box-header">
            <h5 class="box-title">Summary</h5>
          </div>
          <div class="box-body">
            <div class="space-y-3">
              <div class="flex items-center justify-between">
                <span class="text-textmuted">Total Lessons</span>
                <span class="font-bold">{{ lessons.length }}</span>
              </div>
              <div class="flex items-center justify-between">
                <span class="text-textmuted">Positive</span>
                <span class="font-bold text-success">{{ lessons.filter(l => l.impact === 'positive').length }}</span>
              </div>
              <div class="flex items-center justify-between">
                <span class="text-textmuted">Areas to Improve</span>
                <span class="font-bold text-danger">{{ lessons.filter(l => l.impact === 'negative').length }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

