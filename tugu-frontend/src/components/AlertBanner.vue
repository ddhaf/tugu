<script setup>
defineProps({
  type: {
    type: String,
    default: 'error',
    validator: (v) => ['error', 'success', 'warning', 'info'].includes(v),
  },
  message: {
    type: String,
    required: true,
  },
  dismissible: {
    type: Boolean,
    default: true,
  },
})

defineEmits(['close'])
</script>

<template>
  <div class="alert" :class="`alert-${type}`" role="alert">
    <!-- Error icon -->
    <svg
      v-if="type === 'error'"
      class="alert-icon"
      width="18"
      height="18"
      viewBox="0 0 24 24"
      fill="none"
      stroke="currentColor"
      stroke-width="2"
      stroke-linecap="round"
      stroke-linejoin="round"
    >
      <circle cx="12" cy="12" r="10" />
      <line x1="15" y1="9" x2="9" y2="15" />
      <line x1="9" y1="9" x2="15" y2="15" />
    </svg>

    <!-- Success icon -->
    <svg
      v-else-if="type === 'success'"
      class="alert-icon"
      width="18"
      height="18"
      viewBox="0 0 24 24"
      fill="none"
      stroke="currentColor"
      stroke-width="2"
      stroke-linecap="round"
      stroke-linejoin="round"
    >
      <circle cx="12" cy="12" r="10" />
      <polyline points="9 12 11.5 14.5 16 9.5" />
    </svg>

    <!-- Warning icon -->
    <svg
      v-else-if="type === 'warning'"
      class="alert-icon"
      width="18"
      height="18"
      viewBox="0 0 24 24"
      fill="none"
      stroke="currentColor"
      stroke-width="2"
      stroke-linecap="round"
      stroke-linejoin="round"
    >
      <path
        d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"
      />
      <line x1="12" y1="9" x2="12" y2="13" />
      <line x1="12" y1="17" x2="12.01" y2="17" />
    </svg>

    <!-- Info icon -->
    <svg
      v-else
      class="alert-icon"
      width="18"
      height="18"
      viewBox="0 0 24 24"
      fill="none"
      stroke="currentColor"
      stroke-width="2"
      stroke-linecap="round"
      stroke-linejoin="round"
    >
      <circle cx="12" cy="12" r="10" />
      <line x1="12" y1="16" x2="12" y2="12" />
      <line x1="12" y1="8" x2="12.01" y2="8" />
    </svg>

    <p class="alert-message">{{ message }}</p>

    <button
      v-if="dismissible"
      class="alert-close"
      @click="$emit('close')"
      aria-label="Dismiss"
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
        <line x1="18" y1="6" x2="6" y2="18" />
        <line x1="6" y1="6" x2="18" y2="18" />
      </svg>
    </button>
  </div>
</template>

<style scoped>
.alert {
  display: flex;
  align-items: flex-start;
  gap: 10px;
  padding: 12px 16px;
  border-radius: var(--radius-md);
  font-size: 0.875rem;
  line-height: 1.5;
  animation: slideDown 0.3s ease;
}

.alert-icon {
  flex-shrink: 0;
  margin-top: 1px;
}

.alert-message {
  flex: 1;
  font-weight: 500;
}

.alert-close {
  flex-shrink: 0;
  background: none;
  border: none;
  padding: 2px;
  cursor: pointer;
  color: inherit;
  opacity: 0.6;
  transition: opacity var(--transition-fast);
  border-radius: var(--radius-sm);
}

.alert-close:hover {
  opacity: 1;
}

/* Error */
.alert-error {
  background: var(--expense-bg);
  color: var(--expense-text);
  border: 1px solid rgba(239, 68, 68, 0.15);
}

/* Success */
.alert-success {
  background: var(--income-bg);
  color: var(--income-text);
  border: 1px solid rgba(16, 185, 129, 0.15);
}

/* Warning */
.alert-warning {
  background: var(--status-pending-bg);
  color: var(--status-pending-text);
  border: 1px solid rgba(245, 158, 11, 0.15);
}

/* Info */
.alert-info {
  background: var(--accent-50);
  color: var(--accent-700);
  border: 1px solid rgba(6, 182, 212, 0.15);
}

@keyframes slideDown {
  from {
    opacity: 0;
    transform: translateY(-6px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}
</style>
