<script setup>
defineProps({
  title: {
    type: String,
    default: 'No data found',
  },
  message: {
    type: String,
    default: '',
  },
})
</script>

<template>
  <div class="empty-state">
    <div class="empty-icon">
      <svg
        width="56"
        height="56"
        viewBox="0 0 24 24"
        fill="none"
        stroke="currentColor"
        stroke-width="1.2"
        stroke-linecap="round"
        stroke-linejoin="round"
      >
        <rect x="2" y="3" width="20" height="18" rx="3" />
        <line x1="2" y1="9" x2="22" y2="9" />
        <line x1="9" y1="3" x2="9" y2="9" />
      </svg>
    </div>
    <h3 class="empty-title">{{ title }}</h3>
    <p v-if="message" class="empty-message">{{ message }}</p>
    <div class="empty-action">
      <slot name="action" />
    </div>
  </div>
</template>

<style scoped>
.empty-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 48px 24px;
  text-align: center;
  animation: fadeIn 0.4s ease;
}

.empty-icon {
  width: 88px;
  height: 88px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: var(--gray-100);
  border-radius: 50%;
  color: var(--gray-400);
  margin-bottom: 20px;
}

.empty-title {
  font-size: 1.0625rem;
  font-weight: 600;
  color: var(--gray-700);
  margin-bottom: 6px;
}

.empty-message {
  font-size: 0.875rem;
  color: var(--gray-500);
  max-width: 320px;
  line-height: 1.5;
}

.empty-action {
  margin-top: 20px;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(8px); }
  to { opacity: 1; transform: translateY(0); }
}
</style>
