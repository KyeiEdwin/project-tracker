/**
 * Composable for consistent progress bar color logic across the application
 */
export function useProgressColor() {
  /**
   * Get the appropriate color class for a progress value
   * @param {number} progress - Progress value (0-100)
   * @returns {string} - Tailwind/Bootstrap color class
   */
  const getProgressColorClass = (progress) => {
    if (progress >= 75) return 'bg-success'
    if (progress >= 50) return 'bg-info'
    if (progress >= 25) return 'bg-warning'
    return 'bg-danger'
  }

  /**
   * Get the text color class for a progress value
   * @param {number} progress - Progress value (0-100)
   * @returns {string} - Tailwind/Bootstrap text color class
   */
  const getProgressTextClass = (progress) => {
    if (progress >= 75) return 'text-success'
    if (progress >= 50) return 'text-info'
    if (progress >= 25) return 'text-warning'
    return 'text-danger'
  }

  /**
   * Get a descriptive status based on progress
   * @param {number} progress - Progress value (0-100)
   * @returns {string} - Status description
   */
  const getProgressStatus = (progress) => {
    if (progress >= 75) return 'Excellent'
    if (progress >= 50) return 'Good'
    if (progress >= 25) return 'In Progress'
    return 'Getting Started'
  }

  /**
   * Get progress color for different contexts (primary color variants)
   * @param {number} progress - Progress value (0-100)
   * @returns {object} - Color variants
   */
  const getProgressColors = (progress) => {
    const colors = {
      '0-24': {
        bar: 'bg-danger',
        text: 'text-danger',
        badge: 'bg-danger/10 text-danger',
        border: 'border-danger',
      },
      '25-49': {
        bar: 'bg-warning',
        text: 'text-warning',
        badge: 'bg-warning/10 text-warning',
        border: 'border-warning',
      },
      '50-74': {
        bar: 'bg-info',
        text: 'text-info',
        badge: 'bg-info/10 text-info',
        border: 'border-info',
      },
      '75-100': {
        bar: 'bg-success',
        text: 'text-success',
        badge: 'bg-success/10 text-success',
        border: 'border-success',
      },
    }

    if (progress >= 75) return colors['75-100']
    if (progress >= 50) return colors['50-74']
    if (progress >= 25) return colors['25-49']
    return colors['0-24']
  }

  return {
    getProgressColorClass,
    getProgressTextClass,
    getProgressStatus,
    getProgressColors,
  }
}
