<template>
  <Teleport to="body">
    <Transition name="celebration">
      <div
        v-if="show"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm"
        @click="close"
      >
        <!-- Confetti Container -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
          <div
            v-for="i in 50"
            :key="i"
            class="confetti"
            :style="getConfettiStyle(i)"
          ></div>
        </div>

        <!-- Celebration Card -->
        <div
          class="relative bg-white rounded-3xl shadow-2xl max-w-md w-full mx-4 overflow-hidden animate-bounce-in"
          @click.stop
        >
          <!-- Animated Background -->
          <div class="absolute inset-0 bg-gradient-to-br from-green-400 via-emerald-500 to-green-600 opacity-10 animate-pulse"></div>

          <!-- Content -->
          <div class="relative p-8 text-center">
            <!-- Emoji Animation -->
            <div class="text-8xl mb-6 animate-bounce">
              {{ emoji }}
            </div>

            <!-- Message -->
            <h2 class="text-3xl font-bold text-gray-900 mb-4">
              {{ title }}
            </h2>
            <p class="text-lg text-gray-700 mb-6">
              {{ message }}
            </p>

            <!-- Close Button -->
            <button
              @click="close"
              class="px-8 py-3 bg-gradient-to-r from-green-500 to-emerald-600 text-white font-bold rounded-full hover:from-green-600 hover:to-emerald-700 transition-all shadow-lg shadow-green-500/50 transform hover:scale-105"
            >
              Awesome!
            </button>
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
import { ref, watch } from 'vue';

const props = defineProps({
  show: {
    type: Boolean,
    default: false,
  },
  emoji: {
    type: String,
    default: '🎉',
  },
  title: {
    type: String,
    default: 'Congratulations!',
  },
  message: {
    type: String,
    default: 'You did it!',
  },
  duration: {
    type: Number,
    default: 0, // 0 means manual close only
  },
});

const emit = defineEmits(['close']);

const close = () => {
  emit('close');
};

// Auto-close after duration if set
watch(() => props.show, (newVal) => {
  if (newVal && props.duration > 0) {
    setTimeout(() => {
      close();
    }, props.duration);
  }
});

// Generate random confetti styles
const getConfettiStyle = (index) => {
  const colors = ['#10b981', '#34d399', '#6ee7b7', '#fbbf24', '#f59e0b', '#ec4899', '#f97316'];
  const randomColor = colors[Math.floor(Math.random() * colors.length)];
  const randomLeft = Math.random() * 100;
  const randomDelay = Math.random() * 2;
  const randomDuration = 2 + Math.random() * 3;

  return {
    left: `${randomLeft}%`,
    backgroundColor: randomColor,
    animationDelay: `${randomDelay}s`,
    animationDuration: `${randomDuration}s`,
  };
};
</script>

<style scoped>
/* Celebration transition */
.celebration-enter-active,
.celebration-leave-active {
  transition: opacity 0.3s ease;
}

.celebration-enter-from,
.celebration-leave-to {
  opacity: 0;
}

/* Bounce in animation for the card */
@keyframes bounce-in {
  0% {
    transform: scale(0.3);
    opacity: 0;
  }
  50% {
    transform: scale(1.05);
  }
  70% {
    transform: scale(0.9);
  }
  100% {
    transform: scale(1);
    opacity: 1;
  }
}

.animate-bounce-in {
  animation: bounce-in 0.6s cubic-bezier(0.68, -0.55, 0.265, 1.55);
}

/* Confetti animation */
.confetti {
  position: absolute;
  width: 10px;
  height: 10px;
  top: -10px;
  opacity: 0;
  animation: confetti-fall linear infinite;
}

@keyframes confetti-fall {
  0% {
    top: -10px;
    opacity: 1;
    transform: rotate(0deg);
  }
  100% {
    top: 100%;
    opacity: 0;
    transform: rotate(720deg);
  }
}
</style>
