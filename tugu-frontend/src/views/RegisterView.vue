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
  margin: 32px 0;
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

/* Frosted glass card — single backdrop-filter, no GPU promotion */
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

  .auth-container {
    margin: 16px 0;
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