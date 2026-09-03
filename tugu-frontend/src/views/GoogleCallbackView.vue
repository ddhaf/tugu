<script setup>
import { onMounted } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()

onMounted(() => {
  const hash = window.location.hash

  if (!hash || !hash.includes('token=')) {
    router.push('/login')
    return
  }

  const params = new URLSearchParams(hash.substring(1))
  const token = params.get('token')

  if (!token) {
    router.push('/login')
    return
  }

  localStorage.setItem('token', token)

  router.push('/dashboard')
})
</script>

<template>
  <div>
    Signing you in with Google...
  </div>
</template>