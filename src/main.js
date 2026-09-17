import { createApp } from 'vue'
import { createPinia } from 'pinia'
import App from './App.vue'
import router from './router'

// Import global styles
import './assets/css/styles.css'
import './assets/css/pm-custom.css'

// Import Preline for dropdowns and interactive components
import './assets/js/preline.js'

const app = createApp(App)

app.use(createPinia())
app.use(router)

app.mount('#app')

