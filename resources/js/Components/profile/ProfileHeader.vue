<template>
  <div ref="profileCardRef" class="profile-header" :class="headerClasses">
    <!-- Animated background effects -->
    <TierBackgroundEffects :tier="levelInfo.level" />

    <!-- Tier-specific decorative background elements -->
    <div
      v-for="decoration in decorations"
      :key="decoration"
      :class="decoration"
    ></div>

    <div class="relative z-10 space-y-4">
      <!-- Top Section: Avatar + Name + Edit Button -->
      <div class="flex items-start justify-between gap-4">
        <div class="flex items-center gap-3 sm:gap-4 flex-1 min-w-0">
          <!-- Tier-based Avatar -->
          <div ref="avatarRef">
            <ProfileAvatar
              :user-name="userName"
              :avatar-class="avatarClasses"
              :show-glow="showAvatarGlow"
            />
          </div>

          <!-- Name, Username, Bio -->
          <div class="flex-1 min-w-0">
            <div class="flex items-center gap-2 flex-wrap">
              <h1 class="text-xl sm:text-2xl font-bold truncate" :class="nameColor">
                {{ userName }}
              </h1>
              <!-- Tier Badge -->
              <span ref="badgeRef" v-if="showTierBadge" class="tier-badge" :class="tierBadgeClasses">
                {{ levelInfo.tier_name }}
              </span>
            </div>
            <p class="text-sm sm:text-base mt-0.5" :class="usernameColor">
              @{{ username }}
            </p>
            <p v-if="bio" class="text-xs sm:text-sm mt-1.5 line-clamp-2" :class="bioColor">
              {{ bio }}
            </p>
          </div>
        </div>
      </div>

      <!-- Level Progress Section -->
      <ProfileLevelProgress
        :level-info="levelInfo"
        :current-streak="currentStreak"
        :level-text-color="levelTextColor"
        :tier-name-color="tierNameColor"
        :points-color="pointsColor"
        :streak-color="streakColor"
        :progress-text-color="progressTextColor"
        :progress-bg-class="progressBgClass"
        :progress-bar-class="progressBarClass"
        :emoji-animation="emojiAnimation"
        :show-tier-badge="showTierBadge"
        :show-shimmer="showProgressShimmer"
      />

      <!-- Activity Grid Section - Integrated with tier theme -->
      <div
        v-if="activityGridData.length > 0"
        class="activity-grid-section rounded-lg border transition-all duration-300 overflow-hidden"
        :class="[activityGridBg, activityGridBorder]"
      >
        <div class="flex items-center justify-between mb-3 px-4 pt-3 sm:px-4 sm:pt-4">
          <h3 class="text-xs sm:text-sm font-medium flex items-center gap-2 opacity-70" :class="activityGridLabel">
            <span>✦</span>
            Your Consistency
          </h3>
          <div class="text-xs font-medium opacity-60" :class="activityGridLabel">
            {{ activeDaysCount }} active days
          </div>
        </div>
        <ActivityGrid
          :grid-data="activityGridData"
          :color-theme="levelInfo.color_theme"
          :text-color="activityGridText"
          :label-color="activityGridLabel"
          @show-tooltip="handleShowTooltip"
          @hide-tooltip="handleHideTooltip"
        />
      </div>

      <!-- Activity Tooltip -->
      <ActivityTooltip
        :visible="tooltipVisible"
        :day-data="tooltipData"
        :mouse-event="tooltipMouseEvent"
      />
    </div>
  </div>
</template>

<script setup>
import { computed, ref, toRef } from 'vue';
import { Link } from '@inertiajs/vue3';
import { useTierTheme } from '../../composables/useTierTheme';
import { useTierBackgroundEffects } from '../../composables/useTierBackgroundEffects';
import ProfileAvatar from './ProfileAvatar.vue';
import ProfileLevelProgress from './ProfileLevelProgress.vue';
import ActivityGrid from '../gamification/ActivityGrid.vue';
import ActivityTooltip from '../gamification/ActivityTooltip.vue';
import TierBackgroundEffects from '../gamification/TierBackgroundEffects.vue';

