<template>
  <div class="contribution-graph">
    <div class="flex items-start space-x-2">
      <!-- Month labels -->
      <div class="flex-1">
        <div class="flex mb-1 text-xs text-gray-500">
          <div v-for="month in monthLabels" :key="month.name" :style="{ marginLeft: month.offset + 'px' }">
            {{ month.name }}
          </div>
        </div>

        <!-- Grid -->
        <div class="flex space-x-1">
          <div v-for="(week, weekIndex) in weeks" :key="weekIndex" class="flex flex-col space-y-1">
            <div
              v-for="(day, dayIndex) in week"
              :key="`${weekIndex}-${dayIndex}`"
              :class="[
                'w-3 h-3 rounded-sm transition-all cursor-pointer',
                getColorClass(day.count)
              ]"
              :title="getTooltip(day)"
              @mouseenter="hoveredDay = day"
              @mouseleave="hoveredDay = null"
            />
          </div>
        </div>

        <!-- Day labels -->
        <div class="flex mt-1 text-xs text-gray-500 space-x-1">
          <div class="flex flex-col justify-around h-full" style="width: 12px;">
            <span>Mon</span>
            <span>Wed</span>
            <span>Fri</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Legend -->
    <div class="flex items-center justify-end mt-4 space-x-2 text-xs text-gray-500">
      <span>Less</span>
      <div class="flex space-x-1">
        <div class="w-3 h-3 bg-gray-100 border border-gray-200 rounded-sm"></div>
        <div class="w-3 h-3 bg-green-200 rounded-sm"></div>
        <div class="w-3 h-3 bg-green-400 rounded-sm"></div>
        <div class="w-3 h-3 bg-green-600 rounded-sm"></div>
        <div class="w-3 h-3 bg-green-800 rounded-sm"></div>
      </div>
      <span>More</span>
    </div>

    <!-- Hover tooltip -->
    <div v-if="hoveredDay && hoveredDay.date" class="mt-2 text-sm text-gray-600">
      {{ formatTooltipDate(hoveredDay.date) }}: {{ hoveredDay.count }} {{ hoveredDay.count === 1 ? 'activity' : 'activities' }}
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';

const props = defineProps({
  days: {
    type: Number,
    default: 365
  }
});

const activityData = ref({});
const hoveredDay = ref(null);

const weeks = computed(() => {
  const result = [];
  const today = new Date();
  const startDate = new Date(today);
  startDate.setDate(startDate.getDate() - props.days + 1);

  // Start from the most recent Sunday
  const dayOfWeek = today.getDay();
  const daysToAdd = dayOfWeek === 0 ? 0 : 7 - dayOfWeek;
  const endDate = new Date(today);
  endDate.setDate(endDate.getDate() + daysToAdd);

  // Move start date to Sunday
  const startDayOfWeek = startDate.getDay();
  startDate.setDate(startDate.getDate() - startDayOfWeek);

  let currentWeek = [];
  let currentDate = new Date(startDate);

  while (currentDate <= endDate) {
    const dateString = currentDate.toISOString().split('T')[0];
    const dayOfWeek = currentDate.getDay();

    currentWeek.push({
      date: dateString,
      count: activityData.value[dateString] || 0,
      isPast: currentDate <= today
    });

    if (dayOfWeek === 6) {
      result.push([...currentWeek]);
      currentWeek = [];
    }

    currentDate.setDate(currentDate.getDate() + 1);
  }

  if (currentWeek.length > 0) {
    // Pad the last week with empty days
    while (currentWeek.length < 7) {
      currentWeek.push({ date: null, count: 0, isPast: false });
    }
    result.push(currentWeek);
  }

  return result;
});

const monthLabels = computed(() => {
  const labels = [];
  const today = new Date();
  const startDate = new Date(today);
  startDate.setDate(startDate.getDate() - props.days + 1);

  let currentMonth = -1;
  let weekIndex = 0;

  weeks.value.forEach((week, index) => {
    const firstDayOfWeek = week.find(day => day.date);
    if (firstDayOfWeek && firstDayOfWeek.date) {
      const date = new Date(firstDayOfWeek.date);
      const month = date.getMonth();

      if (month !== currentMonth) {
        currentMonth = month;
        labels.push({
          name: date.toLocaleDateString('en-US', { month: 'short' }),
          offset: weekIndex * 16 // 12px width + 4px gap
        });
      }
    }
    weekIndex++;
  });

  return labels;
});

const getColorClass = (count) => {
  if (count === 0) return 'bg-gray-100 border border-gray-200';
  if (count === 1) return 'bg-green-200';
  if (count === 2) return 'bg-green-400';
  if (count === 3) return 'bg-green-600';
  return 'bg-green-800';
};

const getTooltip = (day) => {
  if (!day.date) return '';
  const date = new Date(day.date);
  return `${date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })}: ${day.count} ${day.count === 1 ? 'activity' : 'activities'}`;
};

const formatTooltipDate = (dateString) => {
  const date = new Date(dateString);
  return date.toLocaleDateString('en-US', {
    weekday: 'short',
    month: 'short',
    day: 'numeric',
    year: 'numeric'
  });
};

const fetchActivityData = async () => {
  try {
    // Fetch habit checks
    const habitChecksResponse = await axios.get('/api/habit-checks', {
      params: {
        from: new Date(new Date().setDate(new Date().getDate() - props.days)).toISOString().split('T')[0],
        to: new Date().toISOString().split('T')[0]
      }
    });

    // Fetch entries
    const entriesResponse = await axios.get('/api/entries', {
      params: {
        from: new Date(new Date().setDate(new Date().getDate() - props.days)).toISOString().split('T')[0],
        to: new Date().toISOString().split('T')[0]
      }
    });

    // Count activities per day
    const counts = {};

    habitChecksResponse.data.forEach(check => {
      const date = check.date;
      counts[date] = (counts[date] || 0) + 1;
    });

    entriesResponse.data.forEach(entry => {
      const date = entry.entry_date;
      counts[date] = (counts[date] || 0) + 1;
    });

    activityData.value = counts;
  } catch (error) {
    console.error('Error fetching activity data:', error);
  }
};

onMounted(() => {
  fetchActivityData();
});
</script>
