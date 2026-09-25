<script setup>
import { ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'

const props = defineProps({
  items: { type: Array, required: true },
  projectId: { type: Number, required: true },
  editable: { type: Boolean, default: true }
})

const emit = defineEmits(['itemSelected', 'itemMoved'])

const expandedItems = ref(new Set())
const draggedItem = ref(null)
const hoveredItem = ref(null)

// Build tree structure from flat array
const treeData = computed(() => {
  const itemMap = new Map()
  const roots = []

  // Create map of all items
  props.items.forEach(item => {
    itemMap.set(item.id, { ...item, children: [] })
  })

  // Build tree
  props.items.forEach(item => {
    const node = itemMap.get(item.id)
    if (item.parentId) {
      const parent = itemMap.get(item.parentId)
      if (parent) {
        parent.children.push(node)
      } else {
        roots.push(node)
      }
    } else {
      roots.push(node)
    }
  })

  // Sort by rank
  const sortByRank = (items) => {
    items.sort((a, b) => a.rank - b.rank)
    items.forEach(item => {
      if (item.children.length > 0) {
        sortByRank(item.children)
      }
    })
  }
  sortByRank(roots)

  return roots
})

const toggleExpand = (itemId) => {
  if (expandedItems.value.has(itemId)) {
    expandedItems.value.delete(itemId)
  } else {
    expandedItems.value.add(itemId)
  }
}

const isExpanded = (itemId) => expandedItems.value.has(itemId)

const expandAll = () => {
  props.items.forEach(item => {
    if (item.hasChildren) {
      expandedItems.value.add(item.id)
    }
  })
}

const collapseAll = () => {
  expandedItems.value.clear()
}

// Drag and drop handlers
const handleDragStart = (event, item) => {
  if (!props.editable) return
  draggedItem.value = item
  event.dataTransfer.effectAllowed = 'move'
  event.dataTransfer.setData('text/plain', item.id)
}

const handleDragOver = (event, item) => {
  if (!props.editable || !draggedItem.value) return
  event.preventDefault()
  hoveredItem.value = item
  event.dataTransfer.dropEffect = 'move'
}

const handleDrop = (event, targetItem) => {
  if (!props.editable || !draggedItem.value) return
  event.preventDefault()

  if (draggedItem.value.id === targetItem.id) {
    return
  }

  // Update parent relationship
  router.patch(`/backlog-items/${draggedItem.value.id}/parent`, {
    parent_id: targetItem.id
  }, {
    preserveScroll: true,
    onSuccess: () => {
      emit('itemMoved', draggedItem.value, targetItem)
    }
  })

  draggedItem.value = null
  hoveredItem.value = null
}

const handleDragEnd = () => {
  draggedItem.value = null
  hoveredItem.value = null
}

// Type icons and colors
const getTypeIcon = (type) => {
  const icons = {
    epic: '🎯',
    feature: '📦',
    story: '📝',
    task: '✓',
    bug: '🐛',
    spike: '⚡'
  }
  return icons[type] || '📄'
}

const getTypeColor = (type) => {
  const colors = {
    epic: 'text-purple-600 bg-purple-100',
    feature: 'text-blue-600 bg-blue-100',
    story: 'text-green-600 bg-green-100',
    task: 'text-gray-600 bg-gray-100',
    bug: 'text-red-600 bg-red-100',
    spike: 'text-yellow-600 bg-yellow-100'
  }
  return colors[type] || 'text-gray-600 bg-gray-100'
}

const getPriorityColor = (priority) => {
  const colors = {
    critical: 'text-red-700 bg-red-50 border-red-300',
    high: 'text-orange-700 bg-orange-50 border-orange-300',
    medium: 'text-yellow-700 bg-yellow-50 border-yellow-300',
    low: 'text-blue-700 bg-blue-50 border-blue-300'
  }
  return colors[priority] || colors.medium
}

const addChild = (parentItem) => {
  emit('itemSelected', { action: 'addChild', parent: parentItem })
}

const editItem = (item) => {
  emit('itemSelected', { action: 'edit', item })
}
</script>

<template>
  <div class="backlog-hierarchy-tree">
    <!-- Toolbar -->
    <div class="mb-4 flex items-center justify-between">
      <div class="flex items-center gap-2">
        <button 
          @click="expandAll" 
          class="px-3 py-1 text-sm bg-gray-100 hover:bg-gray-200 rounded transition"
        >
          Expand All
        </button>
        <button 
          @click="collapseAll" 
          class="px-3 py-1 text-sm bg-gray-100 hover:bg-gray-200 rounded transition"
        >
          Collapse All
        </button>
      </div>
      <div class="text-sm text-gray-600">
        {{ items.length }} items total
      </div>
    </div>

    <!-- Tree View -->
    <div class="space-y-1">
      <template v-for="item in treeData" :key="item.id">
        <TreeNode
          :item="item"
          :level="0"
          :expanded="isExpanded(item.id)"
          :dragged="draggedItem?.id === item.id"
          :hovered="hoveredItem?.id === item.id"
          :editable="editable"
          @toggle="toggleExpand"
          @drag-start="handleDragStart"
          @drag-over="handleDragOver"
          @drop="handleDrop"
          @drag-end="handleDragEnd"
          @add-child="addChild"
          @edit="editItem"
        >
          <template #default="{ item: childItem, level: childLevel }">
            <TreeNode
              v-for="child in childItem.children"
              :key="child.id"
              :item="child"
              :level="childLevel + 1"
              :expanded="isExpanded(child.id)"
              :dragged="draggedItem?.id === child.id"
              :hovered="hoveredItem?.id === child.id"
              :editable="editable"
              @toggle="toggleExpand"
              @drag-start="handleDragStart"
              @drag-over="handleDragOver"
              @drop="handleDrop"
              @drag-end="handleDragEnd"
              @add-child="addChild"
              @edit="editItem"
            />
          </template>
        </TreeNode>
      </template>

      <div v-if="items.length === 0" class="text-center py-12 text-gray-500">
        <div class="text-4xl mb-2">📋</div>
        <div class="text-lg font-medium mb-1">No backlog items yet</div>
        <div class="text-sm">Create your first epic or story to get started</div>
      </div>
    </div>
  </div>
</template>

<script>
// TreeNode component
export const TreeNode = {
  name: 'TreeNode',
  props: {
    item: { type: Object, required: true },
    level: { type: Number, required: true },
    expanded: { type: Boolean, default: false },
    dragged: { type: Boolean, default: false },
    hovered: { type: Boolean, default: false },
    editable: { type: Boolean, default: true }
  },
  emits: ['toggle', 'dragStart', 'dragOver', 'drop', 'dragEnd', 'addChild', 'edit'],
  setup(props, { emit, slots }) {
    const hasChildren = computed(() => props.item.children && props.item.children.length > 0)
    
    const getTypeIcon = (type) => {
      const icons = { epic: '🎯', feature: '📦', story: '📝', task: '✓', bug: '🐛', spike: '⚡' }
      return icons[type] || '📄'
    }
    
    const getTypeColor = (type) => {
      const colors = {
        epic: 'text-purple-600 bg-purple-100',
        feature: 'text-blue-600 bg-blue-100',
        story: 'text-green-600 bg-green-100',
        task: 'text-gray-600 bg-gray-100',
        bug: 'text-red-600 bg-red-100',
        spike: 'text-yellow-600 bg-yellow-100'
      }
      return colors[type] || 'text-gray-600 bg-gray-100'
    }

    const getPriorityBadge = (priority) => {
      const badges = {
        critical: { text: 'Critical', class: 'bg-red-100 text-red-700 border-red-300' },
        high: { text: 'High', class: 'bg-orange-100 text-orange-700 border-orange-300' },
        medium: { text: 'Med', class: 'bg-yellow-100 text-yellow-700 border-yellow-300' },
        low: { text: 'Low', class: 'bg-blue-100 text-blue-700 border-blue-300' }
      }
      return badges[priority] || badges.medium
    }

    return {
      hasChildren,
      getTypeIcon,
      getTypeColor,
      getPriorityBadge
    }
  },
  template: `
    <div>
      <!-- Item Row -->
      <div
        :class="[
          'flex items-center gap-2 p-3 rounded-lg border transition-all',
          'hover:border-blue-300 hover:shadow-sm',
          dragged ? 'opacity-50 border-blue-400' : 'border-gray-200',
          hovered ? 'bg-blue-50 border-blue-300' : 'bg-white'
        ]"
        :style="{ marginLeft: level * 24 + 'px' }"
        :draggable="editable"
        @dragstart="$emit('dragStart', $event, item)"
        @dragover="$emit('dragOver', $event, item)"
        @drop="$emit('drop', $event, item)"
        @dragend="$emit('dragEnd')"
      >
        <!-- Expand/Collapse Button -->
        <button
          v-if="hasChildren"
          @click="$emit('toggle', item.id)"
          class="w-6 h-6 flex items-center justify-center text-gray-500 hover:bg-gray-100 rounded"
        >
          <span v-if="expanded">▼</span>
          <span v-else>▶</span>
        </button>
        <div v-else class="w-6"></div>

        <!-- Type Icon -->
        <span class="text-xl">{{ getTypeIcon(item.type) }}</span>

        <!-- Title -->
        <div class="flex-1 min-w-0">
          <div class="font-medium text-gray-900 truncate">{{ item.title }}</div>
        </div>

        <!-- Points Badge -->
        <span v-if="item.points > 0" class="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-700 rounded">
          {{ item.points }} pts
        </span>

        <!-- Priority Badge -->
        <span 
          :class="[
            'px-2 py-1 text-xs font-medium rounded border',
            getPriorityBadge(item.priority).class
          ]"
        >
          {{ getPriorityBadge(item.priority).text }}
        </span>

        <!-- Status Badge -->
        <span 
          :class="[
            'px-2 py-1 text-xs font-medium rounded',
            item.status === 'done' ? 'bg-green-100 text-green-700' :
            item.status === 'in-progress' ? 'bg-blue-100 text-blue-700' :
            'bg-gray-100 text-gray-700'
          ]"
        >
          {{ item.status }}
        </span>

        <!-- Actions -->
        <div v-if="editable" class="flex items-center gap-1">
          <button
            @click="$emit('addChild', item)"
            class="p-1 text-gray-500 hover:bg-gray-100 rounded"
            title="Add child item"
          >
            ➕
          </button>
          <button
            @click="$emit('edit', item)"
            class="p-1 text-gray-500 hover:bg-gray-100 rounded"
            title="Edit item"
          >
            ✏️
          </button>
        </div>
      </div>

      <!-- Children -->
      <div v-if="expanded && hasChildren" class="mt-1 space-y-1">
        <slot :item="item" :level="level" />
      </div>
    </div>
  `
}
</script>

<style scoped>
.backlog-hierarchy-tree {
  font-family: system-ui, -apple-system, sans-serif;
}
</style>
