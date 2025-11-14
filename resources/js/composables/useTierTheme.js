import { computed } from 'vue';

/**
 * Tier configuration with all styling data
 * Single source of truth for tier-based theming
 * Progression: Colorless → Vibrant (Gray → Silver → Blue → Gold → Purple → Cosmic)
 */
const TIER_CONFIG = {
  absolute: {
    minLevel: 85,
    name: 'Absolute',
    colors: {
      name: 'text-white drop-shadow-[0_2px_4px_rgba(0,0,0,0.8)]',
      username: 'text-white/95 drop-shadow-[0_1px_3px_rgba(0,0,0,0.7)]',
      bio: 'text-white/90 drop-shadow-[0_1px_2px_rgba(0,0,0,0.6)]',
      streak: 'text-white drop-shadow-[0_1px_3px_rgba(0,0,0,0.7)]',
      tierBadge: 'bg-gradient-to-r from-yellow-300 via-fuchsia-300 to-purple-400 text-purple-900',
      editButton: 'bg-white/10 border-fuchsia-300/50 text-fuchsia-100 hover:bg-fuchsia-400/20 hover:border-fuchsia-200',
      progressBg: 'bg-gradient-to-r from-purple-900/40 to-yellow-900/40',
      activityGridBg: 'bg-gray-50',
      activityGridBorder: 'border-gray-200',
      activityGridText: 'text-gray-900',
      activityGridLabel: 'text-gray-600',
    },
    progressClass: 'progress-absolute',
    avatarClass: 'avatar-absolute',
    emojiAnimation: 'animate-bounce-slow shimmer-effect',
    showGlow: true,
    showShimmer: true,
    decorations: ['absolute-aura', 'cosmic-sparkles'],
    cardEffect: 'shadow-absolute glow-absolute',
  },
  omnipotent: {
    minLevel: 70,
    name: 'Omnipotent',
    colors: {
      name: 'text-white drop-shadow-[0_2px_4px_rgba(0,0,0,0.8)]',
      username: 'text-white/95 drop-shadow-[0_1px_3px_rgba(0,0,0,0.7)]',
      bio: 'text-white/90 drop-shadow-[0_1px_2px_rgba(0,0,0,0.6)]',
      streak: 'text-white drop-shadow-[0_1px_3px_rgba(0,0,0,0.7)]',
      tierBadge: 'bg-gradient-to-r from-purple-300 via-fuchsia-400 to-amber-400 text-purple-900',
      editButton: 'bg-white/10 border-purple-300/50 text-purple-100 hover:bg-purple-400/20 hover:border-purple-200',
      progressBg: 'bg-purple-950/40',
      activityGridBg: 'bg-gray-50',
      activityGridBorder: 'border-gray-200',
      activityGridText: 'text-gray-900',
      activityGridLabel: 'text-gray-600',
    },
    progressClass: 'progress-omnipotent',
    avatarClass: 'avatar-omnipotent',
    emojiAnimation: 'animate-bounce-slow pulse-glow-effect',
    showGlow: true,
    showShimmer: true,
    decorations: ['omnipotent-crown', 'royal-particles'],
    cardEffect: 'shadow-omnipotent glow-omnipotent',
  },
  eternal: {
    minLevel: 57,
    name: 'Eternal',
    colors: {
      name: 'text-white drop-shadow-[0_2px_4px_rgba(0,0,0,0.8)]',
      username: 'text-white/95 drop-shadow-[0_1px_3px_rgba(0,0,0,0.7)]',
      bio: 'text-white/90 drop-shadow-[0_1px_2px_rgba(0,0,0,0.6)]',
      streak: 'text-white drop-shadow-[0_1px_3px_rgba(0,0,0,0.7)]',
      tierBadge: 'bg-gradient-to-r from-amber-300 to-yellow-400 text-amber-900',
      editButton: 'bg-white/10 border-amber-300/50 text-amber-100 hover:bg-amber-400/20 hover:border-amber-200',
      progressBg: 'bg-amber-950/40',
      activityGridBg: 'bg-gray-50',
      activityGridBorder: 'border-gray-200',
      activityGridText: 'text-gray-900',
      activityGridLabel: 'text-gray-600',
    },
    progressClass: 'progress-eternal',
    avatarClass: 'avatar-eternal',
    emojiAnimation: 'animate-bounce-slow sparkle-rotate',
    showGlow: true,
    showShimmer: true,
    decorations: ['eternal-infinity', 'time-particles'],
    cardEffect: 'shadow-eternal glow-eternal',
  },
  immortal: {
    minLevel: 46,
    name: 'Immortal',
    colors: {
      name: 'text-white drop-shadow-[0_2px_4px_rgba(0,0,0,0.8)]',
      username: 'text-white/95 drop-shadow-[0_1px_3px_rgba(0,0,0,0.7)]',
      bio: 'text-white/90 drop-shadow-[0_1px_2px_rgba(0,0,0,0.6)]',
      streak: 'text-white drop-shadow-[0_1px_3px_rgba(0,0,0,0.7)]',
      tierBadge: 'bg-gradient-to-r from-cyan-300 to-sky-400 text-cyan-900',
      editButton: 'bg-white/10 border-cyan-300/50 text-cyan-100 hover:bg-cyan-400/20 hover:border-cyan-200',
      progressBg: 'bg-cyan-950/40',
      activityGridBg: 'bg-gray-50',
      activityGridBorder: 'border-gray-200',
      activityGridText: 'text-gray-900',
      activityGridLabel: 'text-gray-600',
    },
    progressClass: 'progress-immortal',
    avatarClass: 'avatar-immortal',
    emojiAnimation: 'animate-bounce-slow sparkle-rotate',
    showGlow: true,
    showShimmer: true,
    decorations: ['diamond-sparkles', 'crystal-shards'],
    cardEffect: 'shadow-immortal glow-immortal',
  },
  invincible: {
    minLevel: 36,
    name: 'Invincible',
    colors: {
      name: 'text-white drop-shadow-[0_2px_4px_rgba(0,0,0,0.8)]',
      username: 'text-white/95 drop-shadow-[0_1px_3px_rgba(0,0,0,0.7)]',
      bio: 'text-white/90 drop-shadow-[0_1px_2px_rgba(0,0,0,0.6)]',
      streak: 'text-white drop-shadow-[0_1px_3px_rgba(0,0,0,0.7)]',
      tierBadge: 'bg-gradient-to-r from-violet-300 to-purple-400 text-violet-900',
      editButton: 'bg-white/10 border-violet-300/50 text-violet-100 hover:bg-violet-400/20 hover:border-violet-200',
      progressBg: 'bg-violet-950/40',
      activityGridBg: 'bg-gray-50',
      activityGridBorder: 'border-gray-200',
      activityGridText: 'text-gray-900',
      activityGridLabel: 'text-gray-600',
    },
    progressClass: 'progress-invincible',
    avatarClass: 'avatar-invincible',
    emojiAnimation: 'animate-bounce-slow pulse-glow-effect',
    showGlow: true,
    showShimmer: true,
    decorations: ['invincible-energy', 'lightning-strikes'],
    cardEffect: 'shadow-invincible glow-invincible',
  },
  indomitable: {
    minLevel: 28,
    name: 'Indomitable',
    colors: {
      name: 'text-white drop-shadow-[0_2px_4px_rgba(0,0,0,0.8)]',
      username: 'text-white/95 drop-shadow-[0_1px_3px_rgba(0,0,0,0.7)]',
      bio: 'text-white/90 drop-shadow-[0_1px_2px_rgba(0,0,0,0.6)]',
      streak: 'text-white drop-shadow-[0_1px_3px_rgba(0,0,0,0.7)]',
      tierBadge: 'bg-gradient-to-r from-blue-300 to-indigo-400 text-blue-900',
      editButton: 'bg-white/10 border-blue-300/40 text-blue-100 hover:bg-blue-400/20 hover:border-blue-200',
      progressBg: 'bg-blue-950/40',
      activityGridBg: 'bg-gray-50',
      activityGridBorder: 'border-gray-200',
      activityGridText: 'text-gray-900',
      activityGridLabel: 'text-gray-600',
    },
    progressClass: 'progress-indomitable',
    avatarClass: 'avatar-indomitable',
    emojiAnimation: 'hover-float',
    showGlow: false,
    showShimmer: true,
    decorations: ['indomitable-shield', 'force-field'],
    cardEffect: 'shadow-indomitable',
  },
  formidable: {
    minLevel: 21,
    name: 'Formidable',
    colors: {
      name: 'text-amber-900',
      username: 'text-amber-700',
      bio: 'text-amber-700/90',
      streak: 'text-amber-600',
      tierBadge: 'bg-gradient-to-r from-amber-200 to-yellow-300 text-amber-900',
      editButton: 'bg-white border-amber-300 text-amber-700 hover:bg-amber-50',
      progressBg: 'bg-amber-100/50',
      activityGridBg: 'bg-gray-50',
      activityGridBorder: 'border-gray-200',
      activityGridText: 'text-gray-900',
      activityGridLabel: 'text-gray-600',
    },
    progressClass: '',
    avatarClass: 'avatar-formidable',
    emojiAnimation: '',
    showGlow: false,
    showShimmer: false,
    decorations: [],
    cardEffect: 'shadow-formidable',
  },
  unwavering: {
    minLevel: 15,
    name: 'Unwavering',
    colors: {
      name: 'text-sky-900',
      username: 'text-sky-700',
      bio: 'text-sky-700/90',
      streak: 'text-sky-600',
      tierBadge: 'bg-gradient-to-r from-sky-200 to-blue-300 text-sky-900',
      editButton: 'bg-white border-sky-300 text-sky-700 hover:bg-sky-50',
      progressBg: 'bg-sky-100/50',
      activityGridBg: 'bg-gray-50',
      activityGridBorder: 'border-gray-200',
      activityGridText: 'text-gray-900',
      activityGridLabel: 'text-gray-600',
    },
    progressClass: '',
    avatarClass: 'avatar-unwavering',
    emojiAnimation: '',
    showGlow: false,
    showShimmer: false,
    decorations: [],
    cardEffect: 'shadow-unwavering',
  },
  relentless: {
    minLevel: 10,
    name: 'Relentless',
    colors: {
      name: 'text-gray-900',
      username: 'text-slate-600',
      bio: 'text-slate-600',
      streak: 'text-slate-600',
      tierBadge: 'bg-gradient-to-r from-slate-100 to-slate-200 text-slate-800',
      editButton: 'bg-white border-gray-300 text-gray-700 hover:bg-gray-50',
      progressBg: 'bg-gray-200/50',
      activityGridBg: 'bg-gray-50',
      activityGridBorder: 'border-gray-200',
      activityGridText: 'text-gray-900',
      activityGridLabel: 'text-gray-600',
    },
    progressClass: '',
    avatarClass: 'avatar-relentless',
    emojiAnimation: '',
    showGlow: false,
    showShimmer: false,
    decorations: [],
    cardEffect: 'shadow-relentless',
  },
  devoted: {
    minLevel: 6,
    name: 'Devoted',
    colors: {
      name: 'text-gray-900',
      username: 'text-gray-600',
      bio: 'text-gray-600',
      streak: 'text-gray-600',
      tierBadge: 'bg-gradient-to-r from-gray-100 to-zinc-200 text-gray-800',
      editButton: 'bg-white border-gray-300 text-gray-700 hover:bg-gray-50',
      progressBg: 'bg-gray-200/50',
      activityGridBg: 'bg-gray-50',
      activityGridBorder: 'border-gray-200',
      activityGridText: 'text-gray-900',
      activityGridLabel: 'text-gray-600',
    },
    progressClass: '',
    avatarClass: 'avatar-default',
    emojiAnimation: '',
    showGlow: false,
    showShimmer: false,
    decorations: [],
    cardEffect: 'shadow-devoted',
  },
  committed: {
    minLevel: 3,
    name: 'Committed',
    colors: {
      name: 'text-gray-900',
      username: 'text-gray-600',
      bio: 'text-gray-600',
      streak: 'text-gray-600',
      tierBadge: 'bg-gradient-to-r from-gray-100 to-gray-200 text-gray-800',
      editButton: 'bg-white border-gray-300 text-gray-700 hover:bg-gray-50',
      progressBg: 'bg-gray-200/50',
      activityGridBg: 'bg-gray-50',
      activityGridBorder: 'border-gray-200',
      activityGridText: 'text-gray-900',
      activityGridLabel: 'text-gray-600',
    },
    progressClass: '',
    avatarClass: 'avatar-default',
    emojiAnimation: '',
    showGlow: false,
    showShimmer: false,
    decorations: [],
    cardEffect: 'shadow-committed',
  },
  awakened: {
    minLevel: 1,
    name: 'Awakened',
    colors: {
      name: 'text-gray-900',
      username: 'text-gray-600',
      bio: 'text-gray-600',
      streak: 'text-gray-600',
      tierBadge: 'bg-gradient-to-r from-gray-100 to-gray-200 text-gray-800',
      editButton: 'bg-white border-gray-300 text-gray-700 hover:bg-gray-50',
      progressBg: 'bg-gray-200/50',
      activityGridBg: 'bg-gray-50',
      activityGridBorder: 'border-gray-200',
      activityGridText: 'text-gray-900',
      activityGridLabel: 'text-gray-600',
    },
    progressClass: '',
    avatarClass: 'avatar-default',
    emojiAnimation: '',
    showGlow: false,
    showShimmer: false,
    decorations: [],
    cardEffect: 'shadow-awakened',
  },
};

