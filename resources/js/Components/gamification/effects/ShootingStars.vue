<template>
  <div class="shooting-stars">
    <div
      v-for="i in 10"
      :key="i"
      class="star"
      :style="{
        top: `${Math.random() * 100}%`,
        left: '-100px',
        animationDelay: `${Math.random() * 5}s`,
        animationDuration: `${1 + Math.random()}s`,
      }"
    ></div>
  </div>
</template>

<style scoped>
.shooting-stars {
  position: absolute;
  inset: 0;
  pointer-events: none;
  overflow: hidden;
}

.star {
  position: absolute;
  left: -100px;
  width: 2px;
  height: 2px;
  background: white;
  border-radius: 50%;
  box-shadow:
    0 0 10px rgba(255, 255, 255, 1),
    0 0 20px rgba(34, 211, 238, 0.8),
    0 0 30px rgba(6, 182, 212, 0.6);
  animation: shoot linear infinite;
}

.star::after {
  content: '';
  position: absolute;
  top: 0;
  right: 0;
  height: 2px;
  width: 100px;
  background: linear-gradient(90deg, rgba(255, 255, 255, 1), transparent);
}

@keyframes shoot {
  0% {
    transform: translateX(0) translateY(0);
    opacity: 1;
  }
  100% {
    transform: translateX(1000px) translateY(500px);
    opacity: 0;
  }
}
</style>
