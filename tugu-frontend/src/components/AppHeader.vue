<script setup>
import { onMounted, ref } from 'vue'
import { useRouter } from 'vue-router'
import api from '../services/api'

defineProps({
  pageTitle: {
    type: String,
    default: '',
  },
})

const router = useRouter()

const user = ref({
  name: 'User',
  email: '',
})

const loadUser = async () => {
  try {
    const response = await api.get('/me')
    user.value = response.data.user
  } catch (error) {
    console.error('Failed to load current user:', error)
  }
}

const logout = () => {
  localStorage.removeItem('token')
  localStorage.removeItem('user')
  router.push('/login')
}

onMounted(() => {
  loadUser()
})
</script>

<template>
  <header class="app-header">
    <div class="header-content">
      <div class="header-left">
        <h2 v-if="pageTitle" class="header-title">
          {{ pageTitle }}
        </h2>
      </div>

      <div class="header-right">
        <div class="user-profile">
          <div class="avatar" :title="user.name">
            {{ user.name?.charAt(0)?.toUpperCase() || 'U' }}
          </div>

          <div class="user-meta">
            <span class="user-name">{{ user.name }}</span>
            <span v-if="user.email" class="user-email">{{ user.email }}</span>
          </div>
        </div>

        <div class="header-divider" />

        <button
          class="btn-icon-nav"
          @click="logout"
          title="Sign Out"
        >
          <svg
            width="18"
            height="18"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
            stroke-linecap="round"
            stroke-linejoin="round"
          >
            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
            <polyline points="16 17 21 12 16 7" />
            <line x1="21" y1="12" x2="9" y2="12" />
          </svg>
        </button>
      </div>
    </div>
  </header>
</template>

<style scoped>
.app-header {
  height: var(--header-height);
  background: rgba(255, 255, 255, 0.9);
  backdrop-filter: blur(12px);
  -webkit-backdrop-filter: blur(12px);
  border-bottom: 1px solid var(--gray-200);
  position: sticky;
  top: 0;
  z-index: 20;
}

.header-content {
  display: flex;
  align-items: center;
  justify-content: space-between;
  height: 100%;
  padding: 0 28px;
  max-width: 1200px;
  margin: 0 auto;
}

.header-title {
  font-size: 1.125rem;
  font-weight: 700;
  color: var(--gray-900);
  letter-spacing: -0.015em;
}

.header-right {
  display: flex;
  align-items: center;
  gap: 14px;
  margin-left: auto;
}

.user-profile {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 4px 8px 4px 4px;
  border-radius: var(--radius-full);
}

.avatar {
  width: 34px;
  height: 34px;
  background: linear-gradient(135deg, #4f46e5 0%, #0ea5e9 100%);
  color: #fff;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
  font-size: 0.875rem;
  box-shadow: 0 2px 4px rgba(79, 70, 229, 0.2);
}

.user-meta {
  display: none;
  flex-direction: column;
}

.user-name {
  font-size: 0.875rem;
  font-weight: 600;
  color: var(--gray-800);
  line-height: 1.2;
}

.user-email {
  font-size: 0.75rem;
  color: var(--gray-500);
  line-height: 1.2;
}

.header-divider {
  width: 1px;
  height: 22px;
  background: var(--gray-200);
}

.btn-icon-nav {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 34px;
  height: 34px;
  border-radius: var(--radius-md);
  background: transparent;
  color: var(--gray-500);
  border: 1px solid transparent;
  transition: all var(--transition-fast);
}

.btn-icon-nav:hover {
  background: var(--gray-100);
  color: var(--gray-900);
  border-color: var(--gray-200);
}

@media (min-width: 768px) {
  .user-meta {
    display: flex;
  }
}
</style>