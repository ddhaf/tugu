<script setup>
import { useRoute } from 'vue-router'
import AppSidebar from './AppSidebar.vue'
import AppHeader from './AppHeader.vue'

defineProps({
  pageTitle: {
    type: String,
    default: '',
  },
})

const route = useRoute()
</script>

<template>
  <div class="app-layout">
    <AppSidebar />

    <div class="main-wrapper">
      <AppHeader :pageTitle="pageTitle" />

      <main class="main-content">
        <slot />
      </main>
    </div>

    <!-- Mobile Bottom Navigation -->
    <nav class="mobile-nav">
      <RouterLink
        to="/dashboard"
        class="mobile-nav-item"
        :class="{ active: route.name === 'dashboard' }"
      >
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <rect x="3" y="3" width="7" height="7" rx="1.5"/>
          <rect x="14" y="3" width="7" height="7" rx="1.5"/>
          <rect x="3" y="14" width="7" height="7" rx="1.5"/>
          <rect x="14" y="14" width="7" height="7" rx="1.5"/>
        </svg>
        <span>Dashboard</span>
      </RouterLink>

      <RouterLink
        to="/transactions/create"
        class="mobile-nav-item mobile-nav-add"
      >
        <div class="add-btn">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <line x1="12" y1="5" x2="12" y2="19"/>
            <line x1="5" y1="12" x2="19" y2="12"/>
          </svg>
        </div>
        <span>New</span>
      </RouterLink>

      <RouterLink
        to="/transactions"
        class="mobile-nav-item"
        :class="{ active: route.name === 'transactions' || route.name === 'edit-transaction' }"
      >
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <line x1="8" y1="6" x2="21" y2="6"/>
          <line x1="8" y1="12" x2="21" y2="12"/>
          <line x1="8" y1="18" x2="21" y2="18"/>
          <line x1="3" y1="6" x2="3.01" y2="6"/>
          <line x1="3" y1="12" x2="3.01" y2="12"/>
          <line x1="3" y1="18" x2="3.01" y2="18"/>
        </svg>
        <span>Transactions</span>
      </RouterLink>
    </nav>
  </div>
</template>

<style scoped>
.app-layout {
  display: flex;
  min-height: 100vh;
  background: var(--gray-50);
}

.main-wrapper {
  flex: 1;
  display: flex;
  flex-direction: column;
  min-width: 0;
}

.main-content {
  flex: 1;
  padding: 32px 28px;
  max-width: 1200px;
  margin: 0 auto;
  width: 100%;
}

.mobile-nav {
  display: none;
}

@media (min-width: 768px) {
  .main-wrapper {
    padding-left: var(--sidebar-width);
  }
}

@media (max-width: 767px) {
  .main-content {
    padding: 20px 16px;
    padding-bottom: calc(20px + var(--mobile-nav-height));
  }

  .mobile-nav {
    display: flex;
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    height: var(--mobile-nav-height);
    background: #fff;
    border-top: 1px solid var(--gray-200);
    z-index: 30;
    padding-bottom: env(safe-area-inset-bottom);
    align-items: center;
    justify-content: space-around;
  }

  .mobile-nav-item {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 3px;
    color: var(--gray-400);
    font-size: 0.6875rem;
    font-weight: 600;
    text-decoration: none;
    padding: 6px 0;
    transition: color var(--transition-fast);
  }

  .mobile-nav-item.active {
    color: var(--primary-600);
  }

  .mobile-nav-add {
    color: var(--gray-500);
  }

  .add-btn {
    width: 38px;
    height: 38px;
    border-radius: 50%;
    background: linear-gradient(135deg, #0f172a, #1e293b);
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 2px 8px rgba(15, 23, 42, 0.25);
  }
}
</style>
