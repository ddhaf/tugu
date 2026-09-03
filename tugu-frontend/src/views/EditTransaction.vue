<script setup>
import { onMounted, ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import api from '../services/api'
import AppLayout from '../components/AppLayout.vue'
import AlertBanner from '../components/AlertBanner.vue'
import LoadingSkeleton from '../components/LoadingSkeleton.vue'

const route = useRoute()
const router = useRouter()

const type = ref('')
const amount = ref('')
const description = ref('')
const transactionDate = ref('')
const status = ref('')
const transactionNumber = ref('')

const errorMessage = ref('')
const loading = ref(true)
const saving = ref(false)

const getTransaction = async () => {
  try {
    const response = await api.get(`/transactions/${route.params.id}`)
    const transaction = response.data.data

    type.value = transaction.type
    amount.value = transaction.amount
    description.value = transaction.description || ''
    status.value = transaction.status
    transactionNumber.value = transaction.transaction_number

    if (transaction.transaction_date) {
      transactionDate.value = transaction.transaction_date.slice(0, 16)
    }
  } catch (error) {
    console.error(error)
    errorMessage.value =
      error.response?.data?.message || 'Failed to load transaction details.'
  } finally {
    loading.value = false
  }
}

const updateTransaction = async () => {
  errorMessage.value = ''
  saving.value = true

  try {
    await api.put(`/transactions/${route.params.id}`, {
      type: type.value,
      amount: Number(amount.value),
      description: description.value,
      transaction_date: transactionDate.value,
      status: status.value,
    })

    router.push('/transactions')
  } catch (error) {
    console.error(error)
    errorMessage.value =
      error.response?.data?.message ||
      'Failed to update transaction. Please check your inputs.'
  } finally {
    saving.value = false
  }
}

onMounted(() => {
  getTransaction()
})
</script>

<template>
  <AppLayout pageTitle="Edit Transaction">
    <div class="page-header">
      <div>
        <div class="title-with-badge">
          <h1 class="page-title">Edit Transaction</h1>
          <span v-if="transactionNumber" class="tx-badge">#{{ transactionNumber }}</span>
        </div>
        <p class="page-subtitle">Update and manage transaction details</p>
      </div>
    </div>

    <div class="form-container">
      <div class="card form-card">
        <div v-if="loading" class="loading-state">
          <div class="form-row">
            <div class="form-group flex-1">
              <LoadingSkeleton width="100px" height="18px" class="mb-2" />
              <LoadingSkeleton height="42px" />
            </div>
            <div class="form-group flex-2">
              <LoadingSkeleton width="100px" height="18px" class="mb-2" />
              <LoadingSkeleton height="42px" />
            </div>
          </div>
          <div class="form-group">
            <LoadingSkeleton width="100px" height="18px" class="mb-2" />
            <LoadingSkeleton height="42px" />
          </div>
          <div class="form-row">
            <div class="form-group flex-1">
              <LoadingSkeleton width="100px" height="18px" class="mb-2" />
              <LoadingSkeleton height="42px" />
            </div>
            <div class="form-group flex-1">
              <LoadingSkeleton width="100px" height="18px" class="mb-2" />
              <LoadingSkeleton height="42px" />
            </div>
          </div>
        </div>

        <template v-else>
          <AlertBanner
            v-if="errorMessage"
            type="error"
            :message="errorMessage"
            @close="errorMessage = ''"
            class="mb-6"
          />

          <form @submit.prevent="updateTransaction" class="transaction-form">
            <div class="form-row">
              <div class="form-group flex-1">
                <label for="type">Transaction Type</label>
                <select id="type" v-model="type" :disabled="saving">
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
                    :disabled="saving"
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
                placeholder="e.g. Lunch, Salary, Groceries"
                :disabled="saving"
              />
            </div>

            <div class="form-group">
              <label for="status">Status</label>
              <select id="status" v-model="status" :disabled="saving">
                <option value="pending">Pending</option>
                <option value="completed">Completed</option>
                <option value="cancelled">Cancelled</option>
              </select>
            </div>

            <div class="form-group">
              <label for="transactionDate">Date & Time</label>
              <input
                id="transactionDate"
                v-model="transactionDate"
                type="datetime-local"
                required
                :disabled="saving"
              />
            </div>

            <div class="form-actions">
              <button
                type="button"
                class="btn btn-outline"
                @click="router.push('/transactions')"
                :disabled="saving"
              >
                Cancel
              </button>
              <button type="submit" class="btn btn-primary" :disabled="saving">
                <svg
                  v-if="saving"
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
                {{ saving ? 'Saving Changes...' : 'Save Changes' }}
              </button>
            </div>
          </form>
        </template>
      </div>
    </div>
  </AppLayout>
</template>

<style scoped>
.page-header {
  margin-bottom: 24px;
}

.title-with-badge {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-bottom: 4px;
}

.page-title {
  font-size: 1.625rem;
  margin: 0;
  letter-spacing: -0.025em;
}

.tx-badge {
  background: var(--gray-100);
  color: var(--gray-600);
  font-family: monospace;
  font-size: 0.8125rem;
  font-weight: 600;
  padding: 3px 8px;
  border-radius: 6px;
}

.page-subtitle {
  color: var(--gray-500);
  font-size: 0.875rem;
}

.mb-2 {
  margin-bottom: 8px;
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

.transaction-form,
.loading-state {
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