<script setup>
import { onMounted, ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import api from '../services/api'
import AppLayout from '../components/AppLayout.vue'
import StatusBadge from '../components/StatusBadge.vue'
import LoadingSkeleton from '../components/LoadingSkeleton.vue'
import EmptyState from '../components/EmptyState.vue'
import AlertBanner from '../components/AlertBanner.vue'
import { formatRupiah, formatDate } from '../utils/formatters'

const router = useRouter()

const transactions = ref([])
const errorMessage = ref('')
const loading = ref(true)

const user = ref({
  name: 'User',
  email: '',
})

const loadUserFromToken = () => {
  const token = localStorage.getItem('token')
  if (!token) return

  try {
    const payload = token.split('.')[1]
    const base64 = payload.replace(/-/g, '+').replace(/_/g, '/')
    const jsonPayload = decodeURIComponent(
      atob(base64)
        .split('')
        .map((char) => '%' + ('00' + char.charCodeAt(0).toString(16)).slice(-2))
        .join(''),
    )
    const data = JSON.parse(jsonPayload)
    user.value = {
      name: data.name || 'User',
      email: data.email || '',
    }
  } catch (error) {
    console.error('Failed to read JWT:', error)
  }
}

const greeting = computed(() => {
  const hour = new Date().getHours()
  if (hour < 12) return 'Good morning'
  if (hour < 18) return 'Good afternoon'
  return 'Good evening'
})

const currentDateStr = computed(() => {
  return new Intl.DateTimeFormat('en-US', {
    weekday: 'short',
    month: 'short',
    day: 'numeric',
    year: 'numeric',
  }).format(new Date())
})

const getTransactions = async () => {
  try {
    const response = await api.get('/transactions')
    transactions.value = response.data.data
  } catch (error) {
    console.error(error)
    errorMessage.value =
      error.response?.data?.message || 'Failed to load transactions data'
  } finally {
    loading.value = false
  }
}

// Summary computations
const totalIncome = computed(() => {
  return transactions.value
    .filter((t) => t.type === 'income' && t.status !== 'cancelled')
    .reduce((sum, t) => sum + Number(t.amount), 0)
})

const totalExpense = computed(() => {
  return transactions.value
    .filter((t) => t.type === 'expense' && t.status !== 'cancelled')
    .reduce((sum, t) => sum + Number(t.amount), 0)
})

const netBalance = computed(() => totalIncome.value - totalExpense.value)

const recentTransactions = computed(() => {
  return [...transactions.value]
    .sort((a, b) => new Date(b.transaction_date) - new Date(a.transaction_date))
    .slice(0, 5)
})

onMounted(() => {
  loadUserFromToken()
  getTransactions()
})
</script>

<template>
  <AppLayout pageTitle="Dashboard">
    <!-- Greeting & Quick Action Header -->
    <div class="dashboard-header">
      <div class="greeting-info">
        <div class="date-badge">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
            <line x1="16" y1="2" x2="16" y2="6"/>
            <line x1="8" y1="2" x2="8" y2="6"/>
            <line x1="3" y1="10" x2="21" y2="10"/>
          </svg>
          <span>{{ currentDateStr }}</span>
        </div>
        <h1 class="greeting-title">{{ greeting }}, {{ user.name }}</h1>
        <p class="greeting-subtitle">Here is a quick overview of your latest financial activity.</p>
      </div>

      <div class="header-action">
        <button class="btn btn-primary" @click="router.push('/transactions/create')">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <line x1="12" y1="5" x2="12" y2="19"/>
            <line x1="5" y1="12" x2="19" y2="12"/>
          </svg>
          <span>New Transaction</span>
        </button>
      </div>
    </div>

    <AlertBanner
      v-if="errorMessage"
      type="error"
      :message="errorMessage"
      @close="errorMessage = ''"
      class="mb-6"
    />

    <!-- Summary Metric Cards -->
    <div class="summary-grid">
      <!-- Total Income -->
      <div class="card summary-card card-income">
        <div class="summary-icon income-icon">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/>
            <polyline points="17 6 23 6 23 12"/>
          </svg>
        </div>
        <div class="summary-details">
          <span class="summary-label">TOTAL INCOME</span>
          <div class="summary-value income-val">
            <LoadingSkeleton v-if="loading" width="130px" height="28px" />
            <span v-else>{{ formatRupiah(totalIncome) }}</span>
          </div>
        </div>
      </div>

      <!-- Total Expense -->
      <div class="card summary-card card-expense">
        <div class="summary-icon expense-icon">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="23 18 13.5 8.5 8.5 13.5 1 6"/>
            <polyline points="17 18 23 18 23 12"/>
          </svg>
        </div>
        <div class="summary-details">
          <span class="summary-label">TOTAL EXPENSE</span>
          <div class="summary-value expense-val">
            <LoadingSkeleton v-if="loading" width="130px" height="28px" />
            <span v-else>{{ formatRupiah(totalExpense) }}</span>
          </div>
        </div>
      </div>

      <!-- Net Balance -->
      <div class="card summary-card card-balance">
        <div class="summary-icon balance-icon">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
            <rect x="2" y="4" width="20" height="16" rx="2" ry="2"/>
            <line x1="2" y1="10" x2="22" y2="10"/>
          </svg>
        </div>
        <div class="summary-details">
          <span class="summary-label">NET BALANCE</span>
          <div
            class="summary-value"
            :class="netBalance < 0 ? 'expense-val' : 'balance-val'"
          >
            <LoadingSkeleton v-if="loading" width="130px" height="28px" />
            <span v-else>{{ formatRupiah(netBalance) }}</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Recent Transactions -->
    <div class="card recent-card">
      <div class="recent-header">
        <div class="recent-title-wrap">
          <h3>Recent Transactions</h3>
          <span v-if="!loading && transactions.length > 0" class="count-pill">
            {{ recentTransactions.length }} of {{ transactions.length }}
          </span>
        </div>
        <RouterLink to="/transactions" class="view-all-link">
          <span>View all</span>
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="9 18 15 12 9 6"/>
          </svg>
        </RouterLink>
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="recent-list">
        <div v-for="i in 4" :key="i" class="recent-row">
          <div class="recent-left">
            <LoadingSkeleton width="40px" height="40px" radius="10px" />
            <div>
              <LoadingSkeleton width="150px" height="16px" style="margin-bottom: 6px" />
              <LoadingSkeleton width="90px" height="13px" />
            </div>
          </div>
          <div class="recent-right">
            <LoadingSkeleton width="90px" height="18px" style="margin-bottom: 4px" />
            <LoadingSkeleton width="65px" height="20px" radius="10px" />
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <EmptyState
        v-else-if="transactions.length === 0"
        title="No transactions yet"
        message="Start tracking your finances by creating your first transaction."
      >
        <template #action>
          <button class="btn btn-primary" @click="router.push('/transactions/create')">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
              <line x1="12" y1="5" x2="12" y2="19"/>
              <line x1="5" y1="12" x2="19" y2="12"/>
            </svg>
            <span>Create Transaction</span>
          </button>
        </template>
      </EmptyState>

      <!-- Data List -->
      <div v-else class="recent-list">
        <div
          v-for="tx in recentTransactions"
          :key="tx.id"
          class="recent-row"
          @click="router.push(`/transactions/${tx.id}/edit`)"
        >
          <div class="recent-left">
            <div
              class="tx-type-icon"
              :class="tx.type === 'income' ? 'icon-income' : 'icon-expense'"
            >
              <svg
                v-if="tx.type === 'income'"
                width="18"
                height="18"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2.5"
                stroke-linecap="round"
                stroke-linejoin="round"
              >
                <polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/>
                <polyline points="17 6 23 6 23 12"/>
              </svg>
              <svg
                v-else
                width="18"
                height="18"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2.5"
                stroke-linecap="round"
                stroke-linejoin="round"
              >
                <polyline points="23 18 13.5 8.5 8.5 13.5 1 6"/>
                <polyline points="17 18 23 18 23 12"/>
              </svg>
            </div>

            <div class="recent-info">
              <span class="recent-desc">{{ tx.description || 'Untitled Transaction' }}</span>
              <span class="recent-date">{{ formatDate(tx.transaction_date) }}</span>
            </div>
          </div>

          <div class="recent-right">
            <span
              class="recent-amount"
              :class="tx.type === 'income' ? 'income-val' : 'expense-val'"
            >
              {{ tx.type === 'income' ? '+' : '-' }}{{ formatRupiah(tx.amount) }}
            </span>
            <StatusBadge :status="tx.status" />
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<style scoped>
.dashboard-header {
  display: flex;
  flex-direction: column;
  gap: 16px;
  margin-bottom: 24px;
}

@media (min-width: 768px) {
  .dashboard-header {
    flex-direction: row;
    align-items: flex-end;
    justify-content: space-between;
  }
}

.greeting-info {
  display: flex;
  flex-direction: column;
}

.date-badge {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  font-size: 0.75rem;
  font-weight: 600;
  color: var(--gray-500);
  margin-bottom: 6px;
}

.greeting-title {
  font-size: 1.625rem;
  font-weight: 700;
  color: var(--gray-900);
  letter-spacing: -0.025em;
  margin-bottom: 4px;
}

.greeting-subtitle {
  font-size: 0.875rem;
  color: var(--gray-500);
}

.mb-6 {
  margin-bottom: 24px;
}

/* Summary Grid */
.summary-grid {
  display: grid;
  grid-template-columns: 1fr;
  gap: 16px;
  margin-bottom: 24px;
}

@media (min-width: 768px) {
  .summary-grid {
    grid-template-columns: repeat(3, 1fr);
  }
}

.summary-card {
  display: flex;
  align-items: center;
  padding: 22px 24px;
  gap: 18px;
  border-radius: var(--radius-xl);
  transition: transform var(--transition-base), box-shadow var(--transition-base), border-color var(--transition-base);
}

.summary-card:hover {
  transform: translateY(-2px);
  box-shadow: var(--shadow-md);
  border-color: var(--gray-300);
}

.summary-icon {
  width: 50px;
  height: 50px;
  border-radius: var(--radius-lg);
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.income-icon {
  background: #ecfdf5;
  color: #10b981;
  border: 1px solid #d1fae5;
}

.expense-icon {
  background: #fff1f2;
  color: #f43f5e;
  border: 1px solid #ffe4e6;
}

.balance-icon {
  background: #eef2ff;
  color: #4f46e5;
  border: 1px solid #e0e7ff;
}

.summary-details {
  flex: 1;
  min-width: 0;
}

.summary-label {
  font-size: 0.6875rem;
  font-weight: 700;
  letter-spacing: 0.06em;
  color: var(--gray-500);
  margin-bottom: 4px;
  display: block;
}

.summary-value {
  font-size: 1.4375rem;
  font-weight: 700;
  letter-spacing: -0.02em;
  font-variant-numeric: tabular-nums;
  line-height: 1.2;
}

.income-val {
  color: #047857;
}

.expense-val {
  color: #be123c;
}

.balance-val {
  color: #1e293b;
}

/* Recent Transactions */
.recent-card {
  border-radius: var(--radius-xl);
  overflow: hidden;
}

.recent-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 18px 24px;
  border-bottom: 1px solid var(--gray-200);
}

.recent-title-wrap {
  display: flex;
  align-items: center;
  gap: 10px;
}

.recent-title-wrap h3 {
  font-size: 1rem;
  font-weight: 700;
  color: var(--gray-900);
}

.count-pill {
  font-size: 0.6875rem;
  font-weight: 600;
  color: var(--gray-500);
  background: var(--gray-100);
  padding: 2px 8px;
  border-radius: var(--radius-full);
}

.view-all-link {
  display: inline-flex;
  align-items: center;
  gap: 4px;
  font-size: 0.8125rem;
  font-weight: 600;
  color: var(--accent-600);
  transition: all var(--transition-fast);
}

.view-all-link:hover {
  color: var(--accent-700);
  transform: translateX(2px);
}

.recent-list {
  display: flex;
  flex-direction: column;
}

.recent-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 16px 24px;
  border-bottom: 1px solid var(--gray-100);
  cursor: pointer;
  transition: background-color var(--transition-fast);
  gap: 16px;
}

.recent-row:last-child {
  border-bottom: none;
}

.recent-row:hover {
  background-color: #f8fafc;
}

.recent-left {
  display: flex;
  align-items: center;
  gap: 14px;
  min-width: 0;
  flex: 1;
}

.tx-type-icon {
  width: 40px;
  height: 40px;
  border-radius: var(--radius-md);
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.icon-income {
  background: #ecfdf5;
  color: #10b981;
}

.icon-expense {
  background: #fff1f2;
  color: #f43f5e;
}

.recent-info {
  display: flex;
  flex-direction: column;
  min-width: 0;
}

.recent-desc {
  font-size: 0.875rem;
  font-weight: 600;
  color: var(--gray-900);
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.recent-date {
  font-size: 0.75rem;
  color: var(--gray-500);
  margin-top: 2px;
}

.recent-right {
  display: flex;
  flex-direction: column;
  align-items: flex-end;
  gap: 4px;
  flex-shrink: 0;
}

.recent-amount {
  font-size: 0.9375rem;
  font-weight: 700;
  font-variant-numeric: tabular-nums;
  white-space: nowrap;
}
</style>
