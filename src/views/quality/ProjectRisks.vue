<script setup>
import { ref } from 'vue'
import PageHeader from '@/components/ui/PageHeader.vue'

const risks = ref([
  { id: 1, title: 'Resource shortage', category: 'resource', probability: 'high', impact: 'high', status: 'open', owner: 'John Doe' },
  { id: 2, title: 'Scope creep', category: 'scope', probability: 'medium', impact: 'high', status: 'mitigating', owner: 'Jane Smith' },
  { id: 3, title: 'Technical debt', category: 'technical', probability: 'high', impact: 'medium', status: 'open', owner: 'Mike Johnson' },
  { id: 4, title: 'Vendor delay', category: 'external', probability: 'low', impact: 'high', status: 'closed', owner: 'Sarah Wilson' },
  { id: 5, title: 'Budget overrun', category: 'financial', probability: 'medium', impact: 'high', status: 'mitigating', owner: 'David Brown' }
])

const getRiskLevel = (probability, impact) => {
  if (probability === 'high' && impact === 'high') return { class: 'bg-danger', text: 'Critical' }
  if (probability === 'high' || impact === 'high') return { class: 'bg-warning', text: 'High' }
  if (probability === 'medium' || impact === 'medium') return { class: 'bg-info', text: 'Medium' }
  return { class: 'bg-success', text: 'Low' }
}
</script>

<template>
  <div>
    <PageHeader title="Risks & Issues" subtitle="Track and manage project risks">
      <template #actions>
        <button class="ti-btn ti-btn-primary">
          <i class="ri-add-line me-1"></i> Add Risk
        </button>
      </template>
    </PageHeader>

    <div class="grid grid-cols-12 gap-6">
      <!-- Risk Matrix -->
      <div class="col-span-12 xl:col-span-4">
        <div class="box">
          <div class="box-header">
            <h5 class="box-title">Risk Matrix</h5>
          </div>
          <div class="box-body">
            <div class="grid grid-cols-3 gap-1 text-center text-sm">
              <div class="p-2"></div>
              <div class="p-2 text-textmuted">Low Impact</div>
              <div class="p-2 text-textmuted">High Impact</div>
              <div class="p-2 text-textmuted">High Prob</div>
              <div class="p-3 bg-warning/20 rounded">Medium</div>
              <div class="p-3 bg-danger/20 rounded">Critical</div>
              <div class="p-2 text-textmuted">Low Prob</div>
              <div class="p-3 bg-success/20 rounded">Low</div>
              <div class="p-3 bg-warning/20 rounded">Medium</div>
            </div>
          </div>
        </div>

        <!-- Stats -->
        <div class="box">
          <div class="box-body">
            <div class="space-y-3">
              <div class="flex items-center justify-between">
                <span>Open Risks</span>
                <span class="badge bg-danger/10 text-danger">{{ risks.filter(r => r.status === 'open').length }}</span>
              </div>
              <div class="flex items-center justify-between">
                <span>Mitigating</span>
                <span class="badge bg-warning/10 text-warning">{{ risks.filter(r => r.status === 'mitigating').length }}</span>
              </div>
              <div class="flex items-center justify-between">
                <span>Closed</span>
                <span class="badge bg-success/10 text-success">{{ risks.filter(r => r.status === 'closed').length }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Risk List -->
      <div class="col-span-12 xl:col-span-8">
        <div class="box">
          <div class="box-header">
            <h5 class="box-title">Risk Register</h5>
          </div>
          <div class="box-body p-0">
            <table class="table table-hover whitespace-nowrap">
              <thead>
                <tr>
                  <th>Risk</th>
                  <th>Category</th>
                  <th>Level</th>
                  <th>Status</th>
                  <th>Owner</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="risk in risks" :key="risk.id">
                  <td class="font-medium">{{ risk.title }}</td>
                  <td><span class="badge bg-secondary/10 text-secondary">{{ risk.category }}</span></td>
                  <td>
                    <span class="badge text-white" :class="getRiskLevel(risk.probability, risk.impact).class">
                      {{ getRiskLevel(risk.probability, risk.impact).text }}
                    </span>
                  </td>
                  <td>
                    <span class="badge" :class="{
                      'bg-danger/10 text-danger': risk.status === 'open',
                      'bg-warning/10 text-warning': risk.status === 'mitigating',
                      'bg-success/10 text-success': risk.status === 'closed'
                    }">{{ risk.status }}</span>
                  </td>
                  <td class="text-textmuted">{{ risk.owner }}</td>
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

