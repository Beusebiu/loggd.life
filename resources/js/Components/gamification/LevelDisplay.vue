<template>
  <div class="level-display" :class="[tierClasses, animationClass]">
    <!-- Tier-specific decorative elements -->
    <div v-if="tier >= 41" class="phoenix-flames"></div>
    <div v-if="tier >= 51" class="luminary-stars"></div>
    <div v-if="tier >= 31" class="ocean-waves"></div>
    <div v-if="tier >= 21" class="mountain-peaks"></div>

    <div class="flex items-center justify-between mb-3 relative z-10">
      <div class="flex items-center gap-2 sm:gap-3">
        <span class="level-emoji text-3xl sm:text-4xl" :class="emojiAnimation">{{ levelInfo.emoji }}</span>
        <div>
          <h3 class="text-base sm:text-lg font-bold flex items-center gap-2" :class="titleColor">
            {{ levelInfo.tier_name }} • Level {{ levelInfo.level }}
            <span v-if="tier >= 41" class="inline-flex items-center">
              <svg class="w-4 h-4 animate-pulse" fill="currentColor" viewBox="0 0 20 20">
                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
              </svg>
            </span>
          </h3>
          <p class="text-xs sm:text-sm" :class="subtitleColor">
            {{ levelInfo.total_points.toLocaleString() }} total points
            <span v-if="tier >= 21" class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium" :class="badgeClasses">
              Elite Tracker
            </span>
          </p>
        </div>
      </div>
      <div v-if="showStats" class="text-right">
        <div class="text-xl sm:text-3xl font-bold" :class="streakColor">
          {{ currentStreak }}
        </div>
        <div class="text-xs" :class="subtitleColor">day streak</div>
      </div>
    </div>

    <!-- Progress bar with tier-specific styling -->
    <div class="space-y-1 relative z-10">
      <div class="flex justify-between text-xs font-medium" :class="progressTextColor">
        <span>Progress to Level {{ levelInfo.level + 1 }}</span>
        <span>{{ levelInfo.progress_percentage }}%</span>
      </div>
      <div class="w-full bg-gray-200/50 rounded-full h-3 overflow-hidden relative" :class="progressBgClass">
        <div
          class="h-3 rounded-full transition-all duration-700 ease-out relative overflow-hidden"
          :class="progressBarClass"
          :style="{
            width: `${levelInfo.progress_percentage}%`,
            backgroundColor: levelInfo.color_theme.primary
          }"
        >
          <!-- Shimmer effect for higher tiers -->
          <div v-if="tier >= 31" class="shimmer-effect"></div>
        </div>
      </div>
      <div class="flex justify-between text-[10px] sm:text-xs" :class="subtitleColor">
        <span>{{ levelInfo.points_into_level.toLocaleString() }} / {{ (levelInfo.points_into_level + levelInfo.points_needed).toLocaleString() }}</span>
        <span class="font-semibold">{{ levelInfo.points_needed.toLocaleString() }} to go</span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  levelInfo: {
    type: Object,
    required: true,
  },
  currentStreak: {
    type: Number,
    default: 0,
  },
  showStats: {
    type: Boolean,
    default: true,
  },
});

const tier = computed(() => props.levelInfo.level);

// Tier-specific classes
const tierClasses = computed(() => {
  if (tier.value >= 51) return 'tier-luminary';
  if (tier.value >= 41) return 'tier-phoenix';
  if (tier.value >= 31) return 'tier-ocean';
  if (tier.value >= 21) return 'tier-mountain';
  if (tier.value >= 11) return 'tier-oak';
  if (tier.value >= 6) return 'tier-sprout';
  return 'tier-seedling';
});

const animationClass = computed(() => {
  if (tier.value >= 51) return 'animate-luminary';
  if (tier.value >= 41) return 'animate-phoenix';
  if (tier.value >= 31) return 'animate-ocean';
  return '';
});

const emojiAnimation = computed(() => {
  if (tier.value >= 41) return 'animate-bounce-slow';
  if (tier.value >= 21) return 'hover-float';
  return '';
});

const titleColor = computed(() => {
  if (tier.value >= 51) return 'text-transparent bg-clip-text bg-gradient-to-r from-yellow-200 via-pink-300 to-purple-300';
  if (tier.value >= 41) return 'text-transparent bg-clip-text bg-gradient-to-r from-yellow-200 via-orange-200 to-red-200';
  if (tier.value >= 31) return 'text-cyan-100';
  if (tier.value >= 21) return 'text-purple-100';
  if (tier.value >= 11) return 'text-emerald-100';
  return 'text-gray-900';
});

const subtitleColor = computed(() => {
  if (tier.value >= 51) return 'text-yellow-100/90';
  if (tier.value >= 41) return 'text-orange-100/90';
  if (tier.value >= 31) return 'text-cyan-200/90';
  if (tier.value >= 21) return 'text-purple-200/90';
  if (tier.value >= 11) return 'text-emerald-200/90';
  return 'text-gray-600';
});

