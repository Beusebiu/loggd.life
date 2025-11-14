<template>
  <component :is="effectComponent" v-if="effectComponent" />
</template>

<script setup>
import { computed } from 'vue';
import FloatingDots from './effects/FloatingDots.vue';
import SnowFall from './effects/SnowFall.vue';
import Fireflies from './effects/Fireflies.vue';
import Confetti from './effects/Confetti.vue';
import GoldenSparkles from './effects/GoldenSparkles.vue';
import EnergyParticles from './effects/EnergyParticles.vue';
import AuroraLights from './effects/AuroraLights.vue';
import ShootingStars from './effects/ShootingStars.vue';

const props = defineProps({
  tier: {
    type: Number,
    required: true,
  },
});

/**
 * MODULAR TIER EFFECT MAPPING
 * Easy to add, edit, remove, or swap effects
 * Just change the component for any tier range
 */
const effectComponent = computed(() => {
  // Tier 1 (1-2): No effect
  if (props.tier < 3) {
    return null;
  }

  // Tier 2 (3-5): Floating Dots
  if (props.tier >= 3 && props.tier < 6) {
    return FloatingDots;
  }

  // Tier 3 (6-9): Snow Fall
  if (props.tier >= 6 && props.tier < 10) {
    return SnowFall;
  }

  // Tier 4 (10-14): Fireflies
  if (props.tier >= 10 && props.tier < 15) {
    return Fireflies;
  }

  // Tier 5 (15-20): Confetti
  if (props.tier >= 15 && props.tier < 21) {
    return Confetti;
  }

  // Tier 6 (21-27): Golden Sparkles - YOUR TIER!
  if (props.tier >= 21 && props.tier < 28) {
    return GoldenSparkles;
  }

  // Tier 7 (28-35): Energy Particles
  if (props.tier >= 28 && props.tier < 36) {
    return EnergyParticles;
  }

  // Tier 8 (36-45): Aurora Lights
  if (props.tier >= 36 && props.tier < 46) {
    return AuroraLights;
  }

  // Tier 9 (46-56): Shooting Stars
  if (props.tier >= 46 && props.tier < 57) {
    return ShootingStars;
  }

  // Tier 10+ (57-100): Energy Particles (can swap for other effects)
  if (props.tier >= 57) {
    return EnergyParticles;
  }

  return null;
});
</script>
