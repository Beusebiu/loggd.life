<template>
  <div class="floating-dots">
    <div
      v-for="i in 20"
      :key="i"
      class="dot"
      :style="{
        left: `${Math.random() * 100}%`,
        top: `${Math.random() * 100}%`,
        animationDelay: `${Math.random() * 5}s`,
        animationDuration: `${5 + Math.random() * 3}s`,
      }"
    ></div>
  </div>
</template>

<style scoped>
.floating-dots {
  position: absolute;
  inset: 0;
  pointer-events: none;
  overflow: hidden;
}

.dot {
  position: absolute;
  width: 4px;
  height: 4px;
  background: rgba(156, 163, 175, 0.4);
  border-radius: 50%;
  animation: float-up linear infinite;
}

@keyframes float-up {
  0% {
    transform: translateY(110%) translateX(0);
    opacity: 0;
  }
  10% {
    opacity: 0.6;
  }
  90% {
    opacity: 0.3;
  }
  100% {
    transform: translateY(-10%) translateX(20px);
    opacity: 0;
  }
}
</style>
