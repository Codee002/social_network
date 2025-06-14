import { createApp } from 'vue'
import App from './App.vue'

// Pusher Laravel
import Echo from 'laravel-echo'
import Pusher from 'pusher-js'

window.Pusher = Pusher

window.Echo = new Echo({
  broadcaster: 'pusher',
  key: process.env.VUE_APP_PUSHER_APP_KEY,
  cluster: 'ap1',
  forceTLS: (process.env.VUE_APP_PUSHER_APP_SCHEME ?? 'https') === 'https',
  enabledTransports: ['ws', 'wss'],
  authEndpoint: 'http://127.0.0.1:8000/broadcasting/auth',
  auth: {
    headers: {
      Authorization: `Bearer ${localStorage.getItem('auth_token')}`,
    },
  },
})

// Ant Design Vue
import Antd from 'ant-design-vue'
import 'ant-design-vue/dist/reset.css'

// Vue Toastification
import Toast from 'vue-toastification'
import 'vue-toastification/dist/index.css'
const options = {
  transition: 'Vue-Toastification__bounce',
  maxToasts: 20,
  newestOnTop: true,
}

// Axios
import axios from 'axios'
window.axios = axios
axios.defaults.headers.common['Authorization'] = `Bearer ${localStorage.getItem('auth_token')}`
axios.defaults.baseURL = process.env.VUE_APP_DOMAIN_API;

// Router
import router from './router/index'

// CSS
import './assets/styles/global.css'

const app = createApp(App)
app.use(Toast, options)
app.use(router)
app.use(Antd)
app.mount('#app')