const streakColor = computed(() => {
  if (tier.value >= 51) return 'text-transparent bg-clip-text bg-gradient-to-br from-yellow-200 to-pink-200';
  if (tier.value >= 41) return 'text-yellow-200';
  if (tier.value >= 31) return 'text-cyan-200';
  if (tier.value >= 21) return 'text-purple-200';
  if (tier.value >= 11) return 'text-emerald-200';
  return 'text-green-600';
});

const progressTextColor = computed(() => {
  if (tier.value >= 51) return 'text-yellow-100';
  if (tier.value >= 41) return 'text-orange-100';
  if (tier.value >= 31) return 'text-cyan-100';
  if (tier.value >= 21) return 'text-purple-100';
  if (tier.value >= 11) return 'text-emerald-100';
  return 'text-gray-700';
});

const progressBgClass = computed(() => {
  if (tier.value >= 51) return 'bg-purple-900/40';
  if (tier.value >= 41) return 'bg-red-900/40';
  if (tier.value >= 31) return 'bg-blue-900/40';
  if (tier.value >= 21) return 'bg-purple-900/40';
  if (tier.value >= 11) return 'bg-emerald-900/40';
  return '';
});

const progressBarClass = computed(() => {
  if (tier.value >= 51) return 'progress-luminary';
  if (tier.value >= 41) return 'progress-phoenix';
  if (tier.value >= 31) return 'progress-ocean';
  if (tier.value >= 21) return 'progress-mountain';
  if (tier.value >= 11) return 'progress-oak';
  return '';
});

const badgeClasses = computed(() => {
  if (tier.value >= 51) return 'bg-gradient-to-r from-yellow-300 to-pink-400 text-purple-900 font-bold';
  if (tier.value >= 41) return 'bg-gradient-to-r from-yellow-300 to-orange-400 text-red-900 font-bold';
  if (tier.value >= 31) return 'bg-cyan-200 text-blue-900 font-bold';
  if (tier.value >= 21) return 'bg-purple-200 text-purple-900 font-bold';
  return 'bg-emerald-200 text-emerald-900 font-bold';
});
</script>

<style scoped>
.level-display {
  padding: 1rem;
  border-radius: 0.75rem;
  border: 2px solid;
  transition: all 0.3s ease;
  position: relative;
  overflow: hidden;
}

/* Tier-specific backgrounds - OVER THE TOP & DISTINCT */
.tier-seedling {
  background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
  border-color: #86efac;
  box-shadow: 0 2px 8px rgba(134, 239, 172, 0.3);
}

.tier-sprout {
  background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 50%, #6ee7b7 100%);
  border-color: #10b981;
  box-shadow: 0 4px 12px rgba(16, 185, 129, 0.4);
}

