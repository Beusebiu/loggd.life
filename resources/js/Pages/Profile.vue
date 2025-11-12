<template>
  <AppLayout>
    <div class="max-w-6xl mx-auto">
      <!-- Page Header -->
      <div class="sticky top-0 z-10 bg-white/80 backdrop-blur-md border-b border-gray-200 px-4 py-3">
        <h2 class="text-xl font-bold text-black">Profile</h2>
      </div>

      <div class="px-4 sm:px-6 lg:px-8 py-6 space-y-6">
        <!-- Profile Header -->
        <div class="bg-white rounded-2xl p-8 border border-gray-200">
          <div class="flex items-start justify-between mb-6">
            <div class="flex gap-6">
              <!-- Avatar -->
              <div class="w-20 h-20 rounded-full bg-gradient-to-br from-green-500 to-emerald-600 flex items-center justify-center text-white font-bold text-3xl flex-shrink-0 shadow-lg">
                {{ profileUser.name.charAt(0).toUpperCase() }}
              </div>

              <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ profileUser.name }}</h1>
                <p class="text-base text-gray-600 mb-3">
                  @{{ profileUser.username }}
                  <span v-if="!profileUser.profile_public" class="ml-2 px-2 py-1 bg-gray-100 text-gray-600 text-xs rounded-full">
                    🔒 Private
                  </span>
                  <span v-else class="ml-2 px-2 py-1 bg-green-100 text-green-700 text-xs rounded-full">
                    🌐 Public
                  </span>
                </p>
                <p v-if="profileUser.bio" class="text-sm text-gray-700 max-w-xl">{{ profileUser.bio }}</p>
                <p v-else class="text-sm text-gray-400 italic">No bio yet</p>
              </div>
            </div>

            <Link
              v-if="isOwnProfile"
              :href="`/@${profileUser.username}/settings`"
              class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors"
            >
              Edit Profile
            </Link>
          </div>

          <!-- Key Stats Row -->
          <div class="flex gap-8 pt-4 border-t border-gray-100">
            <div>
              <div class="text-2xl font-bold text-gray-900">{{ stats.activeHabits }}</div>
              <div class="text-sm text-gray-600">Active Habits</div>
            </div>
            <div>
              <div class="text-2xl font-bold text-green-600">{{ stats.longestStreak }}</div>
              <div class="text-sm text-gray-600">Best Streak</div>
            </div>
            <div>
              <div class="text-2xl font-bold text-gray-900">{{ stats.totalChecks }}</div>
              <div class="text-sm text-gray-600">Total Checks</div>
            </div>
            <div>
              <div class="text-2xl font-bold text-gray-900">{{ stats.totalEntries }}</div>
              <div class="text-sm text-gray-600">Entries</div>
            </div>
          </div>
        </div>

        <!-- Active Streaks -->
        <div v-if="stats.streaks && stats.streaks.length > 0" class="bg-white rounded-2xl p-6 border border-gray-200">
          <div class="flex items-center justify-between mb-5">
            <h2 class="text-xl font-bold text-gray-900 flex items-center gap-2">
              <span>🔥</span>
              Current Streaks
            </h2>
            <Link
              :href="`/@${profileUser.username}/habits`"
              class="text-sm text-green-600 hover:text-green-700 font-medium"
            >
              Manage Habits →
            </Link>
          </div>

          <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            <div
              v-for="streak in stats.streaks"
              :key="streak.habit_name"
              class="p-4 bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl hover:from-green-50 hover:to-emerald-50 transition-all border border-gray-200 hover:border-green-300 hover:shadow-md"
            >
              <div class="flex items-center justify-between mb-2">
                <span class="text-3xl">{{ streak.habit_emoji }}</span>
                <div class="text-right">
                  <div class="text-xl font-bold text-green-600">{{ streak.streak }}</div>
                  <div class="text-xs text-gray-500">days</div>
                </div>
              </div>
              <p class="text-sm font-medium text-gray-800">{{ streak.habit_name }}</p>
            </div>
          </div>
        </div>

        <!-- Activity Overview (GitHub-style Year Grid) -->
        <div class="bg-white rounded-2xl p-6 border border-gray-200">
          <div class="flex items-center justify-between mb-5">
            <h2 class="text-xl font-bold text-gray-900">Activity Overview</h2>
            <div class="flex gap-4 text-xs">
              <div class="flex items-center gap-1.5">
                <div class="w-3 h-3 bg-gray-200 rounded-sm"></div>
                <span class="text-gray-600">None</span>
              </div>
              <div class="flex items-center gap-1.5">
                <div class="w-3 h-3 bg-green-500 rounded-sm"></div>
                <span class="text-gray-600">Most</span>
              </div>
            </div>
          </div>

          <!-- Month Labels -->
          <div class="flex gap-0.5 mb-1 overflow-x-auto" style="scrollbar-width: thin;">
            <div
              v-for="(monthLabel, labelIndex) in getMonthLabels()"
              :key="labelIndex"
              class="text-[10px] text-gray-500"
              :style="{ width: monthLabel.width }"
            >
              {{ monthLabel.name }}
            </div>
          </div>

          <!-- GitHub-style Grid (Last 365 Days) -->
          <div class="flex gap-0.5 overflow-x-auto pb-1" style="scrollbar-width: thin;">
            <div
              v-for="week in getYearGrid()"
              :key="week.start"
              class="flex flex-col gap-0.5"
            >
              <div
                v-for="day in week.days"
                :key="day.date || day.index"
                class="w-3 h-3 rounded-sm transition-all hover:ring-1 hover:ring-gray-400"
                :class="getDayClass(day)"
                :title="day.date ? formatDayTooltip(day) : ''"
              ></div>
            </div>
          </div>

          <div class="mt-4 pt-4 border-t border-gray-100 text-sm text-gray-500">
            Showing activity from the last 365 days
          </div>
        </div>

        <!-- Growth Journey Stats -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <!-- Journey Info -->
          <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-2xl p-6 border border-blue-200">
            <div class="text-3xl mb-3">📈</div>
            <h3 class="text-lg font-bold text-gray-900 mb-2">Growth Journey</h3>
            <div class="space-y-2 text-sm">
              <div class="flex justify-between">
                <span class="text-gray-600">Started</span>
                <span class="font-semibold text-gray-900">{{ formatDate(profileUser.created_at) }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-600">Total Habits Tracked</span>
                <span class="font-semibold text-gray-900">{{ stats.totalHabits }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-600">Journal Entries</span>
                <span class="font-semibold text-gray-900">{{ stats.totalEntries }}</span>
              </div>
            </div>
          </div>

          <!-- Consistency Score -->
          <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-2xl p-6 border border-green-200">
            <div class="text-3xl mb-3">💪</div>
            <h3 class="text-lg font-bold text-gray-900 mb-2">Consistency</h3>
            <div class="space-y-2 text-sm">
              <div class="flex justify-between">
                <span class="text-gray-600">Longest Streak</span>
                <span class="font-semibold text-green-600">{{ stats.longestStreak }} days</span>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-600">Total Completions</span>
                <span class="font-semibold text-gray-900">{{ stats.totalChecks }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-600">Active Streaks</span>
                <span class="font-semibold text-gray-900">{{ stats.streaks?.length || 0 }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import axios from 'axios';
import AppLayout from '../Layouts/AppLayout.vue';

const props = defineProps({
  profileUser: Object,
  stats: Object,
  isOwnProfile: Boolean,
});

const activityData = ref({});

// Fetch activity data from backend
const fetchActivityData = async () => {
  try {
    const response = await axios.get('/api/stats/daily-activity', {
      params: { days: 365 }
    });
    activityData.value = response.data;
  } catch (error) {
    console.error('Error fetching activity data:', error);
  }
};

// Generate GitHub-style year grid (365 days)
const getYearGrid = () => {
  const weeks = [];
  const today = new Date();
  const startDate = new Date(today);
  startDate.setDate(startDate.getDate() - 364);

  // Adjust to previous Sunday
  const dayOfWeek = startDate.getDay();
  startDate.setDate(startDate.getDate() - dayOfWeek);

  let currentDate = new Date(startDate);

  while (currentDate <= today) {
    const week = { start: currentDate.toISOString().split('T')[0], days: [] };

    for (let i = 0; i < 7; i++) {
      const dateStr = currentDate.toISOString().split('T')[0];
      const oneYearAgo = new Date(today);
      oneYearAgo.setDate(oneYearAgo.getDate() - 364);

      if (currentDate >= oneYearAgo && currentDate <= today) {
        const dayActivity = activityData.value[dateStr];
        week.days.push({
          date: dateStr,
          index: i,
          intensity: dayActivity ? dayActivity.intensity : 0,
          details: dayActivity || null
        });
      } else {
        week.days.push({ date: null, index: i });
      }

      currentDate.setDate(currentDate.getDate() + 1);
    }

    weeks.push(week);
  }

  return weeks;
};

// Generate month labels for the year grid
const getMonthLabels = () => {
  const weeks = getYearGrid();
  const labels = [];
  let currentMonth = null;
  let currentWidth = 0;

  weeks.forEach((week) => {
    const firstDay = week.days.find(d => d.date);
    if (firstDay) {
      const date = new Date(firstDay.date + 'T00:00:00');
      const month = date.toLocaleDateString('en-US', { month: 'short' });

      if (month !== currentMonth) {
        if (currentMonth !== null && currentWidth > 0) {
          labels.push({
            name: currentMonth,
            width: `${currentWidth * 13}px`, // 12px square + 1px gap
          });
        }
        currentMonth = month;
        currentWidth = 1;
      } else {
        currentWidth++;
      }
    }
  });

  // Add the last month
  if (currentMonth !== null && currentWidth > 0) {
    labels.push({
      name: currentMonth,
      width: `${currentWidth * 13}px`,
    });
  }

  return labels;
};

const getDayClass = (day) => {
  if (!day || !day.date) return 'opacity-0';

  const intensity = day.intensity || 0;

  // Map intensity (0-5) to green shades
  if (intensity === 0) return 'bg-gray-200';
  if (intensity === 1) return 'bg-green-200';
  if (intensity === 2) return 'bg-green-300';
  if (intensity === 3) return 'bg-green-400';
  if (intensity === 4) return 'bg-green-500';
  if (intensity >= 5) return 'bg-green-600';

  return 'bg-gray-200';
};

const formatDayTooltip = (day) => {
  if (!day) return '';
  const date = new Date(day.date);
  const formatted = date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });

  if (day.details) {
    const parts = [];
    if (day.details.habit_checks > 0) parts.push(`${day.details.habit_checks} habit${day.details.habit_checks > 1 ? 's' : ''}`);
    if (day.details.entries > 0) parts.push(`${day.details.entries} entr${day.details.entries > 1 ? 'ies' : 'y'}`);
    if (day.details.check_ins > 0) parts.push(`${day.details.check_ins} check-in${day.details.check_ins > 1 ? 's' : ''}`);

    const detailStr = parts.length > 0 ? parts.join(', ') : 'No activity';
    return `${formatted}\n${detailStr}`;
  }

  return `${formatted} - ${day.activity > 0 ? 'Active' : 'No activity'}`;
};

const formatDate = (dateString) => {
  const date = new Date(dateString);
  return date.toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' });
};

onMounted(() => {
  fetchActivityData();
});
</script>
