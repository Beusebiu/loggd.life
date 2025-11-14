<template>
  <div class="aurora-lights">
    <div class="aurora-layer aurora-1"></div>
    <div class="aurora-layer aurora-2"></div>
    <div class="aurora-layer aurora-3"></div>
  </div>
</template>

<style scoped>
.aurora-lights {
  position: absolute;
  inset: 0;
  pointer-events: none;
  overflow: hidden;
}

.aurora-layer {
  position: absolute;
  width: 200%;
  height: 100%;
  opacity: 0.3;
  filter: blur(40px);
}

.aurora-1 {
  background: linear-gradient(90deg, transparent, rgba(139, 92, 246, 0.5), transparent);
  animation: aurora-wave 8s ease-in-out infinite;
}

.aurora-2 {
  background: linear-gradient(90deg, transparent, rgba(59, 130, 246, 0.5), transparent);
  animation: aurora-wave 10s ease-in-out infinite reverse;
  animation-delay: 2s;
}

.aurora-3 {
  background: linear-gradient(90deg, transparent, rgba(6, 182, 212, 0.5), transparent);
  animation: aurora-wave 12s ease-in-out infinite;
  animation-delay: 4s;
}

@keyframes aurora-wave {
  0% {
    transform: translateX(-50%) translateY(0%) skewX(0deg);
  }
  25% {
    transform: translateX(-25%) translateY(-10%) skewX(5deg);
  }
  50% {
    transform: translateX(0%) translateY(0%) skewX(0deg);
  }
  75% {
    transform: translateX(25%) translateY(10%) skewX(-5deg);
  }
  100% {
    transform: translateX(50%) translateY(0%) skewX(0deg);
  }
}
</style>
