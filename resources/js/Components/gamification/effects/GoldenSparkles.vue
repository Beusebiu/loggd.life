<template>
  <div class="golden-sparkles">
    <div
      v-for="i in 20"
      :key="i"
      class="sparkle"
      :style="{
        left: `${Math.random() * 100}%`,
        top: '-5%',
        animationDelay: `${Math.random() * 4}s`,
        animationDuration: `${4 + Math.random() * 2}s`,
        fontSize: `${16 + Math.random() * 12}px`,
      }"
    >
      ✦
    </div>
  </div>
</template>

<style scoped>
.golden-sparkles {
  position: absolute;
  inset: 0;
  pointer-events: none;
  overflow: hidden;
}

.sparkle {
  position: absolute;
  color: rgba(251, 191, 36, 1);
  text-shadow:
    0 0 10px rgba(251, 191, 36, 1),
    0 0 20px rgba(245, 158, 11, 0.8),
    0 0 30px rgba(251, 191, 36, 0.5);
  animation: sparkle-fall linear infinite;
}

@keyframes sparkle-fall {
  0% {
    transform: translateY(-10%) rotate(0deg) scale(0);
    opacity: 0;
  }
  10% {
    transform: translateY(5%) rotate(45deg) scale(1);
    opacity: 1;
  }
  90% {
    opacity: 0.9;
  }
  100% {
    transform: translateY(110%) rotate(360deg) scale(0.8);
    opacity: 0;
  }
}
</style>
