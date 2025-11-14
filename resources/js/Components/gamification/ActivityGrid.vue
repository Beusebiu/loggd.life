<template>
  <div class="px-2 sm:px-4 py-3 sm:py-4">
    <!-- Header with year selector and legend -->
    <div class="mb-2 flex items-center justify-between flex-wrap gap-2">
      <div class="flex items-center gap-3">
        <div class="text-xs font-medium" :class="textColor">
          {{ selectedYear }}
        </div>
        <!-- Year selector (if multiple years available) -->
        <div v-if="availableYears.length > 1" class="flex items-center gap-1">
          <button
            v-for="year in availableYears"
            :key="year"
            @click="changeYear(year)"
            :class="[
              'px-2 py-0.5 text-[10px] font-medium rounded transition-colors',
              year === selectedYear
                ? [textColor, 'bg-white/20 border border-white/30']
                : [labelColor, 'bg-white/10 hover:bg-white/15']
            ]"
          >
            {{ year }}
          </button>
        </div>
      </div>

      <!-- Legend for intensity -->
      <div class="flex items-center gap-2 text-[10px]" :class="labelColor">
        <span>Less</span>
        <div class="flex items-center gap-1">
          <div class="w-3 h-3 rounded-sm border opacity-40" :style="{ backgroundColor: intensityColor(0), borderColor: colorTheme.primary }"></div>
          <div class="w-3 h-3 rounded-sm" :style="{ backgroundColor: intensityColor(1) }"></div>
          <div class="w-3 h-3 rounded-sm" :style="{ backgroundColor: intensityColor(2) }"></div>
          <div class="w-3 h-3 rounded-sm" :style="{ backgroundColor: intensityColor(3) }"></div>
          <div class="w-3 h-3 rounded-sm" :style="{ backgroundColor: intensityColor(4) }"></div>
        </div>
        <span>More</span>
      </div>
    </div>

    <!-- Mobile Grid - FULL BLEED APPROACH -->
    <div class="block sm:hidden">
      <div class="relative w-screen left-1/2 right-1/2 -ml-[50vw] -mr-[50vw] py-3">
        <div ref="mobileScrollContainer" class="overflow-x-auto overflow-y-hidden pb-2 pl-10 pr-8" style="-webkit-overflow-scrolling: touch; scrollbar-width: thin;">
          <div class="inline-flex flex-col gap-2">
            <!-- Month Labels -->
            <div class="flex gap-[1px]">
              <div class="ml-1 mr-2" style="width: 12px;"></div>
              <div
                v-for="(monthLabel, labelIndex) in mobileMonthLabels"
                :key="labelIndex"
                class="text-[9px] font-medium"
                :class="labelColor"
                :style="{ width: monthLabel.width }"
              >
                {{ monthLabel.name }}
              </div>
            </div>

            <!-- Grid with day labels -->
            <div class="flex gap-[1px]">
              <!-- Day labels -->
              <div class="flex flex-col gap-[1px] mr-2 ml-1">
                <div class="h-[8px]"></div>
                <div class="h-[8px] text-[8px] flex items-center leading-none w-3" :class="labelColor">M</div>
                <div class="h-[8px]"></div>
                <div class="h-[8px] text-[8px] flex items-center leading-none w-3" :class="labelColor">W</div>
                <div class="h-[8px]"></div>
                <div class="h-[8px] text-[8px] flex items-center leading-none w-3" :class="labelColor">F</div>
                <div class="h-[8px]"></div>
              </div>

              <!-- Grid -->
              <div class="flex gap-[1px]">
                <div
                  v-for="week in yearGrid"
                  :key="week.start"
                  class="flex flex-col gap-[1px]"
                >
                  <div
                    v-for="day in week.days"
                    :key="day.date || day.index"
                    class="w-[8px] h-[8px] rounded-[2px]"
                    :style="getMobileDayStyle(day)"
                  ></div>
                </div>
              </div>
            </div>

            <!-- Scroll hint -->
            <div class="text-center text-[9px]" :class="labelColor">
              ← Scroll to see full year →
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Desktop Grid -->
    <div class="hidden sm:block">
      <div class="overflow-x-auto overflow-y-hidden">
        <div class="inline-block min-w-max">
          <!-- Month Labels -->
          <div class="flex gap-0.5 mb-1">
            <div class="mr-1" style="width: 28px;"></div>
            <div
              v-for="(monthLabel, labelIndex) in monthLabels"
              :key="labelIndex"
              class="text-[9px]"
              :class="labelColor"
              :style="{ width: monthLabel.width }"
            >
              {{ monthLabel.name }}
            </div>
          </div>

          <!-- Grid with day labels -->
          <div class="flex gap-1 pb-1">
            <!-- Day labels -->
            <div class="flex flex-col gap-0.5 mr-1">
              <div class="h-3"></div>
              <div class="h-3 text-[9px] flex items-center" :class="labelColor">Mon</div>
              <div class="h-3"></div>
              <div class="h-3 text-[9px] flex items-center" :class="labelColor">Wed</div>
              <div class="h-3"></div>
              <div class="h-3 text-[9px] flex items-center" :class="labelColor">Fri</div>
              <div class="h-3"></div>
            </div>

            <!-- Grid -->
            <div class="flex gap-0.5">
              <div
                v-for="week in yearGrid"
                :key="week.start"
                class="flex flex-col gap-0.5"
              >
                <div
                  v-for="day in week.days"
                  :key="day.date || day.index"
                  class="w-3 h-3 rounded-sm transition-all cursor-pointer hover:ring-1 hover:ring-gray-400"
                  :style="getDayStyle(day)"
                  @mouseenter="day.date && $emit('show-tooltip', $event, day)"
                  @mouseleave="$emit('hide-tooltip')"
                ></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, ref, onMounted } from 'vue';

