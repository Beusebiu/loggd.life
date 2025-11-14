<template>
  <div class="energy-particles">
    <div
      v-for="i in 30"
      :key="i"
      class="particle"
      :style="{
        left: `${Math.random() * 100}%`,
        bottom: '-5%',
        background: colors[Math.floor(Math.random() * colors.length)],
        animationDelay: `${Math.random() * 3}s`,
        animationDuration: `${3 + Math.random() * 2}s`,
      }"
    ></div>
  </div>
</template>

<script setup>
const colors = [
  'rgba(59, 130, 246, 0.9)',
  'rgba(139, 92, 246, 0.9)',
  'rgba(6, 182, 212, 0.9)',
];
</script>

<style scoped>
.energy-particles {
  position: absolute;
  inset: 0;
  pointer-events: none;
  overflow: hidden;
}

.particle {
  position: absolute;
  width: 6px;
  height: 6px;
  border-radius: 50%;
  box-shadow: 0 0 15px currentColor, 0 0 30px currentColor;
  animation: energy-rise ease-out infinite;
}

@keyframes energy-rise {
  0% {
    transform: translateY(110%) translateX(0) scale(0.5);
    opacity: 0;
  }
  10% {
    opacity: 1;
  }
  50% {
    transform: translateY(50%) translateX(30px) scale(1.3);
  }
  90% {
    opacity: 0.8;
  }
  100% {
    transform: translateY(-10%) translateX(-20px) scale(0.5);
    opacity: 0;
  }
}
</style>
