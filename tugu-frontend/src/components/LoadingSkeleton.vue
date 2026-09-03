<script setup>
defineProps({
  width: {
    type: String,
    default: '100%',
  },
  height: {
    type: String,
    default: '20px',
  },
  radius: {
    type: String,
    default: 'var(--radius-md)',
  },
})
</script>

<template>
  <div
    class="skeleton"
    :style="{
      width,
      height,
      borderRadius: radius,
    }"
  />
</template>

<style scoped>
.skeleton {
  background: linear-gradient(
    90deg,
    var(--gray-100) 0%,
    var(--gray-200) 40%,
    var(--gray-100) 80%
  );
  background-size: 400px 100%;
  animation: shimmer 1.5s ease-in-out infinite;
}

@keyframes shimmer {
  0% {
    background-position: -400px 0;
  }
  100% {
    background-position: 400px 0;
  }
}
</style>