const props = defineProps({
  gridData: {
    type: Array,
    required: true,
  },
  colorTheme: {
    type: Object,
    required: true,
  },
  availableYears: {
    type: Array,
    default: () => [new Date().getFullYear()],
  },
  initialYear: {
    type: Number,
    default: () => new Date().getFullYear(),
  },
  textColor: {
    type: String,
    default: 'text-gray-700',
  },
  labelColor: {
    type: String,
    default: 'text-gray-500',
  },
});

const emit = defineEmits(['show-tooltip', 'hide-tooltip', 'change-year']);

const selectedYear = ref(props.initialYear);
const mobileScrollContainer = ref(null);

onMounted(() => {
  if (mobileScrollContainer.value) {
    mobileScrollContainer.value.scrollLeft = mobileScrollContainer.value.scrollWidth;
  }
});

const changeYear = (year) => {
  selectedYear.value = year;
  emit('change-year', year);
};

// Get color for intensity level with improved contrast
const intensityColor = (intensity) => {
  const theme = props.colorTheme;
  const primaryColor = theme.primary;

  // Helper to convert hex to RGB
  const hexToRgb = (hex) => {
    const result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
    return result ? {
      r: parseInt(result[1], 16),
      g: parseInt(result[2], 16),
      b: parseInt(result[3], 16)
    } : null;
  };

  // Check if this is a vibrant tier (tiers 6-12 use colorful backgrounds)
  // Detect by checking if primary color is vibrant (not gray/neutral)
  let rgb = null;

  if (primaryColor) {
    if (primaryColor.startsWith('#')) {
      rgb = hexToRgb(primaryColor);
    } else if (primaryColor.includes('rgb')) {
      const match = primaryColor.match(/\d+/g);
      if (match && match.length >= 3) {
        rgb = { r: parseInt(match[0]), g: parseInt(match[1]), b: parseInt(match[2]) };
      }
    }
  }

  // Determine if this is a vibrant tier (not gray)
  const isVibrantTier = rgb && !(
    Math.abs(rgb.r - rgb.g) < 20 && Math.abs(rgb.g - rgb.b) < 20 && Math.abs(rgb.r - rgb.b) < 20
  );

  if (isVibrantTier && rgb) {
    // For vibrant tiers with light backgrounds: use color variety like habits grid
    const colors = [
      'rgba(0, 0, 0, 0.02)',                     // Empty - barely visible
      `rgba(${rgb.r}, ${rgb.g}, ${rgb.b}, 0.15)`,  // Level 1: Light tint
      `rgba(${rgb.r}, ${rgb.g}, ${rgb.b}, 0.35)`,  // Level 2: Medium tint
      `rgba(${rgb.r}, ${rgb.g}, ${rgb.b}, 0.60)`,  // Level 3: Strong color
      `rgba(${rgb.r}, ${rgb.g}, ${rgb.b}, 0.85)`,  // Level 4: Deep/saturated
    ];
    return colors[intensity] || colors[0];
  } else {
    // For light tier backgrounds: use dark overlays
    const colors = [
      'rgba(0, 0, 0, 0.03)',   // Empty
      'rgba(0, 0, 0, 0.06)',   // Level 1: Very light
      'rgba(0, 0, 0, 0.12)',   // Level 2: Light
      'rgba(0, 0, 0, 0.25)',   // Level 3: Medium
      'rgba(0, 0, 0, 0.45)',   // Level 4: Dark
    ];
    return colors[intensity] || colors[1];
  }
};

