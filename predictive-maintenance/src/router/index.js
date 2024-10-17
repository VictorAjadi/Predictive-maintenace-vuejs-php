import { createRouter, createWebHistory } from 'vue-router';
import TemperatureChart from '@/components/TemperatureChart.vue';
import HumidityChart from '@/components/HumidityChart.vue';
import Weight from '@/components/Weight.vue';
import SignUp from '@/components/SignUp.vue';
import SignIn from '@/components/SignIn.vue';
import { isAuthenticated } from '@/services/auth';  // Import the auth check function

const routes = [
  {
    path: '/',
    name: 'sign-in',
    component: SignIn,
  },
  {
    path: '/sign-up',
    name: 'sign-up',
    component: SignUp,
  },
  {
    path: '/sign-in',
    name: 'sign-in',
    component: SignIn,
  },
  {
    path: '/temperature',
    name: 'temperature',
    component: TemperatureChart,
    meta: { requiresAuth: true }  // Requires authentication
  },
  {
    path: '/humidity',
    name: 'humidity',
    component: HumidityChart,
    meta: { requiresAuth: true }  // Requires authentication
  },
  {
    path: '/weight',
    name: 'weight',
    component: Weight,
    meta: { requiresAuth: true }  // Requires authentication
  }
];

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes
});

// Global Navigation Guard
router.beforeEach(async (to, from, next) => {
  // Check if the route requires authentication
  const auth=await isAuthenticated()
  if (to.meta.requiresAuth && !auth) {
    // Redirect to the sign-in page if not authenticated
    next({ name: 'sign-in' });
  } else {
    // Proceed to the route
    next();
  }
});

export default router;