.tier-oak {
  background: linear-gradient(135deg, #064e3b 0%, #047857 50%, #059669 100%);
  border-color: #10b981;
  box-shadow: 0 6px 16px rgba(5, 150, 105, 0.5), 0 0 40px rgba(16, 185, 129, 0.2);
}

.tier-mountain {
  background: linear-gradient(135deg, #4c1d95 0%, #5b21b6 25%, #6d28d9 50%, #7c3aed 75%, #8b5cf6 100%);
  border-color: #a78bfa;
  box-shadow: 0 8px 20px rgba(109, 40, 217, 0.6), 0 0 60px rgba(139, 92, 246, 0.3);
}

.tier-ocean {
  background: linear-gradient(135deg, #0c4a6e 0%, #075985 25%, #0369a1 50%, #0284c7 75%, #0ea5e9 100%);
  border-color: #38bdf8;
  box-shadow: 0 10px 24px rgba(3, 105, 161, 0.7), 0 0 80px rgba(14, 165, 233, 0.4);
}

.tier-phoenix {
  background: linear-gradient(135deg, #7f1d1d 0%, #991b1b 15%, #dc2626 30%, #ea580c 50%, #f97316 70%, #fb923c 85%, #fbbf24 100%);
  border-color: #fb923c;
  box-shadow: 0 12px 32px rgba(220, 38, 38, 0.8), 0 0 100px rgba(249, 115, 22, 0.5), inset 0 2px 20px rgba(251, 191, 36, 0.3);
}

.tier-luminary {
  background: linear-gradient(135deg, #7e22ce 0%, #a855f7 15%, #c026d3 30%, #d946ef 45%, #f59e0b 60%, #fbbf24 75%, #fde047 90%, #fef08a 100%);
  border-color: #fbbf24;
  box-shadow: 0 16px 40px rgba(217, 70, 239, 0.9), 0 0 120px rgba(251, 191, 36, 0.6), inset 0 2px 30px rgba(254, 240, 138, 0.4);
}

/* Animations */
@keyframes float {
  0%, 100% { transform: translateY(0px); }
  50% { transform: translateY(-10px); }
}

@keyframes bounce-slow {
  0%, 100% { transform: translateY(0); }
  50% { transform: translateY(-5px); }
}

@keyframes shimmer {
  0% { transform: translateX(-100%); }
  100% { transform: translateX(100%); }
}

.animate-luminary {
  animation: float 3s ease-in-out infinite;
}

.animate-phoenix {
  animation: float 2.5s ease-in-out infinite;
}

.animate-ocean {
  animation: float 4s ease-in-out infinite;
}

.animate-bounce-slow {
  animation: bounce-slow 2s ease-in-out infinite;
}

.hover-float:hover {
  animation: float 1s ease-in-out infinite;
}

.shimmer-effect {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
  animation: shimmer 2s infinite;
}

/* Decorative elements - ENHANCED */
.phoenix-flames {
  position: absolute;
  top: -20px;
  right: -20px;
  width: 100px;
  height: 100px;
  background: radial-gradient(circle at 30% 30%, rgba(251, 191, 36, 0.8) 0%, rgba(249, 115, 22, 0.6) 25%, rgba(220, 38, 38, 0.3) 50%, transparent 75%);
  border-radius: 50%;
  animation: pulse-intense 2s ease-in-out infinite;
  filter: blur(8px);
}

.phoenix-flames::after {
  content: '';
  position: absolute;
  top: 50%;
  left: 50%;
  width: 60%;
  height: 60%;
  background: radial-gradient(circle, rgba(251, 191, 36, 0.9) 0%, transparent 70%);
  border-radius: 50%;
  transform: translate(-50%, -50%);
  animation: pulse-intense 1.5s ease-in-out infinite reverse;
}

.luminary-stars {
  position: absolute;
  top: -10px;
  right: -10px;
  width: 80px;
  height: 80px;
  background: radial-gradient(circle at 40% 40%, rgba(254, 240, 138, 0.9) 0%, rgba(251, 191, 36, 0.7) 30%, rgba(217, 70, 239, 0.5) 60%, transparent 80%);
  border-radius: 50%;
  animation: pulse-glow 1.5s ease-in-out infinite;
  filter: blur(10px);
}

.luminary-stars::after {
  content: '';
  position: absolute;
  top: 50%;
  left: 50%;
  width: 50%;
  height: 50%;
  background: radial-gradient(circle, rgba(254, 240, 138, 1) 0%, rgba(217, 70, 239, 0.8) 50%, transparent 70%);
  border-radius: 50%;
  transform: translate(-50%, -50%);
  animation: pulse-glow 1.2s ease-in-out infinite reverse;
}

.ocean-waves {
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  height: 60px;
  background: linear-gradient(90deg, rgba(56, 189, 248, 0.2), rgba(14, 165, 233, 0.3), rgba(56, 189, 248, 0.2));
  animation: wave-flow 3s ease-in-out infinite;
  filter: blur(4px);
}

.ocean-waves::after {
  content: '';
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  height: 30px;
  background: linear-gradient(90deg, transparent, rgba(56, 189, 248, 0.4), transparent);
  animation: wave-flow 2s ease-in-out infinite reverse;
}

.mountain-peaks {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 50px;
  background: linear-gradient(90deg, rgba(167, 139, 250, 0.2), rgba(139, 92, 246, 0.3), rgba(167, 139, 250, 0.2));
  animation: wave-flow 4s ease-in-out infinite reverse;
  filter: blur(4px);
}

.mountain-peaks::after {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 25px;
  background: linear-gradient(90deg, transparent, rgba(167, 139, 250, 0.4), transparent);
  animation: wave-flow 3s ease-in-out infinite;
}

@keyframes wave {
  0%, 100% { transform: translateX(0); }
  50% { transform: translateX(10px); }
}

@keyframes wave-flow {
  0%, 100% { transform: translateX(0) scale(1); opacity: 0.7; }
  50% { transform: translateX(20px) scale(1.05); opacity: 1; }
}

@keyframes pulse {
  0%, 100% { opacity: 0.5; transform: scale(1); }
  50% { opacity: 1; transform: scale(1.1); }
}

@keyframes pulse-intense {
  0%, 100% { opacity: 0.6; transform: scale(1); }
  50% { opacity: 1; transform: scale(1.2); }
}

@keyframes pulse-glow {
  0%, 100% { opacity: 0.7; transform: scale(1) rotate(0deg); }
  50% { opacity: 1; transform: scale(1.15) rotate(5deg); }
}

/* Progress bar effects - ENHANCED */
.progress-oak::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(16, 185, 129, 0.6), transparent);
  animation: shimmer 3s infinite;
}

.progress-mountain::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(167, 139, 250, 0.6), transparent);
  animation: shimmer 2.5s infinite;
}

.progress-ocean::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(56, 189, 248, 0.7), transparent);
  animation: shimmer 2.2s infinite;
}

.progress-phoenix::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(251, 191, 36, 0.8), transparent);
  animation: shimmer 1.8s infinite;
}

.progress-luminary::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(254, 240, 138, 0.9), transparent);
  animation: shimmer 1.5s infinite;
}

@keyframes gradient-shift {
  0%, 100% { background-position: 0% 50%; }
  50% { background-position: 100% 50%; }
}

@media (max-width: 640px) {
  .level-display {
    padding: 0.75rem;
  }

  .phoenix-flames,
  .luminary-stars {
    width: 40px;
    height: 40px;
  }
}
</style>
