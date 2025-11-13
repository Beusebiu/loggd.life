<template>
  <div class="px-4 py-4 bg-gray-50">
    <!-- Header with legend -->
    <div class="mb-2 flex items-center justify-between">
      <div class="text-xs font-medium text-gray-700">Last 365 days</div>

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

    <!-- Month Labels -->
    <div class="flex gap-0.5 mb-1 overflow-x-auto" style="scrollbar-width: thin;">
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
    <div class="flex gap-1 overflow-x-auto pb-1" style="scrollbar-width: thin;">
      <!-- Day labels (Sun=0, Mon=1, Tue=2, Wed=3, Thu=4, Fri=5, Sat=6) -->
      <div class="flex flex-col gap-0.5 mr-1">
        <div class="h-3"></div> <!-- Sunday (row 0) -->
        <div class="h-3 text-[9px] text-gray-400 flex items-center">Mon</div> <!-- Monday (row 1) -->
        <div class="h-3"></div> <!-- Tuesday (row 2) -->
        <div class="h-3 text-[9px] text-gray-400 flex items-center">Wed</div> <!-- Wednesday (row 3) -->
        <div class="h-3"></div> <!-- Thursday (row 4) -->
        <div class="h-3 text-[9px] text-gray-400 flex items-center">Fri</div> <!-- Friday (row 5) -->
        <div class="h-3"></div> <!-- Saturday (row 6) -->
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
</template>

<script setup>
import { computed } from 'vue';

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

defineEmits(['show-tooltip', 'hide-tooltip']);

const habitColor = computed(() => props.habit.color || '#10B981');

// Generate month labels
const monthLabels = computed(() => {
  const labels = [];
  const today = new Date();
  const startDate = new Date(today);
  startDate.setDate(startDate.getDate() - 364);

  let currentMonth = startDate.getMonth();
  let weekCount = 0;
  let monthWeekCount = 0;

  // Calculate weeks per month
  for (let i = 0; i < 53; i++) {
    const weekDate = new Date(startDate);
    weekDate.setDate(weekDate.getDate() + i * 7);
    const month = weekDate.getMonth();

    if (month !== currentMonth) {
      if (monthWeekCount > 0) {
        labels.push({
          name: new Date(2024, currentMonth).toLocaleDateString('en-US', { month: 'short' }),
          width: `${monthWeekCount * 14}px`,
        });
      }
      currentMonth = month;
      monthWeekCount = 1;
    } else {
      monthWeekCount++;
    }
  }

  // Add last month
  if (monthWeekCount > 0) {
    labels.push({
      name: new Date(2024, currentMonth).toLocaleDateString('en-US', { month: 'short' }),
      width: `${monthWeekCount * 14}px`,
    });
  }

  return labels;
});

// Generate year grid (53 weeks x 7 days)
const yearGrid = computed(() => {
  const weeks = [];
  const today = new Date();
  today.setHours(0, 0, 0, 0);

  // Start from 364 days ago
  const startDate = new Date(today);
  startDate.setDate(startDate.getDate() - 364);

  // Adjust to the Sunday of the week containing this start date
  // getDay() returns 0 for Sunday, 1 for Monday, etc.
  const dayOfWeek = startDate.getDay();
  if (dayOfWeek !== 0) {
    startDate.setDate(startDate.getDate() - dayOfWeek);
  }

  // Generate 53 weeks
  for (let week = 0; week < 53; week++) {
    const days = [];

    // For each day of the week (0 = Sunday, 6 = Saturday)
    for (let dayIndex = 0; dayIndex < 7; dayIndex++) {
      const currentDate = new Date(startDate);
      currentDate.setDate(currentDate.getDate() + week * 7 + dayIndex);
      // Format date as YYYY-MM-DD in local timezone (avoid timezone issues)
      const year = currentDate.getFullYear();
      const month = String(currentDate.getMonth() + 1).padStart(2, '0');
      const day = String(currentDate.getDate()).padStart(2, '0');
      const dateString = `${year}-${month}-${day}`;

      // Find data for this date
      const dayData = props.habit.year_data?.find(d => d.date === dateString);

      if (currentDate <= today) {
        days.push(dayData || { date: dateString, tracked: false, completed: false, count: 0 });
      } else {
        days.push({ index: dayIndex, date: null }); // Future date
      }
    }
    weeks.push({ start: week, days });
  }

  return weeks;
});

// Get day style based on completion status and count
const getDayStyle = (day) => {
  if (!day || !day.date) {
    return { opacity: '0' }; // Empty cell
  }

  // Not tracked (based on frequency) - gray
  if (!day.tracked) {
    return { backgroundColor: '#f3f4f6' };
  }

  if (day.completed) {
    // For habits with multiple checks, use opacity gradient
    if (props.habit.allow_multiple_checks && day.count) {
      const opacity = Math.min(0.3 + (day.count * 0.175), 1);
      return {
        backgroundColor: habitColor.value,
        opacity: opacity.toString(),
      };
    }

    // Single check habit - full color
    return { backgroundColor: habitColor.value };
  } else {
    // Tracked but not completed - use habit color at 20% opacity
    return { backgroundColor: habitColor.value + '20' };
  }
};
</script>
