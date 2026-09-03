<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import api from '../services/api'
import AppLayout from '../components/AppLayout.vue'
import AlertBanner from '../components/AlertBanner.vue'

const router = useRouter()

const type = ref('expense')
const amount = ref('')
const description = ref('')
const transactionDate = ref('')
const errorMessage = ref('')
const loading = ref(false)

// Set default datetime to now
const now = new Date()
now.setMinutes(now.getMinutes() - now.getTimezoneOffset())
transactionDate.value = now.toISOString().slice(0, 16)

const createTransaction = async () => {
  errorMessage.value = ''
  loading.value = true

  try {
    await api.post('/transactions', {
      type: type.value,
      amount: Number(amount.value),
      description: description.value,
      transaction_date: transactionDate.value,
    })

    router.push('/transactions')
  } catch (error) {
    console.error(error)
    errorMessage.value =
      error.response?.data?.message ||
      'Failed to create transaction. Please check your inputs.'
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <AppLayout pageTitle="New Transaction">
    <div class="page-header">
      <div>
        <h1 class="page-title">New Transaction</h1>
        <p class="page-subtitle">Record a new income or expense transaction</p>
      </div>
    </div>

    <div class="form-container">
      <div class="card form-card">
        <AlertBanner
          v-if="errorMessage"
          type="error"
          :message="errorMessage"
          @close="errorMessage = ''"
          class="mb-6"
        />

        <form @submit.prevent="createTransaction" class="transaction-form">
          <div class="form-row">
            <div class="form-group flex-1">
              <label for="type">Transaction Type</label>
              <select id="type" v-model="type" :disabled="loading">
                <option value="expense">Expense (-)</option>
                <option value="income">Income (+)</option>
              </select>
            </div>

            <div class="form-group flex-2">
              <label for="amount">Amount (Rp)</label>
              <div class="amount-input-wrapper">
                <span class="currency-prefix">Rp</span>
                <input
                  id="amount"
                  v-model="amount"
                  type="number"
                  min="1"
                  placeholder="0"
                  required
                  :disabled="loading"
                  class="amount-input"
                />
              </div>
            </div>
          </div>

          <div class="form-group">
            <label for="description">Description (Optional)</label>
            <input
              id="description"
              v-model="description"
              type="text"
              placeholder="e.g. Lunch, Monthly Salary, Groceries"
              :disabled="loading"
            />
          </div>

          <div class="form-group">
            <label for="transactionDate">Date & Time</label>
            <input
              id="transactionDate"
              v-model="transactionDate"
              type="datetime-local"
              required
              :disabled="loading"
            />
          </div>

          <div class="form-actions">
            <button
              type="button"
              class="btn btn-outline"
              @click="$router.push('/transactions')"
              :disabled="loading"
            >
              Cancel
            </button>
            <button type="submit" class="btn btn-primary" :disabled="loading">
              <svg
                v-if="loading"
                class="spinner"
                width="16"
                height="16"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2.5"
                stroke-linecap="round"
                stroke-linejoin="round"
              >
                <circle cx="12" cy="12" r="10" opacity="0.25"></circle>
                <path d="M12 2a10 10 0 0 1 10 10"></path>
              </svg>
              {{ loading ? 'Saving...' : 'Save Transaction' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </AppLayout>
</template>

<style scoped>
.page-header {
  margin-bottom: 24px;
}

.page-title {
  font-size: 1.625rem;
  margin-bottom: 4px;
  letter-spacing: -0.025em;
}

.page-subtitle {
  color: var(--gray-500);
  font-size: 0.875rem;
}

.mb-6 {
  margin-bottom: 24px;
}

.form-container {
  max-width: 620px;
}

.form-card {
  padding: 32px;
  border-radius: var(--radius-xl);
}

.transaction-form {
  display: flex;
  flex-direction: column;
  gap: 22px;
}

.form-row {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

@media (min-width: 640px) {
  .form-row {
    flex-direction: row;
  }
}

.form-group {
  display: flex;
  flex-direction: column;
}

.flex-1 {
  flex: 1;
}
.flex-2 {
  flex: 2;
}

.amount-input-wrapper {
  position: relative;
  display: flex;
  align-items: center;
}

.currency-prefix {
  position: absolute;
  left: 14px;
  color: var(--gray-500);
  font-weight: 600;
  font-size: 0.875rem;
  pointer-events: none;
}

.amount-input {
  padding-left: 42px;
  font-weight: 600;
  font-size: 1rem;
}

.form-actions {
  display: flex;
  justify-content: flex-end;
  gap: 12px;
  margin-top: 8px;
  padding-top: 24px;
  border-top: 1px solid var(--gray-100);
}
</style>