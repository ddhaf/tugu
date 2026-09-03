<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import api from '../services/api'
import AlertBanner from '../components/AlertBanner.vue'

const router = useRouter()

const name = ref('')
const email = ref('')
const password = ref('')
const passwordConfirmation = ref('')
const errorMessage = ref('')
const loading = ref(false)

const handleRegister = async () => {
  errorMessage.value = ''
  
  if (password.value !== passwordConfirmation.value) {
    errorMessage.value = 'Passwords do not match.'
    return
  }
  
  loading.value = true

  try {
    const response = await api.post('/register', {
      name: name.value,
      email: email.value,
      password: password.value,
      password_confirmation: passwordConfirmation.value,
    })

    localStorage.setItem('token', response.data.token)

    router.push('/dashboard')
  } catch (error) {
    console.error('REGISTER ERROR:', error)

    errorMessage.value =
      error.response?.data?.message ||
      'Registration failed. Please check your inputs.'
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <div class="auth-layout">
    <div class="auth-container">
      <div class="auth-header">
        <div class="brand-logo">
          <img src="/tugu-logo.png" alt="Tugu Logo" class="custom-logo-lg" />
        </div>
        <h1 class="auth-title">Create Account</h1>
        <p class="auth-subtitle">Join Tugu to manage your transactions</p>
      </div>

      <div class="card auth-card">
        <AlertBanner 
          v-if="errorMessage" 
          type="error" 
          :message="errorMessage" 
          @close="errorMessage = ''" 
          class="auth-alert"
        />

        <form @submit.prevent="handleRegister" class="auth-form">
          <div class="form-group">
            <label for="name">Name</label>
            <input
              id="name"
              v-model="name"
              type="text"
              placeholder="Enter your full name"
              required
              :disabled="loading"
            />
          </div>

          <div class="form-group">
            <label for="email">Email</label>
            <input
              id="email"
              v-model="email"
              type="email"
              placeholder="Enter your email address"
              required
              :disabled="loading"
            />
          </div>

          <div class="form-group">
            <label for="password">Password</label>
            <input
              id="password"
              v-model="password"
              type="password"
              placeholder="Create a strong password"
              required
              :disabled="loading"
            />
          </div>

          <div class="form-group">
            <label for="passwordConfirmation">Confirm Password</label>
            <input
              id="passwordConfirmation"
              v-model="passwordConfirmation"
              type="password"
              placeholder="Confirm your password"
              required
              :disabled="loading"
            />
          </div>

          <button type="submit" class="btn btn-primary btn-block btn-lg" :disabled="loading">
            <svg v-if="loading" class="spinner" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <line x1="12" y1="2" x2="12" y2="6"></line>
              <line x1="12" y1="18" x2="12" y2="22"></line>
              <line x1="4.93" y1="4.93" x2="7.76" y2="7.76"></line>
              <line x1="16.24" y1="16.24" x2="19.07" y2="19.07"></line>
              <line x1="2" y1="12" x2="6" y2="12"></line>
              <line x1="18" y1="12" x2="22" y2="12"></line>
              <line x1="4.93" y1="19.07" x2="7.76" y2="16.24"></line>
              <line x1="16.24" y1="7.76" x2="19.07" y2="4.93"></line>
            </svg>
            {{ loading ? 'Creating account...' : 'Create Account' }}
          </button>
        </form>
      </div>

      <p class="auth-footer">
        Already have an account?
        <RouterLink to="/login" class="auth-link">Sign in instead</RouterLink>
      </p>
    </div>
  </div>
</template>

<style scoped>
.auth-layout {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  padding: 24px;
  background: var(--gray-50);
}

.auth-container {
  width: 100%;
  max-width: 420px;
  animation: slideUp 0.5s ease;
  margin: 32px 0;
}

.auth-header {
  text-align: center;
  margin-bottom: 32px;
}

.brand-logo {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 20px;
}

.custom-logo-lg {
  height: 64px;
  width: auto;
  object-fit: contain;
}

.auth-title {
  font-size: 1.75rem;
  font-weight: 700;
  color: var(--gray-900);
  margin-bottom: 8px;
  letter-spacing: -0.02em;
}

.auth-subtitle {
  color: var(--gray-500);
  font-size: 1rem;
}

.auth-card {
  padding: 32px;
  margin-bottom: 24px;
}

.auth-alert {
  margin-bottom: 24px;
}

.auth-form {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.form-group {
  display: flex;
  flex-direction: column;
}

.auth-footer {
  text-align: center;
  color: var(--gray-600);
  font-size: 0.9375rem;
}

.auth-link {
  font-weight: 600;
  color: var(--primary-600);
}

.auth-link:hover {
  color: var(--primary-700);
  text-decoration: underline;
}

.spinner {
  animation: spin 0.8s linear infinite;
}

@keyframes spin {
  from { transform: rotate(0deg); }
  to { transform: rotate(360deg); }
}
</style>