<template>
  <div class="px-2 sm:px-4 py-3 sm:py-4 bg-gray-50">
    <!-- Header with year selector and legend -->
    <div class="mb-2 flex items-center justify-between flex-wrap gap-2">
      <div class="flex items-center gap-3">
        <div class="text-xs font-medium text-gray-700">
          {{ habit.selected_year ? habit.selected_year : 'Last 365 days' }}
        </div>
        <!-- Year selector (if multiple years available) -->
        <div v-if="habit.available_years && habit.available_years.length > 1" class="flex items-center gap-1">
          <button
            v-for="year in habit.available_years"
            :key="year"
            @click="$emit('change-year', year)"
            :class="[
              'px-2 py-0.5 text-[10px] font-medium rounded transition-colors',
              year === habit.selected_year
                ? 'bg-gray-900 text-white'
                : 'bg-gray-200 text-gray-600 hover:bg-gray-300'
            ]"
          >
            {{ year }}
          </button>
        </div>
      </div>

    <!-- Legend for multiple checks (intensity) -->
    <div v-if="habit.allow_multiple_checks" class="flex items-center gap-2 text-[10px] text-gray-500">
        <div class="flex items-center gap-1">
          <div class="w-3 h-3 rounded-sm bg-gray-200"></div>
          <span>Not tracked</span>
        </div>
        <div class="flex items-center gap-1">
          <div class="w-3 h-3 rounded-sm" :style="{ backgroundColor: habitColor, opacity: 0.3 }"></div>
          <span>Less</span>
        </div>
        <div class="flex items-center gap-1">
          <div class="w-3 h-3 rounded-sm" :style="{ backgroundColor: habitColor }"></div>
          <span>More</span>
        </div>
      </div>

      <!-- Legend for single checks -->
      <div v-else class="flex items-center gap-2 text-[10px] text-gray-500">
        <div class="flex items-center gap-1">
          <div class="w-3 h-3 rounded-sm bg-gray-200"></div>
          <span>Not tracked</span>
        </div>
        <div class="flex items-center gap-1">
          <div class="w-3 h-3 rounded-sm border border-gray-300" :style="{ backgroundColor: habitColor + '20' }"></div>
          <span>Not done</span>
        </div>
        <div class="flex items-center gap-1">
          <div class="w-3 h-3 rounded-sm" :style="{ backgroundColor: habitColor }"></div>
          <span>Done</span>
        </div>
      </div>
    </div>

    <!-- Mobile Grid - FULL BLEED APPROACH (breaks out of parent constraints) -->
    <div class="block sm:hidden">
      <!-- Full viewport width container that breaks out of parent padding -->
      <div class="relative w-screen left-1/2 right-1/2 -ml-[50vw] -mr-[50vw] py-3 bg-gray-50">
        <!-- Scrollable area with padding -->
        <div ref="mobileScrollContainer" class="overflow-x-auto overflow-y-hidden pb-2 pl-10 pr-8" style="-webkit-overflow-scrolling: touch; scrollbar-width: thin;">
          <!-- Grid wrapper - natural content width -->
          <div class="inline-flex flex-col gap-2">
            <!-- Month Labels -->
            <div class="flex gap-[1px]">
              <!-- Spacer for day labels alignment -->
              <div class="ml-1 mr-2" style="width: 12px;"></div>
              <div
                v-for="(monthLabel, labelIndex) in mobileMonthLabels"
                :key="labelIndex"
                class="text-[9px] text-gray-600 font-medium"
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
                <div class="h-[8px] text-[8px] text-gray-500 flex items-center leading-none w-3">M</div>
                <div class="h-[8px]"></div>
                <div class="h-[8px] text-[8px] text-gray-500 flex items-center leading-none w-3">W</div>
                <div class="h-[8px]"></div>
                <div class="h-[8px] text-[8px] text-gray-500 flex items-center leading-none w-3">F</div>
                <div class="h-[8px]"></div>
              </div>

              <!-- Grid - All 365 days -->
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
            <div class="text-center text-[9px] text-gray-500">
              ← Scroll to see full year →
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Desktop Grid (Full 365 days) -->
    <div class="hidden sm:block">
      <div class="overflow-x-auto overflow-y-hidden">
        <div class="inline-block min-w-max">
          <!-- Month Labels -->
          <div class="flex gap-0.5 mb-1">
            <!-- Spacer for day labels alignment -->
            <div class="mr-1" style="width: 28px;"></div>
            <div
              v-for="(monthLabel, labelIndex) in monthLabels"
              :key="labelIndex"
              class="text-[9px] text-gray-500"
              :style="{ width: monthLabel.width }"
            >
              {{ monthLabel.name }}
            </div>
          </div>

          <!-- Grid with day labels -->
          <div class="flex gap-1 pb-1">
            <!-- Day labels -->
            <div class="flex flex-col gap-0.5 mr-1">
              <div class="h-3"></div> <!-- Sunday -->
              <div class="h-3 text-[9px] text-gray-400 flex items-center">Mon</div>
              <div class="h-3"></div> <!-- Tuesday -->
              <div class="h-3 text-[9px] text-gray-400 flex items-center">Wed</div>
              <div class="h-3"></div> <!-- Thursday -->
              <div class="h-3 text-[9px] text-gray-400 flex items-center">Fri</div>
              <div class="h-3"></div> <!-- Saturday -->
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
                  class="w-3 h-3 rounded-sm transition-all"
                  :class="{ 'cursor-pointer hover:ring-1 hover:ring-gray-400': day.date && showTooltip }"
                  :style="getDayStyle(day)"
                  @mouseenter="day.date && showTooltip && $emit('show-tooltip', $event, day)"
                  @mouseleave="showTooltip && $emit('hide-tooltip')"
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
  habit: {
    type: Object,
    required: true,
  },
  showTooltip: {
    type: Boolean,
    default: true,
  },
});

