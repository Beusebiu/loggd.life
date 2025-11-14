<template>
  <div class="snowfall">
    <div
      v-for="i in 30"
      :key="i"
      class="snowflake"
      :style="{
        left: `${Math.random() * 100}%`,
        top: '-5%',
        animationDelay: `${Math.random() * 8}s`,
        animationDuration: `${8 + Math.random() * 4}s`,
        fontSize: `${10 + Math.random() * 8}px`,
      }"
    >
      ❄
    </div>
  </div>
</template>

<style scoped>
.snowfall {
  position: absolute;
  inset: 0;
  pointer-events: none;
  overflow: hidden;
}

.snowflake {
  position: absolute;
  color: rgba(212, 212, 216, 0.8);
  animation: snow-fall linear infinite;
  text-shadow: 0 0 5px rgba(255, 255, 255, 0.5);
}

@keyframes snow-fall {
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
    transform: translateY(110%) rotate(360deg);
    opacity: 0;
  }
}
</style>
