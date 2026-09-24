<script setup>
defineProps({
  title: {
    type: String,
    default: ''
  },
  subtitle: {
    type: String,
    default: ''
  },
  noPadding: {
    type: Boolean,
    default: false
  },
  hover: {
    type: Boolean,
    default: true
  }
})
</script>

<template>
  <div class="box" :class="{ 'hover-effect': hover }">
    <!-- Header slot or title -->
    <div v-if="$slots.header || title" class="box-header">
      <slot name="header">
        <div>
          <h3 class="box-title">{{ title }}</h3>
          <p v-if="subtitle" class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ subtitle }}</p>
        </div>
      </slot>
      <slot name="actions"></slot>
    </div>
    
    <!-- Body -->
    <div class="box-body" :class="{ 'p-0': noPadding }">
      <slot></slot>
    </div>
    
    <!-- Footer slot -->
    <div v-if="$slots.footer" class="box-footer">
      <slot name="footer"></slot>
    </div>
  </div>
</template>

<style scoped>
.box-footer {
  padding: var(--spacing-lg) var(--spacing-xl);
  border-top: 1px solid rgba(0, 0, 0, 0.06);
  background-color: rgba(0, 0, 0, 0.01);
}

.dark .box-footer {
  border-top-color: rgba(255, 255, 255, 0.08);
  background-color: rgba(255, 255, 255, 0.02);
}

.hover-effect {
  cursor: default;
}
</style>
