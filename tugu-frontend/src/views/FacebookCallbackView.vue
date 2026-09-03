<script setup>
import { onMounted } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()

onMounted(() => {
  const params = new URLSearchParams(
    window.location.hash.substring(1),
  )

  const token = params.get('token')

  if (!token) {
    router.push('/login')
    return
  }

  localStorage.setItem('token', token)

  // Remove token from the URL after storing it
  window.history.replaceState(
    null,
    '',
    '/oauth/facebook/callback',
  )

  router.push('/dashboard')
})
</script>

<template>
  <div class="callback-page">
    Signing you in with Facebook...
  </div>
</template>

<style scoped>
.callback-page {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
}
</style>