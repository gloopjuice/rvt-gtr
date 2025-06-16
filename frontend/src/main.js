import './assets/main.css'

import { createApp } from 'vue'
import App from './App.vue'
import router from './router'
import header from './components/Header.vue'

import axios from 'axios';

// Update this line to use your server's IP
axios.defaults.baseURL = 'http://64.227.105.115';
axios.defaults.withCredentials = true;  // Important for sessions/cookies

const app = createApp(App)

app.use(router)

app.mount('#app')
