<template>
  <div class="project-progress-indicator">
    <!-- Progress Bar -->
    <div class="progress-header">
      <span class="progress-label">{{ label }}</span>
      <span class="progress-value">{{ progress }}%</span>
    </div>
    
    <div class="progress-bar-container">
      <div 
        class="progress-bar-fill" 
        :class="progressClass"
        :style="{ width: `${progress}%` }"
      >
      </div>
    </div>

    <!-- Detailed Breakdown (Optional) -->
    <div v-if="showBreakdown && breakdown" class="progress-breakdown">
      <div class="breakdown-header">
        <h4>Progress Breakdown</h4>
        <button 
          v-if="showRecalculate" 
          @click="recalculateProgress" 
          class="btn-recalculate"
          :disabled="recalculating"
        >
          <svg v-if="!recalculating" class="icon" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd" />
          </svg>
          <svg v-else class="icon animate-spin" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          {{ recalculating ? 'Recalculating...' : 'Recalculate' }}
        </button>
      </div>

      <!-- Task Statistics -->
      <div class="breakdown-section">
        <h5>Tasks</h5>
        <div class="breakdown-grid">
          <div class="breakdown-item">
            <span class="item-label">Total</span>
            <span class="item-value">{{ breakdown.total }}</span>
          </div>
          <div class="breakdown-item success">
            <span class="item-label">Completed</span>
            <span class="item-value">{{ breakdown.completed }}</span>
          </div>
          <div class="breakdown-item warning">
            <span class="item-label">In Progress</span>
            <span class="item-value">{{ breakdown.inProgress }}</span>
          </div>
          <div class="breakdown-item muted">
            <span class="item-label">Not Started</span>
            <span class="item-value">{{ breakdown.notStarted }}</span>
          </div>
        </div>
      </div>

      <!-- Hours Statistics (if available) -->
      <div v-if="breakdown.totalEstimatedHours > 0" class="breakdown-section">
        <h5>Estimated Hours</h5>
        <div class="breakdown-grid">
          <div class="breakdown-item">
            <span class="item-label">Total</span>
            <span class="item-value">{{ breakdown.totalEstimatedHours }}h</span>
          </div>
          <div class="breakdown-item success">
            <span class="item-label">Completed</span>
            <span class="item-value">{{ breakdown.completedEstimatedHours }}h</span>
          </div>
          <div class="breakdown-item muted">
            <span class="item-label">Remaining</span>
            <span class="item-value">{{ breakdown.remainingEstimatedHours }}h</span>
          </div>
        </div>
      </div>

      <!-- Milestones (if available) -->
      <div v-if="breakdown.milestones && breakdown.milestones.total > 0" class="breakdown-section">
        <h5>Milestones</h5>
        <div class="breakdown-grid">
          <div class="breakdown-item">
            <span class="item-label">Total</span>
            <span class="item-value">{{ breakdown.milestones.total }}</span>
          </div>
          <div class="breakdown-item success">
            <span class="item-label">Completed</span>
            <span class="item-value">{{ breakdown.milestones.completed }}</span>
          </div>
        </div>
      </div>

      <!-- Estimated Completion -->
      <div v-if="estimatedCompletion" class="breakdown-section">
        <h5>Estimated Completion</h5>
        <p class="completion-date">{{ formattedCompletionDate }}</p>
      </div>

      <!-- Calculation Strategy -->
      <div v-if="calculationStrategy" class="breakdown-section">
        <h5>Calculation Method</h5>
        <p class="strategy-description">{{ calculationStrategy }}</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, ref } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
  projectId: {
    type: Number,
    required: true
  },
  progress: {
    type: Number,
    required: true,
    default: 0
  },
  label: {
    type: String,
    default: 'Project Progress'
  },
  showBreakdown: {
    type: Boolean,
    default: false
  },
  showRecalculate: {
    type: Boolean,
    default: false
  },
  breakdown: {
    type: Object,
    default: null
  },
  estimatedCompletion: {
    type: String,
    default: null
  },
  calculationStrategy: {
    type: String,
    default: null
  }
});

const emit = defineEmits(['progress-updated']);

const recalculating = ref(false);

const progressClass = computed(() => {
  if (props.progress >= 75) return 'bg-success';
  if (props.progress >= 50) return 'bg-info';
  if (props.progress >= 25) return 'bg-warning';
  return 'bg-danger';
});

const formattedCompletionDate = computed(() => {
  if (!props.estimatedCompletion) return 'N/A';
  try {
    const date = new Date(props.estimatedCompletion);
    return date.toLocaleDateString('en-US', { 
      year: 'numeric', 
      month: 'long', 
      day: 'numeric' 
    });
  } catch (e) {
    return props.estimatedCompletion;
  }
});

