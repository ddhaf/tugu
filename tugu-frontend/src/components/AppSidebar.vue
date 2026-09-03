<script setup>
import { useRoute, useRouter } from 'vue-router'

const route = useRoute()
const router = useRouter()

const isDashboardActive = () => {
  return route.name === 'dashboard'
}

const isTransactionsActive = () => {
  return (
    route.name === 'transactions' ||
    route.name === 'create-transaction' ||
    route.name === 'edit-transaction'
  )
}

const logout = () => {
  localStorage.removeItem('token')
  localStorage.removeItem('user')
  router.push('/login')
}
</script>

<template>
  <aside class="app-sidebar">
    <div class="sidebar-top">
      <!-- Brand -->
      <div class="sidebar-brand">
        <div class="brand-logo-wrap">
          <img
            src="/tugu-logo.png"
            alt="Tugu Insurance"
            class="sidebar-logo"
          />
        </div>
      </div>

      <!-- Navigation -->
      <nav class="sidebar-nav">
        <div class="nav-section-label">MAIN MENU</div>
        
        <RouterLink
          to="/dashboard"
          class="nav-item"
          :class="{ active: isDashboardActive() }"
        >
          <div class="nav-icon">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <rect x="3" y="3" width="7" height="7" rx="1.5"/>
              <rect x="14" y="3" width="7" height="7" rx="1.5"/>
              <rect x="3" y="14" width="7" height="7" rx="1.5"/>
              <rect x="14" y="14" width="7" height="7" rx="1.5"/>
            </svg>
          </div>
          <span class="nav-text">Dashboard</span>
        </RouterLink>

        <RouterLink
          to="/transactions"
          class="nav-item"
          :class="{ active: isTransactionsActive() }"
        >
          <div class="nav-icon">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <line x1="8" y1="6" x2="21" y2="6"/>
              <line x1="8" y1="12" x2="21" y2="12"/>
              <line x1="8" y1="18" x2="21" y2="18"/>
              <line x1="3" y1="6" x2="3.01" y2="6"/>
              <line x1="3" y1="12" x2="3.01" y2="12"/>
              <line x1="3" y1="18" x2="3.01" y2="18"/>
            </svg>
          </div>
          <span class="nav-text">Transactions</span>
        </RouterLink>
      </nav>
    </div>

    <!-- Bottom Logout -->
    <div class="sidebar-bottom">
      <button class="nav-item logout-item" @click="logout">
        <div class="nav-icon">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
            <polyline points="16 17 21 12 16 7"/>
            <line x1="21" y1="12" x2="9" y2="12"/>
          </svg>
        </div>
        <span class="nav-text">Sign Out</span>
      </button>
    </div>
  </aside>
</template>

<style scoped>
.app-sidebar {
  width: var(--sidebar-width);
  background: #0f172a;
  color: #94a3b8;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  position: fixed;
  top: 0;
  left: 0;
  bottom: 0;
  z-index: 30;
  border-right: 1px solid rgba(255, 255, 255, 0.06);
}

.sidebar-top {
  display: flex;
  flex-direction: column;
}

/* Brand */
.sidebar-brand {
  padding: 24px 20px 20px;
  display: flex;
  align-items: center;
  border-bottom: 1px solid rgba(255, 255, 255, 0.06);
}

.brand-logo-wrap {
  display: flex;
  align-items: center;
  width: 100%;
}

.sidebar-logo {
  height: 42px;
  width: auto;
  max-width: 160px;
  object-fit: contain;
}

/* Navigation */
.sidebar-nav {
  padding: 20px 14px;
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.nav-section-label {
  font-size: 0.6875rem;
  font-weight: 700;
  letter-spacing: 0.08em;
  color: #475569;
  padding: 0 12px 6px;
}

.nav-item {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 10px 14px;
  border-radius: var(--radius-md);
  color: #94a3b8;
  font-size: 0.875rem;
  font-weight: 500;
  transition: all var(--transition-fast);
  cursor: pointer;
  background: transparent;
  border: 1px solid transparent;
  width: 100%;
  text-align: left;
  font-family: var(--font-sans);
}

.nav-icon {
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  transition: transform var(--transition-fast);
}

.nav-item:hover {
  background: rgba(255, 255, 255, 0.05);
  color: #f1f5f9;
}

.nav-item:hover .nav-icon {
  transform: scale(1.05);
}

.nav-item.active {
  background: rgba(255, 255, 255, 0.09);
  color: #ffffff;
  font-weight: 600;
  border-color: rgba(255, 255, 255, 0.1);
  box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.1);
}

.nav-item.active .nav-icon {
  color: #38bdf8;
}

/* Logout */
.sidebar-bottom {
  padding: 16px 14px;
  border-top: 1px solid rgba(255, 255, 255, 0.06);
}

.logout-item {
  color: #64748b;
}

.logout-item:hover {
  color: #f43f5e;
  background: rgba(244, 63, 94, 0.1);
}

/* Hide on mobile */
@media (max-width: 767px) {
  .app-sidebar {
    display: none;
  }
}
</style>
