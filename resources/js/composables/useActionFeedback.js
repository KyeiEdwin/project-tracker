export const useActionFeedback = () => {
  const notify = (message) => window.dispatchEvent(new CustomEvent('workspace-action', { detail: { message } }))
  return { notify }
}