defineEmits(['show-tooltip', 'hide-tooltip', 'change-year']);

const habitColor = computed(() => props.habit.color || '#10B981');

// Mobile scroll container ref
const mobileScrollContainer = ref(null);

// Scroll to the right end on mount (to show most recent days)
onMounted(() => {
  if (mobileScrollContainer.value) {
    mobileScrollContainer.value.scrollLeft = mobileScrollContainer.value.scrollWidth;
  }
});

// Generate month labels (desktop only now)
const monthLabels = computed(() => {
  const labels = [];
  const yearData = props.habit.year_data || [];
  if (yearData.length === 0) return labels;

  const firstDate = new Date(yearData[0].date);

  // For full year view, start from January 1 of that year
  const startDate = props.habit.selected_year
    ? new Date(props.habit.selected_year, 0, 1)  // January 1st
    : new Date(firstDate);

  // Use the correct year for month labels
  const year = props.habit.selected_year || firstDate.getFullYear();

  // Adjust to the Sunday of the week containing the start date
  const gridStartDate = new Date(startDate);
  const dayOfWeek = gridStartDate.getDay();
  if (dayOfWeek !== 0) {
    gridStartDate.setDate(gridStartDate.getDate() - dayOfWeek);
  }

  let currentMonth = -1;
  let monthWeekCount = 0;

  // Calculate weeks per month - using desktop spacing (14px = 12px cell + 2px gap)
  for (let i = 0; i < 53; i++) {
    const weekDate = new Date(gridStartDate);
    weekDate.setDate(weekDate.getDate() + i * 7 + 3); // Use Wednesday of the week as reference
    const weekYear = weekDate.getFullYear();
    const month = weekDate.getMonth();

    // Only count weeks that belong to the selected year
    if (weekYear !== year) continue;

    if (month !== currentMonth) {
      if (monthWeekCount > 0 && currentMonth >= 0) {
        labels.push({
          name: new Date(year, currentMonth).toLocaleDateString('en-US', { month: 'short' }),
          width: `${monthWeekCount * 14}px`, // Desktop: 12px cell + 2px gap
        });
      }
      currentMonth = month;
      monthWeekCount = 1;
    } else {
      monthWeekCount++;
    }
  }

  // Add last month
  if (monthWeekCount > 0 && currentMonth >= 0) {
    labels.push({
      name: new Date(year, currentMonth).toLocaleDateString('en-US', { month: 'short' }),
      width: `${monthWeekCount * 14}px`,
    });
  }

  return labels;
});

// Generate mobile month labels - Compact mobile style
const mobileMonthLabels = computed(() => {
  const labels = [];
  const yearData = props.habit.year_data || [];
  if (yearData.length === 0) return labels;

  const firstDate = new Date(yearData[0].date);

  // For full year view, start from January 1 of that year
  const startDate = props.habit.selected_year
    ? new Date(props.habit.selected_year, 0, 1)  // January 1st
    : new Date(firstDate);

  // Use the correct year for month labels
  const year = props.habit.selected_year || firstDate.getFullYear();

  // Adjust to the Sunday of the week containing the start date
  const gridStartDate = new Date(startDate);
  const dayOfWeek = gridStartDate.getDay();
  if (dayOfWeek !== 0) {
    gridStartDate.setDate(gridStartDate.getDate() - dayOfWeek);
  }

  let currentMonth = -1;
  let monthWeekCount = 0;

  // Calculate weeks per month for mobile - Compact style (8px cell + 1px gap = 9px per week)
  for (let i = 0; i < 53; i++) {
    const weekDate = new Date(gridStartDate);
    weekDate.setDate(weekDate.getDate() + i * 7 + 3); // Use Wednesday of the week as reference
    const weekYear = weekDate.getFullYear();
    const month = weekDate.getMonth();

    // Only count weeks that belong to the selected year
    if (weekYear !== year) continue;

    if (month !== currentMonth) {
      if (monthWeekCount > 0 && currentMonth >= 0) {
        labels.push({
          name: new Date(year, currentMonth).toLocaleDateString('en-US', { month: 'short' }),
          width: `${monthWeekCount * 9}px`, // Compact mobile: 8px cell + 1px gap
        });
      }
      currentMonth = month;
      monthWeekCount = 1;
    } else {
      monthWeekCount++;
    }
  }

  // Add last month
  if (monthWeekCount > 0 && currentMonth >= 0) {
    labels.push({
      name: new Date(year, currentMonth).toLocaleDateString('en-US', { month: 'short' }),
      width: `${monthWeekCount * 9}px`,
    });
  }

  return labels;
});

