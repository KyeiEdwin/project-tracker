import { onMounted, onUnmounted, ref, unref } from 'vue'
import { router } from '@inertiajs/vue3'
import Echo, { isRealtimeEnabled } from '@/realtime/echo'

export function useProjectRealtime(projectId, options = {}) {
  const connected = ref(false)
  const polling = ref(false)
  let channel = null
  let pollTimer = null
  let visibilityHandler = null

  const refresh = () => {
    if (polling.value) return

    polling.value = true
    router.reload({
      only: options.only || ['project', 'tasks', 'stats', 'columns'],
      preserveScroll: true,
      onFinish: () => {
        polling.value = false
      },
    })
  }

  const startPolling = () => {
    if (pollTimer || typeof document === 'undefined') return

    pollTimer = window.setInterval(() => {
      if (!document.hidden) refresh()
    }, options.pollInterval || 20000)
  }

  const stopPolling = () => {
    if (!pollTimer) return

    window.clearInterval(pollTimer)
    pollTimer = null
  }

  const subscribe = () => {
    const id = unref(projectId)

    if (!id || !Echo) {
      startPolling()
      return
    }

    channel = Echo.private(`project.${id}`)
      .listen('.task.updated', (event) => options.onTaskUpdated?.(event))
      .listen('.project.progress.updated', (event) => options.onProgressUpdated?.(event))

    channel.subscribed(() => {
      connected.value = true
      stopPolling()
    })
    channel.error(() => {
      connected.value = false
      startPolling()
    })
  }

  onMounted(() => {
    subscribe()
    visibilityHandler = () => {
      if (document.hidden) stopPolling()
      else if (!connected.value) {
        startPolling()
        refresh()
      }
    }
    document.addEventListener('visibilitychange', visibilityHandler)
  })

  onUnmounted(() => {
    stopPolling()
    if (visibilityHandler) document.removeEventListener('visibilitychange', visibilityHandler)
    if (channel && Echo) Echo.leave(`project.${unref(projectId)}`)
  })

  if (isRealtimeEnabled) {
    connected.value = Boolean(Echo)
  }

  return { connected, polling, refresh }
}