const props = defineProps({
  userName: {
    type: String,
    required: true,
  },
  username: {
    type: String,
    required: true,
  },
  bio: {
    type: String,
    default: null,
  },
  levelInfo: {
    type: Object,
    required: true,
  },
  currentStreak: {
    type: Number,
    default: 0,
  },
  isOwnProfile: {
    type: Boolean,
    default: false,
  },
  activityGridData: {
    type: Array,
    default: () => [],
  },
});

// Use tier theme composable for all styling
const levelInfoRef = toRef(props, 'levelInfo');
const {
  headerClasses,
  nameColor,
  usernameColor,
  bioColor,
  streakColor,
  tierBadgeClasses,
  progressBgClass,
  progressBarClass,
  avatarClasses,
  emojiAnimation,
  levelTextColor,
  tierNameColor,
  pointsColor,
  progressTextColor,
  activityGridBg,
  activityGridBorder,
  activityGridText,
  activityGridLabel,
  showTierBadge,
  showProgressShimmer,
  showAvatarGlow,
  decorations,
} = useTierTheme(levelInfoRef);

const activeDaysCount = computed(() => props.activityGridData.filter(d => d.activity_count > 0).length);

// Profile card ref for canvas effects
const profileCardRef = ref(null);

// Initialize canvas background effects for tiers 6+
useTierBackgroundEffects(props.levelInfo.level, profileCardRef);

// Tooltip state
const tooltipVisible = ref(false);
const tooltipData = ref(null);
const tooltipMouseEvent = ref(null);

// Tooltip handlers
const handleShowTooltip = (event, dayData) => {
  tooltipVisible.value = true;
  tooltipData.value = dayData;
  tooltipMouseEvent.value = event;
};

const handleHideTooltip = () => {
  tooltipVisible.value = false;
  tooltipData.value = null;
  tooltipMouseEvent.value = null;
};
</script>

<style scoped>
/* ============================================
   TIER CARD BACKGROUNDS - Progressive Enhancement
   Colorless → Vibrant Progression
   ============================================ */