// Generate year grid (weeks x 7 days) - Used by both mobile and desktop
const yearGrid = computed(() => {
  const weeks = [];
  const yearData = props.habit.year_data || [];
  if (yearData.length === 0) return weeks;

  const today = new Date();
  today.setHours(0, 0, 0, 0);

  // For selected year view, always start from January 1st of that year
  const firstDate = props.habit.selected_year
    ? new Date(props.habit.selected_year, 0, 1)
    : new Date(yearData[0].date);

  // Adjust to the Sunday of the week containing January 1st (or start date)
  const startDate = new Date(firstDate);
  const dayOfWeek = startDate.getDay();
  if (dayOfWeek !== 0) {
    startDate.setDate(startDate.getDate() - dayOfWeek);
  }

  // For selected year, always generate 53 weeks to show full year consistently
  const totalWeeks = 53;

  // Generate weeks
  for (let week = 0; week < totalWeeks; week++) {
    const days = [];

    for (let dayIndex = 0; dayIndex < 7; dayIndex++) {
      const currentDate = new Date(startDate);
      currentDate.setDate(currentDate.getDate() + week * 7 + dayIndex);
      const year = currentDate.getFullYear();
      const month = String(currentDate.getMonth() + 1).padStart(2, '0');
      const day = String(currentDate.getDate()).padStart(2, '0');
      const dateString = `${year}-${month}-${day}`;

      // Find day data from year_data
      const dayData = yearData.find(d => d.date === dateString);

      if (dayData) {
        days.push(dayData);
      } else {
        // Day not in year_data - likely out of range
        days.push({ date: null, tracked: false, completed: false, count: 0 });
      }
    }
    weeks.push({ start: week, days });
  }

  return weeks;
});

// Get mobile day style - GitHub style
const getMobileDayStyle = (day) => {
  if (!day || !day.date) {
    return { opacity: '0' }; // Empty - invisible like GitHub
  }

  // Before habit start or future date - show as very faint
  if (day.beforeStart || day.future) {
    return {
      backgroundColor: '#f0f0f0',
      opacity: '0.3',
      border: '1px solid rgba(27,31,35,0.04)'
    };
  }

  if (!day.tracked) {
    return {
      backgroundColor: '#ebedf0', // GitHub's light gray for no activity
      border: '1px solid rgba(27,31,35,0.06)' // Subtle border like GitHub
    };
  }

  if (day.completed) {
    if (props.habit.allow_multiple_checks && day.count) {
      const opacity = Math.min(0.3 + (day.count * 0.175), 1);
      return {
        backgroundColor: habitColor.value,
        opacity: opacity.toString(),
        border: '1px solid rgba(27,31,35,0.06)'
      };
    }
    return {
      backgroundColor: habitColor.value,
      border: '1px solid rgba(27,31,35,0.06)'
    };
  } else {
    // Tracked but not completed - lighter with GitHub-style border
    return {
      backgroundColor: habitColor.value,
      opacity: '0.3',
      border: '1px solid rgba(27,31,35,0.06)'
    };
  }
};

// Get day style based on completion status and count (Desktop)
const getDayStyle = (day) => {
  if (!day || !day.date) {
    return { opacity: '0' };
  }

  // Before habit start or future date - show as very faint
  if (day.beforeStart || day.future) {
    return {
      backgroundColor: '#f0f0f0',
      opacity: '0.3'
    };
  }

  if (!day.tracked) {
    return { backgroundColor: '#f3f4f6' };
  }

  if (day.completed) {
    if (props.habit.allow_multiple_checks && day.count) {
      const opacity = Math.min(0.3 + (day.count * 0.175), 1);
      return {
        backgroundColor: habitColor.value,
        opacity: opacity.toString(),
      };
    }
    return { backgroundColor: habitColor.value };
  } else {
    return { backgroundColor: habitColor.value + '20' };
  }
};
</script>
