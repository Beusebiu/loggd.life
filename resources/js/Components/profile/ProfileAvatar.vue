<template>
  <div class="avatar" :class="avatarClass">
    <span class="avatar-letter">{{ initial }}</span>
    <div v-if="showGlow" class="avatar-glow"></div>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  userName: {
    type: String,
    required: true,
  },
  avatarClass: {
    type: String,
    default: 'avatar-default',
  },
  showGlow: {
    type: Boolean,
    default: false,
  },
});

const initial = computed(() => props.userName.charAt(0).toUpperCase());
</script>

<style scoped>
.avatar {
  width: 4rem;
  height: 4rem;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  position: relative;
  color: white;
  font-weight: bold;
  font-size: 1.5rem;
  box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
  transition: all 0.3s;
}

@media (min-width: 640px) {
  .avatar {
    width: 5rem;
    height: 5rem;
    font-size: 1.875rem;
  }
}

/* Tier 1-3: Grayscale/Colorless - No special effects */
.avatar-default {
  background: linear-gradient(135deg, #d1d5db 0%, #9ca3af 100%);
  color: #374151;
}

/* Tier 4: Relentless - Steel Blue-Gray */
.avatar-relentless {
  background: linear-gradient(135deg, #64748b 0%, #475569 100%);
  box-shadow: 0 4px 16px rgba(100, 116, 139, 0.4);
}

/* Tier 5: Unwavering - Steel Blue */
.avatar-unwavering {
  background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);
  box-shadow: 0 6px 20px rgba(14, 165, 233, 0.5);
}

/* Tier 6: Formidable - Bronze/Gold */
.avatar-formidable {
  background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
  box-shadow: 0 6px 20px rgba(245, 158, 11, 0.6), 0 0 30px rgba(217, 119, 6, 0.3);
}

/* Tier 7: Indomitable - Deep Blue */
.avatar-indomitable {
  background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
  box-shadow: 0 8px 24px rgba(59, 130, 246, 0.7), 0 0 40px rgba(37, 99, 235, 0.4);
}

/* Tier 8: Invincible - Electric Purple */
.avatar-invincible {
  background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
  box-shadow: 0 10px 28px rgba(139, 92, 246, 0.8), 0 0 50px rgba(124, 58, 237, 0.5);
  animation: pulse-glow 2.5s ease-in-out infinite;
}

/* Tier 9: Immortal - Diamond/Crystal */
.avatar-immortal {
  background: linear-gradient(135deg, #06b6d4 0%, #0891b2 50%, #0e7490 100%);
  box-shadow: 0 12px 32px rgba(8, 145, 178, 0.9), 0 0 60px rgba(34, 211, 238, 0.6);
  animation: pulse-glow 2.3s ease-in-out infinite;
}

/* Tier 10: Eternal - Cosmic Gold */
.avatar-eternal {
  background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 50%, #d97706 100%);
  box-shadow: 0 14px 36px rgba(245, 158, 11, 0.9), 0 0 70px rgba(251, 191, 36, 0.7);
  animation: pulse-glow 2.1s ease-in-out infinite;
}

/* Tier 11: Omnipotent - Royal Purple/Gold */
.avatar-omnipotent {
  background: linear-gradient(135deg, #a855f7 0%, #9333ea 50%, #f59e0b 100%);
  box-shadow: 0 16px 40px rgba(168, 85, 247, 0.9), 0 0 80px rgba(245, 158, 11, 0.7);
  animation: pulse-glow 2s ease-in-out infinite;
}

/* Tier 12: Absolute - Prismatic Everything */
.avatar-absolute {
  background: linear-gradient(135deg, #fbbf24 0%, #d946ef 50%, #7e22ce 100%);
  box-shadow: 0 18px 44px rgba(217, 70, 239, 1), 0 0 90px rgba(251, 191, 36, 0.9), 0 0 120px rgba(168, 85, 247, 0.6);
  animation: absolute-glow 2s ease-in-out infinite;
}

.avatar-glow {
  position: absolute;
  inset: 0;
  border-radius: 50%;
  background: radial-gradient(circle, rgba(255, 255, 255, 0.4) 0%, transparent 70%);
  animation: pulse 2s ease-in-out infinite;
}

@keyframes pulse {
  0%, 100% { opacity: 0.6; transform: scale(1); }
  50% { opacity: 1; transform: scale(1.1); }
}

@keyframes pulse-glow {
  0%, 100% { opacity: 0.7; transform: scale(1); }
  50% { opacity: 1; transform: scale(1.08); }
}

@keyframes absolute-glow {
  0%, 100% {
    opacity: 1;
    transform: scale(1);
    box-shadow: 0 18px 44px rgba(217, 70, 239, 1), 0 0 90px rgba(251, 191, 36, 0.9), 0 0 120px rgba(168, 85, 247, 0.6);
  }
  50% {
    opacity: 1;
    transform: scale(1.12);
    box-shadow: 0 22px 52px rgba(217, 70, 239, 1), 0 0 110px rgba(251, 191, 36, 1), 0 0 150px rgba(168, 85, 247, 0.8);
  }
}
</style>