// Generate month labels (desktop)
const monthLabels = computed(() => {
  const labels = [];
  if (props.gridData.length === 0) return labels;

  const startDate = new Date(selectedYear.value, 0, 1);
  const gridStartDate = new Date(startDate);
  const dayOfWeek = gridStartDate.getDay();
  if (dayOfWeek !== 0) {
    gridStartDate.setDate(gridStartDate.getDate() - dayOfWeek);
  }

  let currentMonth = -1;
  let monthWeekCount = 0;

  for (let i = 0; i < 53; i++) {
    const weekDate = new Date(gridStartDate);
    weekDate.setDate(weekDate.getDate() + i * 7 + 3);
    const weekYear = weekDate.getFullYear();
    const month = weekDate.getMonth();

    if (weekYear !== selectedYear.value) continue;

    if (month !== currentMonth) {
      if (monthWeekCount > 0 && currentMonth >= 0) {
        labels.push({
          name: new Date(selectedYear.value, currentMonth).toLocaleDateString('en-US', { month: 'short' }),
          width: `${monthWeekCount * 14}px`,
        });
      }
      currentMonth = month;
      monthWeekCount = 1;
    } else {
      monthWeekCount++;
    }
  }

  if (monthWeekCount > 0 && currentMonth >= 0) {
    labels.push({
      name: new Date(selectedYear.value, currentMonth).toLocaleDateString('en-US', { month: 'short' }),
      width: `${monthWeekCount * 14}px`,
    });
  }

  return labels;
});

// Generate mobile month labels
const mobileMonthLabels = computed(() => {
  const labels = [];
  if (props.gridData.length === 0) return labels;

  const startDate = new Date(selectedYear.value, 0, 1);
  const gridStartDate = new Date(startDate);
  const dayOfWeek = gridStartDate.getDay();
  if (dayOfWeek !== 0) {
    gridStartDate.setDate(gridStartDate.getDate() - dayOfWeek);
  }

  let currentMonth = -1;
  let monthWeekCount = 0;

  for (let i = 0; i < 53; i++) {
    const weekDate = new Date(gridStartDate);
    weekDate.setDate(weekDate.getDate() + i * 7 + 3);
    const weekYear = weekDate.getFullYear();
    const month = weekDate.getMonth();

    if (weekYear !== selectedYear.value) continue;

    if (month !== currentMonth) {
      if (monthWeekCount > 0 && currentMonth >= 0) {
        labels.push({
          name: new Date(selectedYear.value, currentMonth).toLocaleDateString('en-US', { month: 'short' }),
          width: `${monthWeekCount * 9}px`,
        });
      }
      currentMonth = month;
      monthWeekCount = 1;
    } else {
      monthWeekCount++;
    }
  }

  if (monthWeekCount > 0 && currentMonth >= 0) {
    labels.push({
      name: new Date(selectedYear.value, currentMonth).toLocaleDateString('en-US', { month: 'short' }),
      width: `${monthWeekCount * 9}px`,
    });
  }

  return labels;
});

// Generate year grid
const yearGrid = computed(() => {
  const weeks = [];
  if (props.gridData.length === 0) return weeks;

  const startDate = new Date(selectedYear.value, 0, 1);
  const gridStartDate = new Date(startDate);
  const dayOfWeek = gridStartDate.getDay();
  if (dayOfWeek !== 0) {
    gridStartDate.setDate(gridStartDate.getDate() - dayOfWeek);
  }

  const totalWeeks = 53;

  // Create a map for quick lookup
  const dataMap = {};
  props.gridData.forEach(day => {
    dataMap[day.date] = day;
  });

  for (let week = 0; week < totalWeeks; week++) {
    const days = [];

    for (let dayIndex = 0; dayIndex < 7; dayIndex++) {
      const currentDate = new Date(gridStartDate);
      currentDate.setDate(currentDate.getDate() + week * 7 + dayIndex);
      const year = currentDate.getFullYear();
      const month = String(currentDate.getMonth() + 1).padStart(2, '0');
      const day = String(currentDate.getDate()).padStart(2, '0');
      const dateString = `${year}-${month}-${day}`;

      const dayData = dataMap[dateString];

      if (dayData) {
        days.push(dayData);
      } else {
        days.push({ date: null, points: 0, activity_count: 0, intensity: 0 });
      }
    }
    weeks.push({ start: week, days });
  }

  return weeks;
});

// Get mobile day style
const getMobileDayStyle = (day) => {
  if (!day || !day.date) {
    return { opacity: '0' };
  }

  const color = intensityColor(day.intensity);
  return {
    backgroundColor: color,
    border: day.intensity === 0
      ? '1px solid rgba(0, 0, 0, 0.08)'   // Empty: very subtle border
      : '1px solid rgba(0, 0, 0, 0.12)',  // Active: slightly stronger border
  };
};

// Get desktop day style
const getDayStyle = (day) => {
  if (!day || !day.date) {
    return { opacity: '0' };
  }

  const color = intensityColor(day.intensity);
  return {
    backgroundColor: color,
    border: day.intensity === 0
      ? '1px solid rgba(0, 0, 0, 0.08)'   // Empty: very subtle border
      : '1px solid rgba(0, 0, 0, 0.12)',  // Active: slightly stronger border
  };
};
</script>
