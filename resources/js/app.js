import { createApp, h } from 'vue'
import { createInertiaApp, Link } from '@inertiajs/vue3'
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers'
import { createPinia } from 'pinia'
import AppLayout from './Layouts/AppLayout.vue'
import './realtime/echo'

// Import Preline for dropdowns and interactive components
import './preline.js'

createInertiaApp({
  title: (title) => (title ? `${title} - Project Tracker` : 'Project Tracker'),
  resolve: async (name) => {
    const page = await resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue'))
    page.default.layout = page.default.layout || AppLayout
    return page
  },
  setup({ el, App, props, plugin }) {
    const app = createApp({ render: () => h(App, props) })
    app.use(plugin)
    app.use(createPinia())
    app.component('Link', Link)
    app.component('router-link', Link)
    app.mount(el)
  },
  progress: {
    color: '#5c67f7',
  },
})