const recalculateProgress = async () => {
  if (recalculating.value) return;
  
  recalculating.value = true;
  
  try {
    const response = await fetch(`/projects/${props.projectId}/recalculate-progress`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
      }
    });

    if (!response.ok) {
      throw new Error('Failed to recalculate progress');
    }

    const data = await response.json();
    
    // Emit event with updated data
    emit('progress-updated', {
      oldProgress: data.old_progress,
      newProgress: data.new_progress,
      breakdown: data.breakdown
    });

    // Reload the page to show updated data
    router.reload({ only: ['project', 'dashboard'] });
  } catch (error) {
    console.error('Error recalculating progress:', error);
    alert('Failed to recalculate progress. Please try again.');
  } finally {
    recalculating.value = false;
  }
};
</script>

<style scoped>
.project-progress-indicator {
  width: 100%;
}

.progress-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 0.5rem;
  font-size: 0.875rem;
}

.progress-label {
  font-weight: 500;
  color: #374151;
}

.progress-value {
  font-weight: 600;
  color: #1f2937;
}

.progress-bar-container {
  width: 100%;
  height: 1.5rem;
  background-color: #e5e7eb;
  border-radius: 0.5rem;
  overflow: hidden;
  position: relative;
}

.progress-bar-fill {
  height: 100%;
  transition: width 0.3s ease, background-color 0.3s ease;
  border-radius: 0.5rem;
}

.progress-bar-fill.bg-success {
  background-color: #10b981;
}

.progress-bar-fill.bg-info {
  background-color: #3b82f6;
}

.progress-bar-fill.bg-warning {
  background-color: #f59e0b;
}

.progress-bar-fill.bg-danger {
  background-color: #ef4444;
}

.progress-breakdown {
  margin-top: 1.5rem;
  padding: 1rem;
  background-color: #f9fafb;
  border-radius: 0.5rem;
  border: 1px solid #e5e7eb;
}

.breakdown-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1rem;
}

.breakdown-header h4 {
  font-size: 1rem;
  font-weight: 600;
  color: #1f2937;
  margin: 0;
}

.btn-recalculate {
  display: flex;
  align-items: center;
  gap: 0.375rem;
  padding: 0.375rem 0.75rem;
  font-size: 0.875rem;
  font-weight: 500;
  color: #3b82f6;
  background-color: white;
  border: 1px solid #3b82f6;
  border-radius: 0.375rem;
  cursor: pointer;
  transition: all 0.2s;
}

.btn-recalculate:hover:not(:disabled) {
  background-color: #3b82f6;
  color: white;
}

.btn-recalculate:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.btn-recalculate .icon {
  width: 1rem;
  height: 1rem;
}

.animate-spin {
  animation: spin 1s linear infinite;
}

@keyframes spin {
  from { transform: rotate(0deg); }
  to { transform: rotate(360deg); }
}

.breakdown-section {
  margin-bottom: 1.25rem;
}

.breakdown-section:last-child {
  margin-bottom: 0;
}

.breakdown-section h5 {
  font-size: 0.875rem;
  font-weight: 600;
  color: #6b7280;
  text-transform: uppercase;
  letter-spacing: 0.025em;
  margin: 0 0 0.75rem 0;
}

.breakdown-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
  gap: 0.75rem;
}

.breakdown-item {
  display: flex;
  flex-direction: column;
  padding: 0.75rem;
  background-color: white;
  border-radius: 0.375rem;
  border: 1px solid #e5e7eb;
}

.breakdown-item.success {
  border-color: #10b981;
  background-color: #f0fdf4;
}

.breakdown-item.warning {
  border-color: #f59e0b;
  background-color: #fffbeb;
}

.breakdown-item.muted {
  border-color: #d1d5db;
  background-color: #f9fafb;
}

.item-label {
  font-size: 0.75rem;
  color: #6b7280;
  margin-bottom: 0.25rem;
}

.item-value {
  font-size: 1.25rem;
  font-weight: 600;
  color: #1f2937;
}

.completion-date,
.strategy-description {
  font-size: 0.875rem;
  color: #4b5563;
  margin: 0;
  padding: 0.5rem;
  background-color: white;
  border-radius: 0.375rem;
  border: 1px solid #e5e7eb;
}

@media (max-width: 640px) {
  .breakdown-grid {
    grid-template-columns: repeat(2, 1fr);
  }
  
  .breakdown-header {
    flex-direction: column;
    align-items: flex-start;
    gap: 0.75rem;
  }
  
  .btn-recalculate {
    width: 100%;
    justify-content: center;
  }
}
</style>
