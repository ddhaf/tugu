import { createRouter, createWebHistory } from 'vue-router'
import LoginView from '../views/LoginView.vue'
import RegisterView from '../views/RegisterView.vue'
import DashboardView from '../views/DashboardView.vue'
import TransactionsView from '../views/TransactionsView.vue'
import CreateTransaction from '../views/CreateTransaction.vue'
import EditTransaction from '../views/EditTransaction.vue'
import GoogleCallbackView from '../views/GoogleCallbackView.vue'
import FacebookCallbackView from '../views/FacebookCallbackView.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),

  routes: [
    {
      path: '/',
      redirect: '/dashboard',
    },
    {
      path: '/login',
      name: 'login',
      component: LoginView,
    },
    {
      path: '/register',
      name: 'register',
      component: RegisterView,
    },
    {
      path: '/dashboard',
      name: 'dashboard',
      component: DashboardView,
      meta: { requiresAuth: true },
    },
    {
      path: '/transactions',
      name: 'transactions',
      component: TransactionsView,
      meta: { requiresAuth: true },
    },
    {
      path: '/transactions/create',
      name: 'create-transaction',
      component: CreateTransaction,
      meta: { requiresAuth: true },
    },
    {
      path: '/transactions/:id/edit',
      name: 'edit-transaction',
      component: EditTransaction,
      meta: { requiresAuth: true },
    },
    {
  path: '/oauth/google/callback',
  name: 'google-callback',
  component: GoogleCallbackView,
    },
    {
  path: '/oauth/facebook/callback',
  name: 'facebook-callback',
  component: FacebookCallbackView,
}
  ],
})

router.beforeEach((to) => {
  const token = localStorage.getItem('token')

  if (to.meta.requiresAuth && !token) {
    return '/login'
  }

  if (to.path === '/login' && token) {
    return '/dashboard'
  }
})

export default router