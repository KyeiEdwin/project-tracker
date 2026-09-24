<script setup>
import { computed, nextTick, onMounted, onUnmounted, ref, watch } from 'vue'
import { router, useForm, usePage } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import Echo, { isRealtimeEnabled } from '@/realtime/echo'

defineOptions({ layout: AppLayout })

const props = defineProps({
  member: { type: Object, required: true },
  csrfToken: { type: String, required: true },
  team: { type: Object, default: null },
  teamMembers: { type: Array, default: () => [] },
  messages: { type: Array, default: () => [] },
})

const page = usePage()
const logoutForm = useForm({})
const logout = () => logoutForm.post('/team-member/logout')
const chatMessages = ref([...props.messages])
const messageBody = ref('')
const sendingMessage = ref(false)
const chatError = ref('')
const onlineMemberIds = ref(new Set())
const messagesContainer = ref(null)
const realtimeChannels = []
let refreshTimer = null

watch(() => props.messages, (messages) => {
  chatMessages.value = [...messages]
}, { deep: true })

const refreshChat = () => {
  router.reload({
    only: ['teamMembers', 'messages'],
    preserveScroll: true,
  })
}

const startPolling = () => {
  if (!refreshTimer) refreshTimer = window.setInterval(refreshChat, 20000)
}

const appendMessage = (message) => {
  if (!message || chatMessages.value.some((item) => item.id === message.id)) return

  chatMessages.value.push(message)
  nextTick(() => {
    if (messagesContainer.value) {
      messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight
    }
  })
}

const subscribeToTeamChat = () => {
  if (!isRealtimeEnabled || !Echo || !props.team?.id) return false

  const teamChannel = `team.${props.team.id}`
  const presenceChannel = `team-presence.${props.team.id}`

  Echo.private(teamChannel)
    .listen('.chat.message.created', (event) => appendMessage(event.message))
    .error(startPolling)

  Echo.join(presenceChannel)
    .here((members) => {
      onlineMemberIds.value = new Set(members.map((teamMember) => teamMember.id))
    })
    .joining((teamMember) => {
      onlineMemberIds.value = new Set([...onlineMemberIds.value, teamMember.id])
    })
    .leaving((teamMember) => {
      const ids = new Set(onlineMemberIds.value)
      ids.delete(teamMember.id)
      onlineMemberIds.value = ids
    })
    .error(startPolling)

  realtimeChannels.push(teamChannel, presenceChannel)
  return true
}

onMounted(() => {
  if (!subscribeToTeamChat()) startPolling()
})

onUnmounted(() => {
  realtimeChannels.forEach((channel) => Echo?.leave(channel))
  realtimeChannels.length = 0

  if (refreshTimer) {
    window.clearInterval(refreshTimer)
    refreshTimer = null
  }
})

const isMemberOnline = (memberId) => onlineMemberIds.value.has(memberId)

const formatMessageTime = (timestamp) => {
  if (!timestamp) return ''

  return new Intl.DateTimeFormat(undefined, {
    hour: 'numeric',
    minute: '2-digit',
  }).format(new Date(timestamp))
}

const cookieValue = (name) => {
  const cookie = document.cookie
    .split('; ')
    .find((value) => value.startsWith(`${name}=`))

  return cookie ? decodeURIComponent(cookie.substring(name.length + 1)) : ''
}

const sendMessage = async () => {
  const body = messageBody.value.trim()
  if (!body || sendingMessage.value || !props.team) return

  sendingMessage.value = true
  chatError.value = ''

  try {
    const csrfToken = props.csrfToken
      || cookieValue('XSRF-TOKEN')
      || document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
    const response = await fetch('/team-member/chat/messages', {
      method: 'POST',
      credentials: 'same-origin',
      headers: {
        Accept: 'application/json',
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrfToken || '',
        'X-Requested-With': 'XMLHttpRequest',
      },
      body: JSON.stringify({ body }),
    })

    const data = await response.json()
    if (!response.ok) throw new Error(data.message || 'Message could not be sent.')

    appendMessage(data.message)
    messageBody.value = ''
  } catch (error) {
    chatError.value = error.message || 'Message could not be sent.'
  } finally {
    sendingMessage.value = false
  }
}
</script>

<template>
  <div>
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between mb-6">
      <div>
        <p class="text-sm text-textmuted mb-1">Team communication</p>
        <h1 class="text-2xl font-semibold">{{ team?.name || 'Team chat' }}</h1>
        <p class="text-textmuted mt-1">Talk with members of your team in real time.</p>
      </div>
      <button type="button" class="ti-btn ti-btn-light" :disabled="logoutForm.processing" @click="logout">
        <i class="ri-logout-box-r-line me-1"></i> Sign out
      </button>
    </div>

    <div v-if="!team" class="box">
      <div class="box-body">
        <p class="text-textmuted">You are not assigned to a team, so team chat is unavailable.</p>
      </div>
    </div>

    <div v-else class="box">
      <div class="box-header flex items-center justify-between gap-4">
        <div>
          <h2 class="box-title">Team chat</h2>
          <p class="text-textmuted text-sm mt-1">{{ team.name }} conversation</p>
        </div>
        <span class="badge bg-success/10 text-success">{{ onlineMemberIds.size }} online</span>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-[minmax(0,1fr)_16rem]">
        <div class="border-e border-defaultborder">
          <div ref="messagesContainer" class="h-[32rem] overflow-y-auto p-5 space-y-4">
            <p v-if="!chatMessages.length" class="text-textmuted">No messages yet. Start the conversation.</p>
            <div v-for="message in chatMessages" :key="message.id" class="flex gap-3">
              <span class="w-9 h-9 shrink-0 rounded-full flex items-center justify-center bg-primary/10 text-primary font-semibold">
                {{ message.sender?.name?.slice(0, 1) || '?' }}
              </span>
              <div class="min-w-0">
                <div class="flex flex-wrap items-baseline gap-x-2 gap-y-1">
                  <p class="font-medium">{{ message.sender?.name || 'Team member' }}</p>
                  <time class="text-textmuted text-xs" :datetime="message.createdAt">{{ formatMessageTime(message.createdAt) }}</time>
                </div>
                <p class="text-textmuted break-words whitespace-pre-wrap">{{ message.body }}</p>
              </div>
            </div>
          </div>

          <form class="border-t border-defaultborder p-4 flex gap-3" @submit.prevent="sendMessage">
            <input
              v-model="messageBody"
              type="text"
              maxlength="2000"
              class="form-control"
              placeholder="Write a message..."
              :disabled="sendingMessage"
            >
            <button type="submit" class="ti-btn ti-btn-primary shrink-0" :disabled="sendingMessage || !messageBody.trim()">
              <i class="ri-send-plane-line me-1"></i> Send
            </button>
          </form>
          <p v-if="chatError" class="px-4 pb-4 text-danger text-sm">{{ chatError }}</p>
        </div>

        <div class="p-5">
          <h3 class="font-medium mb-3">Team members</h3>
          <div class="space-y-3">
            <div v-for="teamMember in teamMembers" :key="teamMember.id" class="flex items-center gap-3">
              <span class="w-2 h-2 rounded-full" :class="isMemberOnline(teamMember.id) ? 'bg-success' : 'bg-textmuted'"></span>
              <div class="min-w-0">
                <p class="font-medium truncate">{{ teamMember.name }}</p>
                <p class="text-textmuted text-xs capitalize">{{ teamMember.role }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
