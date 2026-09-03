<script setup>
defineProps({
  status: {
    type: String,
    required: true,
    validator: (v) => ['pending', 'completed', 'cancelled'].includes(v),
  },
})

const labels = {
  pending: 'Pending',
  completed: 'Completed',
  cancelled: 'Cancelled',
}
</script>

<template>
  <span class="status-badge" :class="`status-${status}`">
    <span class="status-dot" />
    <span class="status-label">{{ labels[status] || status }}</span>
  </span>
</template>

<style scoped>
.status-badge {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 3px 9px;
  font-size: 0.75rem;
  font-weight: 600;
  border-radius: var(--radius-full);
  letter-spacing: -0.01em;
  white-space: nowrap;
  border: 1px solid transparent;
}

.status-dot {
  width: 6px;
  height: 6px;
  border-radius: 50%;
  flex-shrink: 0;
}

.status-pending {
  background: #fffbeb;
  color: #b45309;
  border-color: #fde68a;
}
.status-pending .status-dot {
  background: #f59e0b;
  box-shadow: 0 0 0 2px rgba(245, 158, 11, 0.2);
}

.status-completed {
  background: #ecfdf5;
  color: #047857;
  border-color: #a7f3d0;
}
.status-completed .status-dot {
  background: #10b981;
  box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.2);
}

.status-cancelled {
  background: #f1f5f9;
  color: #475569;
  border-color: #e2e8f0;
}
.status-cancelled .status-dot {
  background: #64748b;
  box-shadow: 0 0 0 2px rgba(100, 116, 139, 0.2);
}
</style>
