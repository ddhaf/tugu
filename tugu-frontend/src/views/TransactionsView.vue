<script setup>
import { onMounted, ref, computed } from 'vue'
import api from '../services/api'
import AppLayout from '../components/AppLayout.vue'
import StatusBadge from '../components/StatusBadge.vue'
import TypeBadge from '../components/TypeBadge.vue'
import LoadingSkeleton from '../components/LoadingSkeleton.vue'
import EmptyState from '../components/EmptyState.vue'
import AlertBanner from '../components/AlertBanner.vue'
import DeleteModal from '../components/DeleteModal.vue'
import { formatRupiah, formatDate } from '../utils/formatters'

const transactions = ref([])
const errorMessage = ref('')
const loading = ref(true)

// Filters
const searchQuery = ref('')
const filterType = ref('')
const filterStatus = ref('')

// Delete Modal
const showDeleteModal = ref(false)
const transactionToDelete = ref(null)
const isDeleting = ref(false)

const getTransactions = async () => {
  try {
    const response = await api.get('/transactions')
    transactions.value = response.data.data
  } catch (error) {
    console.error(error)
    errorMessage.value =
      error.response?.data?.message || 'Failed to load transactions'
  } finally {
    loading.value = false
  }
}

const filteredTransactions = computed(() => {
  let result = transactions.value

  // Search
  if (searchQuery.value.trim()) {
    const q = searchQuery.value.toLowerCase()
    result = result.filter(
      (t) =>
        (t.description || '').toLowerCase().includes(q) ||
        (t.transaction_number || '').toLowerCase().includes(q),
    )
  }

  // Type filter
  if (filterType.value) {
    result = result.filter((t) => t.type === filterType.value)
  }

  // Status filter
  if (filterStatus.value) {
    result = result.filter((t) => t.status === filterStatus.value)
  }

  return result
})

const confirmDelete = (transaction) => {
  transactionToDelete.value = transaction
  showDeleteModal.value = true
}

const deleteTransaction = async () => {
  if (!transactionToDelete.value) return
  isDeleting.value = true
  const id = transactionToDelete.value.id

  try {
    await api.delete(`/transactions/${id}`)
    transactions.value = transactions.value.filter((t) => t.id !== id)
    showDeleteModal.value = false
    transactionToDelete.value = null
  } catch (error) {
    console.error(error)
    errorMessage.value =
      error.response?.data?.message || 'Failed to delete transaction'
  } finally {
    isDeleting.value = false
  }
}

const clearFilters = () => {
  searchQuery.value = ''
  filterType.value = ''
  filterStatus.value = ''
}

const hasActiveFilters = computed(() => {
  return Boolean(searchQuery.value || filterType.value || filterStatus.value)
})

onMounted(() => {
  getTransactions()
})
</script>