/* Tier 1: Awakened - Pale & Colorless */
.tier-awakened {
  background: linear-gradient(135deg, #f9fafb 0%, #f3f4f6 100%);
  border-color: #d1d5db;
}

/* Tier 2: Committed - Light Gray */
.tier-committed {
  background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 50%, #f9fafb 100%);
  border-color: #9ca3af;
  box-shadow: 0 2px 8px rgba(156, 163, 175, 0.15);
}

/* Tier 3: Devoted - Silver */
.tier-devoted {
  background: linear-gradient(135deg, #e4e4e7 0%, #d4d4d8 50%, #e4e4e7 100%);
  border-color: #71717a;
  box-shadow: 0 3px 10px rgba(113, 113, 122, 0.2);
}

/* Tier 4: Relentless - Steel Blue-Gray */
.tier-relentless {
  background: linear-gradient(135deg, #e2e8f0 0%, #cbd5e1 50%, #94a3b8 100%);
  border-color: #64748b;
  box-shadow: 0 4px 12px rgba(100, 116, 139, 0.25);
}

/* Tier 5: Unwavering - Steel Blue */
.tier-unwavering {
  background: linear-gradient(135deg, #e0f2fe 0%, #bae6fd 50%, #7dd3fc 100%);
  border-color: #0ea5e9;
  box-shadow: 0 6px 16px rgba(14, 165, 233, 0.35);
}

/* Tier 6: Formidable - Bronze/Gold */
.tier-formidable {
  background: linear-gradient(135deg, #fef3c7 0%, #fde68a 30%, #fcd34d 60%, #fbbf24 100%);
  border-color: #d97706;
  box-shadow: 0 8px 20px rgba(217, 119, 6, 0.4), 0 0 40px rgba(251, 191, 36, 0.2);
}

/* Tier 7: Indomitable - Deep Blue */
.tier-indomitable {
  background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 25%, #2563eb 50%, #3b82f6 75%, #60a5fa 100%);
  border-color: #3b82f6;
  box-shadow: 0 10px 24px rgba(59, 130, 246, 0.5), 0 0 60px rgba(96, 165, 250, 0.3);
}

/* Tier 8: Invincible - Electric Purple */
.tier-invincible {
  background: linear-gradient(135deg, #581c87 0%, #6d28d9 25%, #7c3aed 50%, #8b5cf6 75%, #a78bfa 100%);
  border-color: #8b5cf6;
  box-shadow: 0 12px 28px rgba(139, 92, 246, 0.6), 0 0 80px rgba(167, 139, 250, 0.4);
  animation: glow-pulse-invincible 2.5s ease-in-out infinite;
}

/* Tier 9: Immortal - Diamond/Crystal */
.tier-immortal {
  background: linear-gradient(135deg, #164e63 0%, #0891b2 25%, #06b6d4 50%, #22d3ee 75%, #67e8f9 100%);
  border-color: #06b6d4;
  box-shadow: 0 14px 32px rgba(6, 182, 212, 0.7), 0 0 100px rgba(34, 211, 238, 0.5), inset 0 2px 20px rgba(103, 232, 249, 0.3);
  animation: glow-pulse-immortal 2.3s ease-in-out infinite;
}

/* Tier 10: Eternal - Cosmic Gold */
.tier-eternal {
  background: linear-gradient(135deg, #b45309 0%, #d97706 20%, #f59e0b 40%, #fbbf24 60%, #fcd34d 80%, #fde047 100%);
  border-color: #f59e0b;
  box-shadow: 0 16px 36px rgba(245, 158, 11, 0.8), 0 0 120px rgba(251, 191, 36, 0.6), inset 0 2px 30px rgba(253, 224, 71, 0.4);
  animation: glow-pulse-eternal 2.1s ease-in-out infinite;
}

/* Tier 11: Omnipotent - Royal Purple/Gold */
.tier-omnipotent {
  background: linear-gradient(135deg, #6b21a8 0%, #7e22ce 15%, #a855f7 30%, #c084fc 50%, #f59e0b 70%, #fbbf24 85%, #fde047 100%);
  border-color: #a855f7;
  box-shadow: 0 18px 40px rgba(168, 85, 247, 0.9), 0 0 140px rgba(245, 158, 11, 0.7), inset 0 2px 40px rgba(253, 224, 71, 0.5);
  animation: glow-pulse-omnipotent 2s ease-in-out infinite;
}

/* Tier 12: Absolute - Prismatic Everything */
.tier-absolute {
  background: linear-gradient(135deg, #7e22ce 0%, #a855f7 12%, #c026d3 25%, #d946ef 37%, #f59e0b 50%, #fbbf24 62%, #fde047 75%, #fef08a 87%, #fef3c7 100%);
  border-color: #d946ef;
  box-shadow: 0 20px 48px rgba(217, 70, 239, 1), 0 0 180px rgba(251, 191, 36, 0.8), inset 0 2px 50px rgba(254, 240, 138, 0.6);
  animation: glow-pulse-absolute 1.8s ease-in-out infinite;
}

/* ============================================
   GLOW EFFECTS FOR HIGH TIERS
   ============================================ */
.glow-absolute {
  animation: glow-pulse-absolute 1.8s ease-in-out infinite;
}

.glow-omnipotent {
  animation: glow-pulse-omnipotent 2s ease-in-out infinite;
}

.glow-eternal {
  animation: glow-pulse-eternal 2.1s ease-in-out infinite;
}

.glow-immortal {
  animation: glow-pulse-immortal 2.3s ease-in-out infinite;
}

.glow-invincible {
  animation: glow-pulse-invincible 2.5s ease-in-out infinite;
}

@keyframes glow-pulse-absolute {
  0%, 100% {
    box-shadow: 0 20px 48px rgba(217, 70, 239, 1), 0 0 180px rgba(251, 191, 36, 0.8), inset 0 2px 50px rgba(254, 240, 138, 0.6);
  }
  50% {
    box-shadow: 0 25px 60px rgba(217, 70, 239, 1), 0 0 220px rgba(251, 191, 36, 1), inset 0 2px 60px rgba(254, 240, 138, 0.8);
  }
}

@keyframes glow-pulse-omnipotent {
  0%, 100% {
    box-shadow: 0 18px 40px rgba(168, 85, 247, 0.9), 0 0 140px rgba(245, 158, 11, 0.7), inset 0 2px 40px rgba(253, 224, 71, 0.5);
  }
  50% {
    box-shadow: 0 22px 50px rgba(168, 85, 247, 1), 0 0 180px rgba(245, 158, 11, 0.9), inset 0 2px 50px rgba(253, 224, 71, 0.7);
  }
}

@keyframes glow-pulse-eternal {
  0%, 100% {
    box-shadow: 0 16px 36px rgba(245, 158, 11, 0.8), 0 0 120px rgba(251, 191, 36, 0.6), inset 0 2px 30px rgba(253, 224, 71, 0.4);
  }
  50% {
    box-shadow: 0 20px 45px rgba(245, 158, 11, 1), 0 0 160px rgba(251, 191, 36, 0.8), inset 0 2px 40px rgba(253, 224, 71, 0.6);
  }
}

@keyframes glow-pulse-immortal {
  0%, 100% {
    box-shadow: 0 14px 32px rgba(6, 182, 212, 0.7), 0 0 100px rgba(34, 211, 238, 0.5), inset 0 2px 20px rgba(103, 232, 249, 0.3);
  }
  50% {
    box-shadow: 0 18px 42px rgba(6, 182, 212, 0.9), 0 0 140px rgba(34, 211, 238, 0.7), inset 0 2px 30px rgba(103, 232, 249, 0.5);
  }
}

@keyframes glow-pulse-invincible {
  0%, 100% {
    box-shadow: 0 12px 28px rgba(139, 92, 246, 0.6), 0 0 80px rgba(167, 139, 250, 0.4);
  }
  50% {
    box-shadow: 0 16px 36px rgba(139, 92, 246, 0.8), 0 0 120px rgba(167, 139, 250, 0.6);
  }
}

/* Additional Shadow Effects */
.shadow-absolute, .shadow-omnipotent, .shadow-eternal, .shadow-immortal, .shadow-invincible {
  position: relative;
}

.shadow-indomitable {
  filter: drop-shadow(0 0 20px rgba(59, 130, 246, 0.3));
}

.shadow-formidable {
  filter: drop-shadow(0 0 15px rgba(217, 119, 6, 0.25));
}

.shadow-unwavering {
  filter: drop-shadow(0 0 12px rgba(14, 165, 233, 0.2));
}

.shadow-relentless {
  filter: drop-shadow(0 0 8px rgba(100, 116, 139, 0.15));
}

.shadow-devoted, .shadow-committed, .shadow-awakened {
  filter: drop-shadow(0 0 5px rgba(156, 163, 175, 0.1));
}

.shadow-sprout {
  filter: drop-shadow(0 0 6px rgba(16, 185, 129, 0.1));
}

.shadow-seedling {
  filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.05));
}

/* Tier Badge */
.tier-badge {
  display: inline-flex;
  align-items: center;
  gap: 0.25rem;
  padding: 0.125rem 0.5rem;
  border-radius: 9999px;
  font-size: 0.75rem;
  font-weight: bold;
}

.tier-badge-icon {
  width: 1rem;
  height: 1rem;
  font-size: 1rem;
}

/* ============================================
   DECORATIVE BACKGROUND ELEMENTS
   ============================================ */

/* All decorative elements */
.phoenix-flames, .ember-particles,
.ocean-waves, .water-ripples,
.mountain-peaks, .mist-layer,
.diamond-sparkles, .crystal-shards,
.titan-energy, .lightning-strikes,
.luminary-aura, .cosmic-sparkles,
.leaves-falling {
  position: absolute;
  pointer-events: none;
  z-index: 1;
}

/* ===== LUMINARY DECORATIONS ===== */
.luminary-aura {
  top: -30px;
  left: -30px;
  right: -30px;
  bottom: -30px;
  background: radial-gradient(circle at 50% 50%,
    rgba(251, 191, 36, 0.3) 0%,
    rgba(217, 70, 239, 0.2) 30%,
    rgba(168, 85, 247, 0.1) 60%,
    transparent 100%);
  animation: luminary-pulse 4s ease-in-out infinite;
  filter: blur(40px);
}

.cosmic-sparkles {
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background:
    radial-gradient(circle at 20% 30%, rgba(251, 191, 36, 0.8) 0%, transparent 2%),
    radial-gradient(circle at 80% 20%, rgba(217, 70, 239, 0.8) 0%, transparent 2%),
    radial-gradient(circle at 40% 70%, rgba(34, 211, 238, 0.8) 0%, transparent 2%),
    radial-gradient(circle at 90% 60%, rgba(251, 191, 36, 0.8) 0%, transparent 2%),
    radial-gradient(circle at 10% 80%, rgba(217, 70, 239, 0.8) 0%, transparent 2%);
  animation: sparkle-twinkle 3s ease-in-out infinite;
}

@keyframes luminary-pulse {
  0%, 100% { opacity: 0.6; transform: scale(1) rotate(0deg); }
  50% { opacity: 1; transform: scale(1.1) rotate(5deg); }
}

@keyframes sparkle-twinkle {
  0%, 100% { opacity: 0.6; }
  50% { opacity: 1; }
}

/* ===== TITAN DECORATIONS ===== */
.titan-energy {
  top: -20px;
  left: -20px;
  right: -20px;
  bottom: -20px;
  background: radial-gradient(circle at 50% 50%,
    rgba(167, 139, 250, 0.4) 0%,
    rgba(109, 40, 217, 0.3) 40%,
    transparent 80%);
  animation: energy-pulse 2.5s ease-in-out infinite;
  filter: blur(30px);
}

.lightning-strikes {
  top: 10%;
  left: 10%;
  right: 10%;
  bottom: 10%;
  background:
    linear-gradient(45deg, transparent 48%, rgba(196, 181, 253, 0.9) 49%, rgba(196, 181, 253, 0.9) 51%, transparent 52%),
    linear-gradient(-45deg, transparent 48%, rgba(167, 139, 250, 0.8) 49%, rgba(167, 139, 250, 0.8) 51%, transparent 52%);
  background-size: 200% 200%;
  animation: lightning-flash 3s ease-in-out infinite;
  opacity: 0;
}

@keyframes energy-pulse {
  0%, 100% { opacity: 0.7; transform: scale(1); }
  50% { opacity: 1; transform: scale(1.15); }
}

@keyframes lightning-flash {
  0%, 90%, 100% { opacity: 0; }
  93%, 97% { opacity: 0.6; }
  95% { opacity: 0.9; }
}

/* ===== DIAMOND DECORATIONS ===== */
.diamond-sparkles {
  top: -15px;
  right: -15px;
  width: 150px;
  height: 150px;
  background: radial-gradient(circle at 40% 40%, rgba(34, 211, 238, 0.9) 0%, rgba(6, 182, 212, 0.6) 30%, transparent 70%);
  border-radius: 50%;
  animation: diamond-rotate 4s linear infinite;
  filter: blur(20px);
}

.crystal-shards {
  bottom: -10px;
  left: -10px;
  width: 100px;
  height: 100px;
  background: linear-gradient(135deg, rgba(34, 211, 238, 0.4) 0%, transparent 100%);
  clip-path: polygon(50% 0%, 100% 50%, 50% 100%, 0% 50%);
  animation: crystal-spin 6s ease-in-out infinite;
  filter: blur(8px);
}

@keyframes diamond-rotate {
  0% { transform: rotate(0deg) scale(1); opacity: 0.7; }
  50% { transform: rotate(180deg) scale(1.1); opacity: 1; }
  100% { transform: rotate(360deg) scale(1); opacity: 0.7; }
}

@keyframes crystal-spin {
  0%, 100% { transform: rotate(0deg); }
  50% { transform: rotate(180deg); }
}

/* ===== PHOENIX DECORATIONS ===== */
.phoenix-flames {
  top: -25px;
  right: -25px;
  width: 140px;
  height: 140px;
  background: radial-gradient(circle at 30% 30%, rgba(251, 191, 36, 0.7) 0%, rgba(249, 115, 22, 0.5) 25%, rgba(220, 38, 38, 0.3) 50%, transparent 75%);
  border-radius: 50%;
  animation: flame-dance 2s ease-in-out infinite;
  filter: blur(15px);
}

.ember-particles {
  bottom: 0;
  left: 0;
  right: 0;
  height: 100px;
  background:
    radial-gradient(circle at 20% 80%, rgba(251, 191, 36, 0.7) 0%, transparent 10%),
    radial-gradient(circle at 50% 90%, rgba(249, 115, 22, 0.6) 0%, transparent 8%),
    radial-gradient(circle at 80% 85%, rgba(220, 38, 38, 0.7) 0%, transparent 12%);
  animation: embers-rise 3s ease-in-out infinite;
  filter: blur(6px);
}

@keyframes flame-dance {
  0%, 100% { opacity: 0.6; transform: scale(1) translateY(0); }
  25% { opacity: 0.9; transform: scale(1.15) translateY(-5px); }
  50% { opacity: 1; transform: scale(1.2) translateY(-8px); }
  75% { opacity: 0.8; transform: scale(1.1) translateY(-3px); }
}

@keyframes embers-rise {
  0% { transform: translateY(0); opacity: 0.7; }
  100% { transform: translateY(-30px); opacity: 0; }
}

/* ===== OCEAN DECORATIONS ===== */
.ocean-waves {
  bottom: 0;
  left: 0;
  right: 0;
  height: 90px;
  background: linear-gradient(90deg, rgba(56, 189, 248, 0.3), rgba(14, 165, 233, 0.4), rgba(56, 189, 248, 0.3));
  animation: wave-flow 3.5s ease-in-out infinite;
  filter: blur(8px);
}

.water-ripples {
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background:
    radial-gradient(ellipse at 30% 40%, rgba(14, 165, 233, 0.2) 0%, transparent 50%),
    radial-gradient(ellipse at 70% 60%, rgba(34, 211, 238, 0.2) 0%, transparent 50%);
  animation: ripple-expand 4s ease-in-out infinite;
  filter: blur(20px);
}

@keyframes wave-flow {
  0%, 100% { transform: translateX(0) scaleY(1); opacity: 0.7; }
  50% { transform: translateX(30px) scaleY(1.1); opacity: 1; }
}

@keyframes ripple-expand {
  0%, 100% { opacity: 0.4; transform: scale(1); }
  50% { opacity: 0.8; transform: scale(1.05); }
}

/* ===== MOUNTAIN DECORATIONS ===== */
.mountain-peaks {
  top: 0;
  left: 0;
  right: 0;
  height: 70px;
  background: linear-gradient(90deg, rgba(167, 139, 250, 0.3), rgba(139, 92, 246, 0.4), rgba(167, 139, 250, 0.3));
  clip-path: polygon(0 100%, 20% 40%, 40% 60%, 60% 30%, 80% 50%, 100% 100%);
  animation: peaks-shimmer 4s ease-in-out infinite;
  filter: blur(6px);
}

.mist-layer {
  bottom: 0;
  left: 0;
  right: 0;
  height: 60px;
  background: linear-gradient(to top, rgba(167, 139, 250, 0.3) 0%, transparent 100%);
  animation: mist-drift 5s ease-in-out infinite;
  filter: blur(15px);
}

@keyframes peaks-shimmer {
  0%, 100% { opacity: 0.6; }
  50% { opacity: 1; }
}

@keyframes mist-drift {
  0%, 100% { transform: translateX(0); opacity: 0.5; }
  50% { transform: translateX(20px); opacity: 0.8; }
}

/* ===== OAK DECORATIONS ===== */
.leaves-falling {
  top: -10px;
  left: 0;
  right: 0;
  height: 100%;
  background:
    radial-gradient(circle at 15% 20%, rgba(16, 185, 129, 0.3) 0%, transparent 5%),
    radial-gradient(circle at 85% 40%, rgba(5, 150, 105, 0.3) 0%, transparent 5%),
    radial-gradient(circle at 45% 60%, rgba(16, 185, 129, 0.3) 0%, transparent 5%);
  animation: leaves-fall 8s ease-in-out infinite;
  filter: blur(4px);
}

@keyframes leaves-fall {
  0% { transform: translateY(-20px); opacity: 0; }
  20% { opacity: 0.6; }
  100% { transform: translateY(100px); opacity: 0; }
}

/* ============================================
   EMOJI ANIMATIONS
   ============================================ */
.shimmer-effect {
  animation: shimmer-glow 2s ease-in-out infinite;
}

.pulse-glow-effect {
  animation: pulse-glow-emoji 2.5s ease-in-out infinite;
}

.sparkle-rotate {
  animation: sparkle-spin 3s ease-in-out infinite;
}

.fire-pulse {
  animation: fire-beat 1.8s ease-in-out infinite;
}

.wave-float {
  animation: wave-bob 3s ease-in-out infinite;
}

@keyframes shimmer-glow {
  0%, 100% { filter: brightness(1) drop-shadow(0 0 8px rgba(251, 191, 36, 0.6)); }
  50% { filter: brightness(1.3) drop-shadow(0 0 16px rgba(251, 191, 36, 1)); }
}

@keyframes pulse-glow-emoji {
  0%, 100% { transform: scale(1); filter: drop-shadow(0 0 8px rgba(167, 139, 250, 0.6)); }
  50% { transform: scale(1.15); filter: drop-shadow(0 0 16px rgba(167, 139, 250, 1)); }
}

@keyframes sparkle-spin {
  0%, 100% { transform: rotate(-5deg) scale(1); filter: drop-shadow(0 0 8px rgba(34, 211, 238, 0.6)); }
  50% { transform: rotate(5deg) scale(1.1); filter: drop-shadow(0 0 16px rgba(34, 211, 238, 1)); }
}

@keyframes fire-beat {
  0%, 100% { transform: scale(1) translateY(0); filter: drop-shadow(0 0 10px rgba(249, 115, 22, 0.8)); }
  50% { transform: scale(1.15) translateY(-3px); filter: drop-shadow(0 0 18px rgba(249, 115, 22, 1)); }
}

@keyframes wave-bob {
  0%, 100% { transform: translateY(0) rotate(-2deg); }
  50% { transform: translateY(-6px) rotate(2deg); }
}

/* ============================================
   RESPONSIVE & UTILITY
   ============================================ */
@media (max-width: 640px) {
  .profile-header {
    padding: 1rem;
  }

  /* Reduce decoration intensity on mobile */
  .luminary-aura, .titan-energy,
  .diamond-sparkles, .phoenix-flames {
    opacity: 0.7;
  }
}

/* Activity Grid already has tier-based styling from composable */
.activity-grid-section {
  backdrop-filter: blur(10px);
  -webkit-backdrop-filter: blur(10px);
}
</style>