/**
 * Get tier configuration for a given level
 */
function getTierConfig(level) {
  const tiers = Object.values(TIER_CONFIG).sort((a, b) => b.minLevel - a.minLevel);
  return tiers.find(tier => level >= tier.minLevel) || TIER_CONFIG.awakened;
}

/**
 * Get tier key name for a given level
 */
function getTierKey(level) {
  const config = getTierConfig(level);
  return Object.keys(TIER_CONFIG).find(
    key => TIER_CONFIG[key].minLevel === config.minLevel
  ) || 'awakened';
}

/**
 * Composable for tier-based theming
 * Centralizes all tier styling logic
 */
export function useTierTheme(levelInfo) {
  const tier = computed(() => levelInfo.value.level);
  const config = computed(() => getTierConfig(tier.value));
  const tierKey = computed(() => getTierKey(tier.value));

  // Header styling
  const headerClasses = computed(() => {
    const base = 'rounded-xl p-4 sm:p-6 border-2 transition-all duration-300 relative overflow-hidden';
    return `${base} tier-${tierKey.value} ${config.value.cardEffect}`;
  });

  // All color-based classes
  const nameColor = computed(() => config.value.colors.name);
  const usernameColor = computed(() => config.value.colors.username);
  const bioColor = computed(() => config.value.colors.bio);
  const streakColor = computed(() => config.value.colors.streak);
  const tierBadgeClasses = computed(() => config.value.colors.tierBadge);
  const editButtonClasses = computed(() => config.value.colors.editButton);
  const progressBgClass = computed(() => config.value.colors.progressBg);

  // Activity grid styling
  const activityGridBg = computed(() => config.value.colors.activityGridBg);
  const activityGridBorder = computed(() => config.value.colors.activityGridBorder);
  const activityGridText = computed(() => config.value.colors.activityGridText);
  const activityGridLabel = computed(() => config.value.colors.activityGridLabel);

  // Feature flags - Always show tier badge for consistent layout
  const showTierBadge = computed(() => true);
  const showProgressShimmer = computed(() => config.value.showShimmer);
  const showAvatarGlow = computed(() => config.value.showGlow);

  // Animation and style classes
  const avatarClasses = computed(() => config.value.avatarClass);
  const progressBarClass = computed(() => config.value.progressClass);
  const emojiAnimation = computed(() => config.value.emojiAnimation);

  // Decorative elements
  const decorations = computed(() => config.value.decorations);

  // Text colors (reused)
  const levelTextColor = computed(() => nameColor.value);
  const tierNameColor = computed(() => usernameColor.value);
  const pointsColor = computed(() => bioColor.value);
  const progressTextColor = computed(() => pointsColor.value);

  return {
    tier,
    tierKey,
    config,
    headerClasses,
    nameColor,
    usernameColor,
    bioColor,
    streakColor,
    tierBadgeClasses,
    editButtonClasses,
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
  };
}
