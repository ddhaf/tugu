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
  width: 100%;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  padding: 56px 20px 48px;
  position: relative;
  overflow-x: hidden;
  background: 
    radial-gradient(ellipse 120% 80% at 50% 0%, rgba(186, 230, 253, 0.55) 0%, transparent 65%),
    linear-gradient(180deg, #daeffe 0%, #eaf4fd 30%, #f8fafc 60%, #e8f0fe 100%);
}

/* Single lightweight accent ring — no box-shadow blur, GPU-friendly */
.auth-layout::before {
  content: '';
  position: absolute;
  top: 50%;
  left: 50%;
  width: 620px;
  height: 620px;
  transform: translate3d(-50%, -50%, 0);
  border-radius: 50%;
  border: 1px solid rgba(255, 255, 255, 0.6);
  pointer-events: none;
  z-index: 0;
}

.auth-container {
  width: 100%;
  max-width: 440px;
  position: relative;
  z-index: 1;
  animation: slideUp 0.6s cubic-bezier(0.16, 1, 0.3, 1);
}

.auth-header {
  text-align: center;
  margin-bottom: 26px;
}

/* Logo badge — no backdrop-filter, clean opaque surface */
.brand-logo {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 18px;
  padding: 10px 22px;
  background: rgba(255, 255, 255, 0.9);
  border: 1px solid rgba(255, 255, 255, 0.95);
  border-radius: 20px;
  box-shadow: 
    0 2px 12px rgba(15, 23, 42, 0.06),
    inset 0 1px 1px #ffffff;
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.brand-logo:hover {
  transform: translateY(-2px);
  box-shadow: 
    0 6px 18px rgba(15, 23, 42, 0.09),
    inset 0 1px 1px #ffffff;
}

.custom-logo-lg {
  height: 46px;
  width: auto;
  object-fit: contain;
}

.auth-title {
  font-size: 1.875rem;
  font-weight: 700;
  color: #0f172a;
  margin-bottom: 6px;
  letter-spacing: -0.025em;
}

.auth-subtitle {
  color: #475569;
  font-size: 0.95rem;
  font-weight: 400;
  letter-spacing: -0.01em;
}

/* Frosted glass card — single backdrop-filter */
.auth-card {
  position: relative;
  background: linear-gradient(145deg, rgba(255, 255, 255, 0.78) 0%, rgba(255, 255, 255, 0.56) 100%);
  -webkit-backdrop-filter: blur(14px) saturate(160%);
  backdrop-filter: blur(14px) saturate(160%);
  border: 1px solid rgba(255, 255, 255, 0.88);
  border-radius: 28px;
  box-shadow: 
    0 20px 40px -10px rgba(15, 23, 42, 0.09),
    inset 0 1.5px 1px rgba(255, 255, 255, 0.95),
    inset 0 -1px 1px rgba(255, 255, 255, 0.3);
  padding: 36px 32px;
  margin-bottom: 24px;
}

.auth-alert {
  margin-bottom: 22px;
  border-radius: 14px;
  /* no backdrop-filter — avoids nested blur pass */
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

.form-group label {
  font-size: 0.8125rem;
  font-weight: 600;
  color: #334155;
  margin-bottom: 7px;
  letter-spacing: 0.01em;
}

.auth-form input {
  width: 100%;
  padding: 12px 16px;
  font-family: inherit;
  font-size: 0.9375rem;
  color: #0f172a;
  background: rgba(248, 250, 252, 0.75);
  border: 1px solid rgba(226, 232, 240, 0.9);
  border-radius: 14px;
  outline: none;
  box-shadow: inset 0 1px 2px rgba(15, 23, 42, 0.03);
  transition: border-color 0.18s ease, box-shadow 0.18s ease, background 0.18s ease;
}

.auth-form input:hover {
  background: rgba(255, 255, 255, 0.92);
  border-color: rgba(203, 213, 225, 0.95);
}

.auth-form input:focus {
  background: #ffffff;
  border-color: #6366f1;
  box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.12);
}

.auth-form input::placeholder {
  color: #94a3b8;
  font-size: 0.875rem;
}

.btn-primary {
  background: linear-gradient(180deg, #1e293b 0%, #0f172a 100%);
  color: #ffffff;
  border: 1px solid rgba(255, 255, 255, 0.1);
  border-radius: 14px;
  padding: 13px 24px;
  font-size: 0.95rem;
  font-weight: 600;
  letter-spacing: -0.01em;
  box-shadow: 
    0 4px 12px rgba(15, 23, 42, 0.16),
    inset 0 1px 1px rgba(255, 255, 255, 0.18);
  transition: background 0.18s ease, box-shadow 0.18s ease, transform 0.18s ease;
}

.btn-primary:hover:not(:disabled) {
  background: linear-gradient(180deg, #334155 0%, #1e293b 100%);
  box-shadow: 
    0 8px 20px rgba(15, 23, 42, 0.22),
    inset 0 1px 1px rgba(255, 255, 255, 0.22);
  transform: translateY(-1px);
}

.btn-primary:active:not(:disabled) {
  transform: translateY(0);
  box-shadow: 0 2px 6px rgba(15, 23, 42, 0.16);
}

.spinner {
  animation: spin 0.8s linear infinite;
}

@keyframes spin {
  from { transform: rotate(0deg); }
  to { transform: rotate(360deg); }
}

@keyframes slideUp {
  from {
    opacity: 0;
    transform: translateY(14px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.oauth-divider {
  display: flex;
  align-items: center;
  gap: 14px;
  margin: 22px 0 20px;
  color: #94a3b8;
  font-size: 0.8125rem;
  font-weight: 500;
  text-transform: lowercase;
}

.oauth-divider::before,
.oauth-divider::after {
  content: '';
  flex: 1;
  height: 1px;
  background: linear-gradient(90deg, transparent, rgba(203, 213, 225, 0.7), transparent);
}

/* SSO buttons — no backdrop-filter, solid semi-transparent surface */
.btn-google,
.btn-facebook {
  background: rgba(255, 255, 255, 0.88);
  border: 1px solid rgba(226, 232, 240, 0.9);
  border-radius: 14px;
  padding: 12px 20px;
  font-size: 0.9rem;
  font-weight: 600;
  color: #1e293b;
  box-shadow: 
    0 1px 4px rgba(15, 23, 42, 0.04),
    inset 0 1px 1px rgba(255, 255, 255, 0.95);
  transition: background 0.18s ease, box-shadow 0.18s ease, transform 0.18s ease;
}

.btn-google:hover,
.btn-facebook:hover {
  background: rgba(255, 255, 255, 0.98);
  border-color: rgba(203, 213, 225, 0.95);
  box-shadow: 
    0 4px 14px rgba(15, 23, 42, 0.07),
    inset 0 1px 1px #ffffff;
  transform: translateY(-1px);
}

.btn-google:active,
.btn-facebook:active {
  transform: translateY(0);
}

.auth-footer {
  text-align: center;
  color: #475569;
  font-size: 0.9375rem;
  font-weight: 500;
}

.auth-link {
  font-weight: 600;
  color: #4f46e5;
  margin-left: 4px;
  transition: color 0.15s ease;
}

.auth-link:hover {
  color: #3730a3;
  text-decoration: underline;
}

@media (max-width: 480px) {
  .auth-layout {
    padding: 32px 16px 28px;
  }
  
  .auth-card {
    padding: 26px 20px;
    border-radius: 22px;
  }
  
  .auth-title {
    font-size: 1.625rem;
  }
}
</style>