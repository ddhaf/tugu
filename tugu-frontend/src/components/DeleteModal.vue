<script setup>
defineProps({
  show: {
    type: Boolean,
    default: false,
  },
  identifier: {
    type: String,
    default: 'this transaction',
  },
  loading: {
    type: Boolean,
    default: false,
  },
})

defineEmits(['confirm', 'cancel'])
</script>

<template>
  <Teleport to="body">
    <Transition name="modal">
      <div v-if="show" class="modal-overlay" @click.self="$emit('cancel')">
        <div class="modal-container">
          <div class="modal-icon-wrapper">
            <svg
              width="26"
              height="26"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
              stroke-linecap="round"
              stroke-linejoin="round"
            >
              <polyline points="3 6 5 6 21 6" />
              <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" />
              <line x1="10" y1="11" x2="10" y2="17" />
              <line x1="14" y1="11" x2="14" y2="17" />
            </svg>
          </div>

          <h3 class="modal-title">Delete Transaction</h3>

          <p class="modal-message">
            Are you sure you want to delete
            <strong>{{ identifier }}</strong>? This action cannot be undone.
          </p>

          <div class="modal-actions">
            <button
              class="btn btn-outline"
              @click="$emit('cancel')"
              :disabled="loading"
            >
              Cancel
            </button>
            <button
              class="btn btn-danger"
              @click="$emit('confirm')"
              :disabled="loading"
            >
              <svg
                v-if="loading"
                class="spinner"
                width="16"
                height="16"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2.5"
              >
                <circle cx="12" cy="12" r="10" opacity="0.25" />
                <path d="M12 2a10 10 0 0 1 10 10" />
              </svg>
              {{ loading ? 'Deleting...' : 'Delete' }}
            </button>
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<style scoped>
.modal-overlay {
  position: fixed;
  inset: 0;
  z-index: 1000;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 24px;
  background: rgba(15, 23, 42, 0.6);
  backdrop-filter: blur(6px);
  -webkit-backdrop-filter: blur(6px);
}

.modal-container {
  background: #fff;
  border-radius: var(--radius-2xl);
  box-shadow: var(--shadow-xl);
  padding: 32px 28px;
  width: 100%;
  max-width: 380px;
  text-align: center;
  border: 1px solid var(--gray-100);
}

.modal-icon-wrapper {
  width: 52px;
  height: 52px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 16px;
  background: #fff1f2;
  border: 1px solid #ffe4e6;
  border-radius: var(--radius-lg);
  color: #f43f5e;
}

.modal-title {
  font-size: 1.125rem;
  font-weight: 700;
  color: var(--gray-900);
  margin-bottom: 8px;
  letter-spacing: -0.015em;
}

.modal-message {
  font-size: 0.875rem;
  color: var(--gray-500);
  line-height: 1.5;
  margin-bottom: 24px;
}

.modal-message strong {
  color: var(--gray-800);
}

.modal-actions {
  display: flex;
  gap: 10px;
  justify-content: center;
}

.modal-actions .btn {
  flex: 1;
}

/* Transition */
.modal-enter-active {
  transition: opacity 0.2s ease;
}
.modal-leave-active {
  transition: opacity 0.15s ease;
}
.modal-enter-from,
.modal-leave-to {
  opacity: 0;
}

.modal-enter-active .modal-container {
  animation: scaleIn 0.2s cubic-bezier(0.4, 0, 0.2, 1);
}
.modal-leave-active .modal-container {
  animation: scaleIn 0.15s cubic-bezier(0.4, 0, 0.2, 1) reverse;
}

@keyframes scaleIn {
  from {
    opacity: 0;
    transform: scale(0.94);
  }
  to {
    opacity: 1;
    transform: scale(1);
  }
}
</style>