<template>
  <AppLayout pageTitle="Transactions">
    <!-- Page Header -->
    <div class="page-header">
      <div>
        <h1 class="page-title">Transactions</h1>
        <p class="page-subtitle">Manage and keep track of your financial records</p>
      </div>

      <button
        class="btn btn-primary"
        @click="$router.push('/transactions/create')"
      >
        <svg
          width="18"
          height="18"
          viewBox="0 0 24 24"
          fill="none"
          stroke="currentColor"
          stroke-width="2.5"
          stroke-linecap="round"
          stroke-linejoin="round"
        >
          <line x1="12" y1="5" x2="12" y2="19" />
          <line x1="5" y1="12" x2="19" y2="12" />
        </svg>
        <span>New Transaction</span>
      </button>
    </div>

    <AlertBanner
      v-if="errorMessage"
      type="error"
      :message="errorMessage"
      @close="errorMessage = ''"
      class="mb-6"
    />

    <!-- Filter Bar -->
    <div class="card filter-bar">
      <div class="filter-search">
        <svg
          class="search-icon"
          width="17"
          height="17"
          viewBox="0 0 24 24"
          fill="none"
          stroke="currentColor"
          stroke-width="2"
          stroke-linecap="round"
          stroke-linejoin="round"
        >
          <circle cx="11" cy="11" r="8" />
          <line x1="21" y1="21" x2="16.65" y2="16.65" />
        </svg>
        <input
          v-model="searchQuery"
          type="text"
          placeholder="Search description or #ID..."
          class="search-input"
        />
      </div>

      <div class="filter-dropdowns">
        <select v-model="filterType" class="filter-select">
          <option value="">All Types</option>
          <option value="income">Income</option>
          <option value="expense">Expense</option>
        </select>

        <select v-model="filterStatus" class="filter-select">
          <option value="">All Status</option>
          <option value="pending">Pending</option>
          <option value="completed">Completed</option>
          <option value="cancelled">Cancelled</option>
        </select>

        <button
          v-if="hasActiveFilters"
          class="btn btn-ghost btn-sm clear-btn"
          @click="clearFilters"
        >
          Clear
        </button>
      </div>
    </div>

    <!-- Transactions List Card -->
    <div class="card tx-card">
      <!-- Loading -->
      <div v-if="loading" class="tx-list">
        <div v-for="i in 5" :key="i" class="tx-row">
          <div class="tx-left">
            <LoadingSkeleton width="40px" height="40px" radius="10px" />
            <div>
              <LoadingSkeleton
                width="160px"
                height="16px"
                style="margin-bottom: 6px"
              />
              <LoadingSkeleton width="100px" height="13px" />
            </div>
          </div>
          <div class="tx-right-skeleton">
            <LoadingSkeleton width="100px" height="18px" />
            <LoadingSkeleton width="70px" height="22px" radius="12px" />
          </div>
        </div>
      </div>

      <!-- Empty -->
      <EmptyState
        v-else-if="filteredTransactions.length === 0 && !hasActiveFilters"
        title="No transactions yet"
        message="Start by recording your first transaction."
      >
        <template #action>
          <button
            class="btn btn-primary"
            @click="$router.push('/transactions/create')"
          >
            <svg
              width="16"
              height="16"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2.5"
              stroke-linecap="round"
              stroke-linejoin="round"
            >
              <line x1="12" y1="5" x2="12" y2="19" />
              <line x1="5" y1="12" x2="19" y2="12" />
            </svg>
            <span>Create Transaction</span>
          </button>
        </template>
      </EmptyState>

      <!-- No results for filter -->
      <EmptyState
        v-else-if="filteredTransactions.length === 0 && hasActiveFilters"
        title="No matching transactions"
        message="Try adjusting your search criteria or reset active filters."
      >
        <template #action>
          <button class="btn btn-outline" @click="clearFilters">
            Clear Filters
          </button>
        </template>
      </EmptyState>

      <!-- Data -->
      <div v-else class="tx-list">
        <div
          v-for="tx in filteredTransactions"
          :key="tx.id"
          class="tx-row"
        >
          <div class="tx-left">
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
                <polyline points="23 6 13.5 15.5 8.5 10.5 1 18" />
                <polyline points="17 6 23 6 23 12" />
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
                <polyline points="23 18 13.5 8.5 8.5 13.5 1 6" />
                <polyline points="17 18 23 18 23 12" />
              </svg>
            </div>
            <div class="tx-info">
              <div class="tx-desc-row">
                <span class="tx-desc">{{ tx.description || 'Untitled Transaction' }}</span>
                <span v-if="tx.transaction_number" class="tx-number">#{{ tx.transaction_number }}</span>
              </div>
              <span class="tx-date">{{ formatDate(tx.transaction_date) }}</span>
            </div>
          </div>

          <div class="tx-right">
            <span
              class="tx-amount"
              :class="tx.type === 'income' ? 'income-val' : 'expense-val'"
            >
              {{ tx.type === 'income' ? '+' : '-' }}{{ formatRupiah(tx.amount) }}
            </span>
            <div class="tx-badges">
              <TypeBadge :type="tx.type" />
              <StatusBadge :status="tx.status" />
            </div>
          </div>

          <div class="tx-actions">
            <button
              class="btn btn-ghost btn-icon btn-sm"
              title="Edit Transaction"
              @click="$router.push(`/transactions/${tx.id}/edit`)"
            >
              <svg
                width="16"
                height="16"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
              >
                <path
                  d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"
                />
                <path
                  d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"
                />
              </svg>
            </button>
            <button
              class="btn btn-ghost btn-icon btn-sm action-delete"
              title="Delete Transaction"
              @click="confirmDelete(tx)"
            >
              <svg
                width="16"
                height="16"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
              >
                <polyline points="3 6 5 6 21 6" />
                <path
                  d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"
                />
              </svg>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Delete Modal -->
    <DeleteModal
      :show="showDeleteModal"
      :identifier="transactionToDelete?.transaction_number || transactionToDelete?.description"
      :loading="isDeleting"
      @confirm="deleteTransaction"
      @cancel="showDeleteModal = false; transactionToDelete = null"
    />
  </AppLayout>
