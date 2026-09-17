<script setup>
import { ref, nextTick, onMounted } from 'vue'
import PageHeader from '@/components/ui/PageHeader.vue'

const channels = ref([
  { id: 1, name: 'general', unread: 2 },
  { id: 2, name: 'website-redesign', unread: 5 },
  { id: 3, name: 'mobile-app', unread: 0 },
  { id: 4, name: 'random', unread: 1 }
])

const activeChannel = ref('general')

const messages = ref([
  { id: 1, user: 'John Doe', avatar: 'JD', message: 'Hey team, the new designs are ready for review!', time: '10:30 AM', isMe: false },
  { id: 2, user: 'Jane Smith', avatar: 'JS', message: 'Great! I\'ll take a look this afternoon.', time: '10:32 AM', isMe: false },
  { id: 3, user: 'You', avatar: 'ME', message: 'Perfect, let me know if you have any feedback.', time: '10:35 AM', isMe: true },
  { id: 4, user: 'Mike Johnson', avatar: 'MJ', message: 'The API endpoints are also ready for integration. Check the docs in #mobile-app channel.', time: '10:45 AM', isMe: false },
  { id: 5, user: 'You', avatar: 'ME', message: 'Thanks Mike! Will start integration tomorrow.', time: '10:50 AM', isMe: true }
])

const newMessage = ref('')
const chatContainer = ref(null)

const sendMessage = () => {
  if (!newMessage.value.trim()) return
  
  messages.value.push({
    id: messages.value.length + 1,
    user: 'You',
    avatar: 'ME',
    message: newMessage.value,
    time: new Date().toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' }),
    isMe: true
  })
  newMessage.value = ''
  
  nextTick(() => {
    if (chatContainer.value) {
      chatContainer.value.scrollTop = chatContainer.value.scrollHeight
    }
  })
}
</script>

<template>
  <div>
    <PageHeader title="Project Chat" subtitle="Team communication">
      <template #actions>
        <button class="ti-btn ti-btn-light">
          <i class="ri-phone-line me-1"></i> Voice Call
        </button>
        <button class="ti-btn ti-btn-primary">
          <i class="ri-vidicon-line me-1"></i> Video Call
        </button>
      </template>
    </PageHeader>

    <div class="grid grid-cols-12 gap-6">
      <!-- Channels Sidebar -->
      <div class="col-span-12 xl:col-span-3">
        <div class="box">
          <div class="box-header">
            <div class="flex items-center justify-between">
              <h5 class="box-title">Channels</h5>
              <button class="ti-btn ti-btn-sm ti-btn-soft-primary ti-btn-icon"><i class="ri-add-line"></i></button>
            </div>
          </div>
          <div class="box-body p-0">
            <ul class="list-group list-group-flush">
              <li 
                v-for="channel in channels" 
                :key="channel.id"
                class="list-group-item flex items-center justify-between cursor-pointer hover:bg-light"
                :class="{ 'bg-primary/10': activeChannel === channel.name }"
                @click="activeChannel = channel.name"
              >
                <span class="flex items-center gap-2">
                  <i class="ri-hashtag"></i>
                  {{ channel.name }}
                </span>
                <span v-if="channel.unread > 0" class="badge bg-primary rounded-full">{{ channel.unread }}</span>
              </li>
            </ul>
          </div>
        </div>

        <!-- Team Members -->
        <div class="box">
          <div class="box-header">
            <h5 class="box-title">Team Online</h5>
          </div>
          <div class="box-body p-0">
            <ul class="list-group list-group-flush">
              <li class="list-group-item flex items-center gap-2">
                <span class="avatar avatar-xs bg-primary text-white">JD</span>
                <span>John Doe</span>
                <span class="ms-auto w-2 h-2 bg-success rounded-full"></span>
              </li>
              <li class="list-group-item flex items-center gap-2">
                <span class="avatar avatar-xs bg-info text-white">JS</span>
                <span>Jane Smith</span>
                <span class="ms-auto w-2 h-2 bg-success rounded-full"></span>
              </li>
              <li class="list-group-item flex items-center gap-2">
                <span class="avatar avatar-xs bg-warning text-white">MJ</span>
                <span>Mike Johnson</span>
                <span class="ms-auto w-2 h-2 bg-gray-300 rounded-full"></span>
              </li>
            </ul>
          </div>
        </div>
      </div>

      <!-- Chat Area -->
      <div class="col-span-12 xl:col-span-9">
        <div class="box h-[600px] flex flex-col">
          <div class="box-header border-b">
            <div class="flex items-center gap-2">
              <i class="ri-hashtag text-lg"></i>
              <h5 class="box-title mb-0">{{ activeChannel }}</h5>
            </div>
          </div>
          
          <!-- Messages -->
          <div ref="chatContainer" class="box-body flex-1 overflow-y-auto space-y-4">
            <div 
              v-for="msg in messages" 
              :key="msg.id"
              class="flex gap-3"
              :class="{ 'flex-row-reverse': msg.isMe }"
            >
              <span class="avatar avatar-sm flex-shrink-0" :class="msg.isMe ? 'bg-primary text-white' : 'bg-light text-defaulttextcolor'">
                {{ msg.avatar }}
              </span>
              <div :class="{ 'text-right': msg.isMe }">
                <div class="flex items-center gap-2 mb-1" :class="{ 'flex-row-reverse': msg.isMe }">
                  <span class="font-medium text-sm">{{ msg.user }}</span>
                  <span class="text-xs text-textmuted">{{ msg.time }}</span>
                </div>
                <div 
                  class="inline-block p-3 rounded-lg max-w-md"
                  :class="msg.isMe ? 'bg-primary text-white' : 'bg-light'"
                >
                  {{ msg.message }}
                </div>
              </div>
            </div>
          </div>

          <!-- Input -->
          <div class="box-footer border-t">
            <form @submit.prevent="sendMessage" class="flex gap-2">
              <input 
                v-model="newMessage"
                type="text" 
                class="ti-form-control" 
                placeholder="Type a message..."
              >
              <button type="button" class="ti-btn ti-btn-light ti-btn-icon"><i class="ri-attachment-line"></i></button>
              <button type="button" class="ti-btn ti-btn-light ti-btn-icon"><i class="ri-emotion-line"></i></button>
              <button type="submit" class="ti-btn ti-btn-primary ti-btn-icon"><i class="ri-send-plane-fill"></i></button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

