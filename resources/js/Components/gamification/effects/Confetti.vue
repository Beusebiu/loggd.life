<template>
  <div class="confetti">
    <div
      v-for="i in 40"
      :key="i"
      class="confetti-piece"
      :style="{
        left: `${Math.random() * 100}%`,
        top: '-5%',
        background: colors[Math.floor(Math.random() * colors.length)],
        animationDelay: `${Math.random() * 3}s`,
        animationDuration: `${3 + Math.random() * 2}s`,
        transform: `rotate(${Math.random() * 360}deg)`,
      }"
    ></div>
  </div>
</template>

<script setup>
const colors = [
  'rgba(59, 130, 246, 0.9)',
  'rgba(139, 92, 246, 0.9)',
  'rgba(236, 72, 153, 0.9)',
  'rgba(251, 191, 36, 0.9)',
  'rgba(34, 211, 238, 0.9)',
  'rgba(16, 185, 129, 0.9)',
];
</script>

<style scoped>
.confetti {
  position: absolute;
  inset: 0;
  pointer-events: none;
  overflow: hidden;
}

.confetti-piece {
  position: absolute;
  width: 10px;
  height: 10px;
  animation: confetti-fall linear infinite;
}

@keyframes confetti-fall {
  0% {
    transform: translateY(-10%) rotate(0deg);
    opacity: 0;
  }
  10% {
    opacity: 1;
  }
  90% {
    opacity: 0.8;
  }
  100% {
    transform: translateY(110%) rotate(720deg) translateX(50px);
    opacity: 0;
  }
}
</style>
