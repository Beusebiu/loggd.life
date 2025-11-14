<template>
  <div class="fireflies">
    <div
      v-for="i in 25"
      :key="i"
      class="firefly"
      :style="{
        left: `${Math.random() * 100}%`,
        top: `${Math.random() * 100}%`,
        animationDelay: `${Math.random() * 5}s`,
        animationDuration: `${3 + Math.random() * 2}s`,
      }"
    ></div>
  </div>
</template>

<style scoped>
.fireflies {
  position: absolute;
  inset: 0;
  pointer-events: none;
  overflow: hidden;
}

.firefly {
  position: absolute;
  width: 3px;
  height: 3px;
  background: radial-gradient(circle, rgba(251, 191, 36, 1) 0%, rgba(251, 191, 36, 0.3) 50%, transparent 100%);
  border-radius: 50%;
  box-shadow:
    0 0 10px rgba(251, 191, 36, 0.8),
    0 0 20px rgba(251, 191, 36, 0.5),
    0 0 30px rgba(251, 191, 36, 0.3);
  animation: firefly-dance ease-in-out infinite, firefly-blink ease-in-out infinite;
}

@keyframes firefly-dance {
  0%, 100% {
    transform: translate(0, 0);
  }
  25% {
    transform: translate(30px, -20px);
  }
  50% {
    transform: translate(-20px, 20px);
  }
  75% {
    transform: translate(20px, 30px);
  }
}

@keyframes firefly-blink {
  0%, 100% {
    opacity: 0.2;
  }
  50% {
    opacity: 1;
  }
}
</style>
