<script setup>
defineProps({
  type: {
    type: String,
    required: true,
    validator: (v) => ['income', 'expense'].includes(v),
  },
})
</script>

<template>
  <span class="type-badge" :class="`type-${type}`">
    <svg
      v-if="type === 'income'"
      width="13"
      height="13"
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
      width="13"
      height="13"
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
    <span>{{ type === 'income' ? 'Income' : 'Expense' }}</span>
  </span>
</template>

<style scoped>
.type-badge {
  display: inline-flex;
  align-items: center;
  gap: 4px;
  padding: 3px 8px;
  font-size: 0.75rem;
  font-weight: 600;
  border-radius: var(--radius-full);
  letter-spacing: -0.01em;
  white-space: nowrap;
  border: 1px solid transparent;
}

.type-income {
  background: var(--income-bg);
  color: var(--income-text);
  border-color: var(--income-border);
}

.type-expense {
  background: var(--expense-bg);
  color: var(--expense-text);
  border-color: var(--expense-border);
}
</style>
