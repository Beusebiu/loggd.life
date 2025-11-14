<template>
  <div class="level-section">
    <!-- Level & Points -->
    <div class="flex items-center justify-between mb-2">
      <div class="flex items-center gap-2 sm:gap-3 flex-1 min-w-0">
        <div class="flex-1 min-w-0">
          <div class="flex items-center gap-2">
            <h3 class="text-base sm:text-lg font-bold whitespace-nowrap" :class="levelTextColor">
              Level {{ levelInfo.level }}
            </h3>
            <span class="text-xs font-medium truncate" :class="[tierNameColor, { 'opacity-0 invisible': showTierBadge }]">
              {{ levelInfo.tier_name }}
            </span>
          </div>
          <p class="text-xs sm:text-sm" :class="pointsColor">
            {{ levelInfo.total_points.toLocaleString() }} points
          </p>
        </div>
      </div>

      <!-- Streak Display -->
      <div v-if="currentStreak > 0" class="text-right flex-shrink-0">
        <div class="text-2xl sm:text-3xl font-bold" :class="streakColor">
          {{ currentStreak }}
        </div>
        <div class="text-xs" :class="pointsColor">day streak</div>
      </div>
    </div>

    <!-- Progress Bar -->
    <div class="space-y-1">
      <div class="flex justify-between text-xs font-medium" :class="progressTextColor">
        <span>Progress to Level {{ levelInfo.level + 1 }}</span>
        <span>{{ levelInfo.progress_percentage }}%</span>
      </div>
      <div class="progress-bar-container" :class="progressBgClass">
        <div
          class="progress-bar"
          :class="progressBarClass"
          :style="{
            width: `${levelInfo.progress_percentage}%`,
            backgroundColor: levelInfo.color_theme.primary
          }"
        >
          <div v-if="showShimmer" class="progress-shimmer"></div>
        </div>
      </div>
      <div class="flex justify-between text-xs" :class="pointsColor">
        <span>{{ levelInfo.points_into_level.toLocaleString() }} / {{ (levelInfo.points_into_level + levelInfo.points_needed).toLocaleString() }}</span>
        <span class="font-semibold">{{ levelInfo.points_needed.toLocaleString() }} to go</span>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  levelInfo: {
    type: Object,
    required: true,
  },
  currentStreak: {
    type: Number,
    default: 0,
  },
  levelTextColor: {
    type: String,
    default: 'text-gray-900',
  },
  tierNameColor: {
    type: String,
    default: 'text-gray-600',
  },
  pointsColor: {
    type: String,
    default: 'text-gray-600',
  },
  streakColor: {
    type: String,
    default: 'text-green-600',
  },
  progressTextColor: {
    type: String,
    default: 'text-gray-600',
  },
  progressBgClass: {
    type: String,
    default: 'bg-gray-200/50',
  },
  progressBarClass: {
    type: String,
    default: '',
  },
  emojiAnimation: {
    type: String,
    default: '',
  },
  showTierBadge: {
    type: Boolean,
    default: false,
  },
  showShimmer: {
    type: Boolean,
    default: false,
  },
});
</script>

<style scoped>
.progress-bar-container {
  width: 100%;
  border-radius: 9999px;
  height: 0.75rem;
  overflow: hidden;
  position: relative;
}

.progress-bar {
  height: 0.75rem;
  border-radius: 9999px;
  transition: all 0.7s ease-out;
  position: relative;
  overflow: hidden;
}

.progress-shimmer {
  position: absolute;
  inset: 0;
  background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.6), transparent);
  animation: shimmer 2s infinite;
}

.progress-luminary::before,
.progress-titan::before,
.progress-diamond::before,
.progress-phoenix::before,
.progress-ocean::before,
.progress-mountain::before {
  content: '';
  position: absolute;
  inset: 0;
  background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.5), transparent);
  animation: shimmer 2s infinite;
}

@keyframes shimmer {
  0% { transform: translateX(-100%); }
  100% { transform: translateX(100%); }
}

@keyframes bounce-slow {
  0%, 100% { transform: translateY(0); }
  50% { transform: translateY(-8px); }
}

.animate-bounce-slow {
  animation: bounce-slow 2.5s ease-in-out infinite;
}

.hover-float:hover {
  animation: bounce-slow 1.5s ease-in-out infinite;
}

.level-emoji {
  width: 2.25rem;
  height: 2.25rem;
  font-size: 2.25rem;
}

@media (min-width: 640px) {
  .level-emoji {
    width: 3rem;
    height: 3rem;
    font-size: 3rem;
  }
}
</style>
