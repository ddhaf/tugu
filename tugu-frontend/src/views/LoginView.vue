<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import api from '../services/api'
import AlertBanner from '../components/AlertBanner.vue'
const loginWithGoogle = () => {
  window.location.href = `${import.meta.env.VITE_OAUTH_BASE_URL}/auth/google/redirect`
}
const loginWithFacebook = () => {
  window.location.href = `${import.meta.env.VITE_OAUTH_BASE_URL}/auth/facebook/redirect`
}

const router = useRouter()

const login = ref('')
const password = ref('')
const errorMessage = ref('')
const loading = ref(false)

const handleLogin = async () => {
  errorMessage.value = ''
  loading.value = true

  try {
    const response = await api.post('/login', {
      login: login.value,
      password: password.value,
    })

    localStorage.setItem('token', response.data.token)

    router.push('/dashboard')
  } catch (error) {
    console.error('LOGIN ERROR:', error)

    errorMessage.value =
      error.response?.data?.message ||
      error.message ||
      'Login failed. Please check your credentials.'
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
        <h1 class="auth-title">Welcome Back</h1>
        <p class="auth-subtitle">Sign in to your Tugu account</p>
      </div>

      <div class="card auth-card">
        <AlertBanner 
          v-if="errorMessage" 
          type="error" 
          :message="errorMessage" 
          @close="errorMessage = ''" 
          class="auth-alert"
        />

        <form @submit.prevent="handleLogin" class="auth-form">
          <div class="form-group">
            <label for="login">Name or Email</label>
            <input
              id="login"
              v-model="login"
              type="text"
              placeholder="Enter your name or email"
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
              placeholder="Enter your password"
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
            {{ loading ? 'Signing in...' : 'Sign In' }}
          </button>

          <div class="oauth-divider">
  <span>or</span>
</div>

<button
  type="button"
  class="btn btn-google btn-block btn-lg"
  @click="loginWithGoogle"
>
  <svg
    width="20"
    height="20"
    viewBox="0 0 24 24"
    fill="none"
  >
    <path
      d="M21.35 12.27c0-.72-.06-1.42-.18-2.09H12v3.95h5.24a4.48 4.48 0 0 1-1.94 2.94v2.45h3.14c1.84-1.69 2.91-4.18 2.91-7.25Z"
      fill="#4285F4"
    />
    <path
      d="M12 21.75c2.63 0 4.83-.87 6.44-2.34l-3.14-2.45c-.87.58-1.98.92-3.3.92-2.54 0-4.69-1.72-5.46-4.03H3.3v2.52A9.72 9.72 0 0 0 12 21.75Z"
      fill="#34A853"
    />
    <path
      d="M6.54 13.85a5.84 5.84 0 0 1 0-3.7V7.63H3.3a9.75 9.75 0 0 0 0 8.74l3.24-2.52Z"
      fill="#FBBC05"
    />
    <path
      d="M12 6.12c1.43 0 2.7.49 3.71 1.45l2.78-2.78C16.83 3.28 14.63 2.25 12 2.25a9.72 9.72 0 0 0-8.7 5.38l3.24 2.52C7.31 7.84 9.46 6.12 12 6.12Z"
      fill="#EA4335"
    />
  </svg>

  Continue with Google
</button>
<button
  type="button"
  class="btn btn-facebook btn-block btn-lg"
  @click="loginWithFacebook"
>
  <svg
    width="20"
    height="20"
    viewBox="0 0 24 24"
    fill="none"
    xmlns="http://www.w3.org/2000/svg"
  >
    <circle cx="12" cy="12" r="10" fill="#1877F2" />
    <path
      d="M13.5 20V13.5H15.7L16 11H13.5V9.4C13.5 8.68 13.74 8.2 14.95 8.2H16V6C15.81 5.97 15.16 5.9 14.4 5.9C12.8 5.9 11.7 6.88 11.7 8.68V11H9.5V13.5H11.7V20H13.5Z"
      fill="white"
    />
  </svg>

  Continue with Facebook
</button>
        </form>
      </div>
      
      <p class="auth-footer">
        Don't have an account?
        <RouterLink to="/register" class="auth-link">Create one now</RouterLink>
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

.oauth-divider {
  display: flex;
  align-items: center;
  gap: 12px;
  margin: 20px 0;
  color: var(--gray-400);
  font-size: 0.875rem;
}

.oauth-divider::before,
.oauth-divider::after {
  content: '';
  flex: 1;
  height: 1px;
  background: var(--gray-200);
}

.btn-google {
  background: #fff;
  color: var(--gray-800);
  border: 1px solid var(--gray-300);
}

.btn-google:hover {
  background: var(--gray-50);
} 

.btn-facebook {
  background: #fff;
  color: var(--gray-900);
  border: 1px solid var(--gray-300);
}

.btn-facebook:hover {
  background: var(--gray-50);
}
</style>