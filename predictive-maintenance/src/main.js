import { createApp } from 'vue';
import App from './App.vue';
import router from './router';
import Vue3Toastify from 'vue3-toastify';
import 'vue3-toastify/dist/index.css';
import '@fortawesome/fontawesome-free/css/all.css';
import 'bootstrap/dist/js/bootstrap.min.js';
import 'bootstrap/dist/css/bootstrap.min.css';
import './assets/main.css';

const app = createApp(App);

app.use(router);

// Set default toast options (optional)
const options = {
  autoClose: 5000, // 5 seconds auto-close
  position: 'top-right', // Customize the toast position
};

app.use(Vue3Toastify, options);

app.mount('#app');
