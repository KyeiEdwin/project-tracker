<script setup>
import { ref, computed } from 'vue'
import PageHeader from '@/components/ui/PageHeader.vue'

const searchQuery = ref('')
const statusFilter = ref('all')
const projects = ref([
  {
    id: 1,
    name: 'Website Redesign',
    description: 'Complete redesign of corporate website',
    team: 'Marketing Team',
    status: 'in-progress',
    progress: 75,
    priority: 'high',
    dueDate: '2024-12-15',
    budget: 50000,
    spent: 35000
  },
  {
    id: 2,
    name: 'Mobile App Development',
    description: 'Native iOS and Android app development',
    team: 'Development Team',
    status: 'on-hold',
    progress: 45,
    priority: 'medium',
    dueDate: '2024-12-20',
    budget: 120000,
    spent: 54000
  },
  {
    id: 3,
    name: 'CRM Integration',
    description: 'Integrate Salesforce with internal systems',
    team: 'IT Team',
    status: 'completed',
    progress: 100,
    priority: 'low',
    dueDate: '2024-11-30',
    budget: 30000,
    spent: 28500
  },
  {
    id: 4,
    name: 'Data Migration',
    description: 'Migrate legacy data to new cloud platform',
    team: 'Database Team',
    status: 'in-progress',
    progress: 60,
    priority: 'high',
    dueDate: '2024-12-10',
    budget: 75000,
    spent: 45000
  },
  {
    id: 5,
    name: 'Security Audit',
    description: 'Annual security assessment and compliance review',
    team: 'Security Team',
    status: 'planning',
    progress: 10,
    priority: 'high',
    dueDate: '2024-12-25',
    budget: 25000,
    spent: 2500
  }
])

const filteredProjects = computed(() => {
  return projects.value.filter(project => {
    const matchesSearch = project.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
                         project.description.toLowerCase().includes(searchQuery.value.toLowerCase())
    const matchesStatus = statusFilter.value === 'all' || project.status === statusFilter.value
    return matchesSearch && matchesStatus
  })
})

const getStatusClass = (status) => {
  const classes = {
    'planning': 'bg-info/10 text-info',
    'in-progress': 'bg-primary/10 text-primary',
    'on-hold': 'bg-warning/10 text-warning',
    'completed': 'bg-success/10 text-success'
  }
  return classes[status] || 'bg-secondary/10 text-secondary'
}

const getPriorityClass = (priority) => {
  const classes = {
    'high': 'bg-danger/10 text-danger',
    'medium': 'bg-warning/10 text-warning',
    'low': 'bg-success/10 text-success'
  }
  return classes[priority] || 'bg-secondary/10 text-secondary'
}

const formatDate = (dateStr) => {
  return new Date(dateStr).toLocaleDateString('en-US', { 
    month: 'short', 
    day: 'numeric', 
    year: 'numeric' 
  })
}

const formatCurrency = (amount) => {
  return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD', maximumFractionDigits: 0 }).format(amount)
}
</script>

<template>
  <div>
    <PageHeader title="Projects List" subtitle="Manage and track all your projects">
      <template #actions>
        <router-link to="/projects/create" class="ti-btn ti-btn-primary btn-wave">
          <i class="ri-add-line me-1"></i> New Project
        </router-link>
      </template>
    </PageHeader>

    <div class="box">
      <div class="box-header flex flex-wrap items-center justify-between gap-4">
        <div class="flex items-center gap-3">
          <div class="relative">
            <input 
              v-model="searchQuery"
              type="text" 
              class="ti-form-control !ps-10" 
              placeholder="Search projects..."
            >
            <i class="ri-search-line absolute start-3 top-1/2 -translate-y-1/2 text-textmuted"></i>
          </div>
          <select v-model="statusFilter" class="ti-form-select w-auto">
            <option value="all">All Status</option>
            <option value="planning">Planning</option>
            <option value="in-progress">In Progress</option>
            <option value="on-hold">On Hold</option>
            <option value="completed">Completed</option>
          </select>
        </div>
        <div class="flex items-center gap-2">
          <button class="ti-btn ti-btn-light ti-btn-sm">
            <i class="ri-download-line me-1"></i> Export
          </button>
        </div>
      </div>

      <div class="box-body p-0">
        <div class="table-responsive">
          <table class="table table-hover whitespace-nowrap">
            <thead>
              <tr>
                <th>
                  <input type="checkbox" class="ti-form-check-input">
                </th>
                <th>Project</th>
                <th>Status</th>
                <th>Progress</th>
                <th>Priority</th>
                <th>Budget</th>
                <th>Due Date</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="project in filteredProjects" :key="project.id">
                <td>
                  <input type="checkbox" class="ti-form-check-input">
                </td>
                <td>
                  <div style="display: flex; align-items: center; gap: 0.75rem;">
                    <span class="avatar avatar-md bg-primary/10 text-primary avatar-rounded" style="display: inline-flex; align-items: center; justify-content: center;">
                      <i class="ri-folder-line" style="font-size: 1.125rem; line-height: 1;"></i>
                    </span>
                    <div>
                      <router-link :to="`/projects/${project.id}`" class="font-medium text-defaulttextcolor hover:text-primary">
                        {{ project.name }}
                      </router-link>
                      <p class="text-textmuted text-xs mb-0">{{ project.team }}</p>
                    </div>
                  </div>
                </td>
                <td>
                  <span class="badge" :class="getStatusClass(project.status)">
                    {{ project.status.replace('-', ' ') }}
                  </span>
                </td>
                <td>
                  <div class="flex items-center gap-2 min-w-[120px]">
                    <div class="progress progress-xs flex-1">
                      <div class="progress-bar bg-primary" :style="{ width: project.progress + '%' }"></div>
                    </div>
                    <span class="text-xs text-textmuted">{{ project.progress }}%</span>
                  </div>
                </td>
                <td>
                  <span class="badge" :class="getPriorityClass(project.priority)">
                    {{ project.priority }}
                  </span>
                </td>
                <td>
                  <div>
                    <span class="font-medium">{{ formatCurrency(project.spent) }}</span>
                    <span class="text-textmuted text-xs"> / {{ formatCurrency(project.budget) }}</span>
                  </div>
                </td>
                <td>{{ formatDate(project.dueDate) }}</td>
                <td>
                  <div class="flex gap-1">
                    <router-link :to="`/projects/${project.id}`" class="ti-btn ti-btn-soft-primary ti-btn-icon ti-btn-sm">
                      <i class="ri-eye-line"></i>
                    </router-link>
                    <button class="ti-btn ti-btn-soft-info ti-btn-icon ti-btn-sm">
                      <i class="ri-edit-line"></i>
                    </button>
                    <button class="ti-btn ti-btn-soft-danger ti-btn-icon ti-btn-sm">
                      <i class="ri-delete-bin-line"></i>
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <div class="box-footer flex items-center justify-between">
        <div class="text-textmuted text-sm">
          Showing {{ filteredProjects.length }} of {{ projects.length }} projects
        </div>
        <nav>
          <ul class="ti-pagination mb-0">
            <li class="page-item disabled"><a class="page-link" href="#">Previous</a></li>
            <li class="page-item active"><a class="page-link" href="#">1</a></li>
            <li class="page-item"><a class="page-link" href="#">2</a></li>
            <li class="page-item"><a class="page-link" href="#">3</a></li>
            <li class="page-item"><a class="page-link" href="#">Next</a></li>
          </ul>
        </nav>
      </div>
    </div>
  </div>
</template>