</template>

<style scoped>
/* Page Header */
.page-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-end;
  margin-bottom: 24px;
  gap: 16px;
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

/* Filter Bar */
.filter-bar {
  display: flex;
  flex-direction: column;
  padding: 12px 16px;
  margin-bottom: 20px;
  gap: 12px;
  border-radius: var(--radius-xl);
}

@media (min-width: 768px) {
  .filter-bar {
    flex-direction: row;
    align-items: center;
    padding: 10px 16px;
  }
}

.filter-search {
  position: relative;
  flex: 1;
}

.search-icon {
  position: absolute;
  left: 12px;
  top: 50%;
  transform: translateY(-50%);
  color: var(--gray-400);
  pointer-events: none;
}

.search-input {
  width: 100%;
  padding: 8px 14px 8px 36px;
  border: 1px solid var(--gray-200);
  border-radius: var(--radius-md);
  font-size: 0.875rem;
  background: var(--gray-50);
  transition: all var(--transition-fast);
}

.search-input:focus {
  background: #fff;
  border-color: var(--primary-500);
  box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.12);
}

.filter-dropdowns {
  display: flex;
  gap: 8px;
  align-items: center;
}

.filter-select {
  padding: 8px 30px 8px 12px;
  font-size: 0.8125rem;
  border: 1px solid var(--gray-200);
  border-radius: var(--radius-md);
  background: var(--gray-50);
  min-width: 110px;
}

.filter-select:focus {
  background: #fff;
  border-color: var(--primary-500);
}

.clear-btn {
  color: var(--gray-500);
}

/* Transaction Card */
.tx-card {
  border-radius: var(--radius-xl);
  overflow: hidden;
}

.tx-list {
  display: flex;
  flex-direction: column;
}

.tx-row {
  display: flex;
  flex-direction: column;
  padding: 16px 24px;
  border-bottom: 1px solid var(--gray-100);
  gap: 12px;
  transition: background-color var(--transition-fast);
}

.tx-row:last-child {
  border-bottom: none;
}

.tx-row:hover {
  background-color: #f8fafc;
}

.tx-left {
  display: flex;
  align-items: center;
  gap: 14px;
  flex: 1;
  min-width: 0;
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

.tx-info {
  display: flex;
  flex-direction: column;
  min-width: 0;
}

.tx-desc-row {
  display: flex;
  align-items: baseline;
  gap: 8px;
}

.tx-desc {
  font-size: 0.875rem;
  font-weight: 600;
  color: var(--gray-900);
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.tx-number {
  font-size: 0.6875rem;
  color: var(--gray-400);
  font-family: monospace;
  font-weight: 600;
  background: var(--gray-100);
  padding: 1px 6px;
  border-radius: 4px;
  flex-shrink: 0;
}

.tx-date {
  font-size: 0.75rem;
  color: var(--gray-500);
  margin-top: 2px;
}

.tx-right {
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.tx-right-skeleton {
  display: flex;
  flex-direction: column;
  align-items: flex-end;
  gap: 6px;
}

.tx-amount {
  font-size: 0.9375rem;
  font-weight: 700;
  font-variant-numeric: tabular-nums;
  white-space: nowrap;
}

.income-val {
  color: #047857;
}

.expense-val {
  color: #be123c;
}

.tx-badges {
  display: flex;
  gap: 6px;
}

.tx-actions {
  display: flex;
  gap: 4px;
}

.action-delete {
  color: var(--gray-400);
}
.action-delete:hover {
  color: #f43f5e;
  background: #fff1f2;
}

/* Desktop row layout */
@media (min-width: 768px) {
  .tx-row {
    flex-direction: row;
    align-items: center;
    gap: 20px;
    padding: 16px 24px;
  }

  .tx-right {
    align-items: flex-end;
    min-width: 170px;
  }

  .tx-badges {
    justify-content: flex-end;
  }

  .tx-actions {
    min-width: 72px;
    justify-content: flex-end;
  }
}

@media (max-width: 479px) {
  .page-header {
    flex-direction: column;
    align-items: flex-start;
  }
}
</style>