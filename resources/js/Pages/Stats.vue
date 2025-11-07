<template>
  <div class="min-h-screen bg-gradient-to-b from-white to-gray-50">
    <AppNav />
    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Header -->
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-black mb-2">Statistics & Insights</h1>
        <p class="text-gray-600">Track your progress and analyze your patterns</p>
      </div>

      <div v-if="stats" class="space-y-8">
        <!-- Overview Cards -->
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-4">
          <div class="bg-white rounded-xl p-6 border border-gray-200 shadow-sm">
            <div class="text-3xl font-bold text-green-600 mb-1">{{ stats.overview.days_active }}</div>
            <div class="text-sm text-gray-600">Active Days</div>
          </div>
          <div class="bg-white rounded-xl p-6 border border-gray-200 shadow-sm">
            <div class="text-3xl font-bold text-black mb-1">{{ stats.overview.active_habits }}</div>
            <div class="text-sm text-gray-600">Active Habits</div>
          </div>
          <div class="bg-white rounded-xl p-6 border border-gray-200 shadow-sm">
            <div class="text-3xl font-bold text-black mb-1">{{ stats.overview.total_entries }}</div>
            <div class="text-sm text-gray-600">Total Entries</div>
          </div>
          <div class="bg-white rounded-xl p-6 border border-gray-200 shadow-sm">
            <div class="text-3xl font-bold text-black mb-1">{{ stats.overview.total_check_ins }}</div>
            <div class="text-sm text-gray-600">Check-ins</div>
          </div>
          <div class="bg-white rounded-xl p-6 border border-gray-200 shadow-sm">
            <div class="text-3xl font-bold text-black mb-1">{{ stats.overview.total_habit_checks }}</div>
            <div class="text-sm text-gray-600">Completed</div>
          </div>
          <div class="bg-white rounded-xl p-6 border border-gray-200 shadow-sm col-span-2">
            <div class="text-3xl font-bold text-black mb-1">{{ formatDate(stats.overview.member_since) }}</div>
            <div class="text-sm text-gray-600">Member Since</div>
          </div>
        </div>

        <!-- Current Month Progress -->
        <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-2xl p-8 border border-green-200 shadow-lg">
          <h2 class="text-2xl font-bold text-black mb-6">Current Month Progress</h2>
          <div class="flex items-center gap-6">
            <div class="flex-1">
              <div class="flex justify-between mb-2">
                <span class="text-sm font-medium text-gray-700">Active Days</span>
                <span class="text-sm font-bold text-green-700">
                  {{ stats.activity.current_month_progress.active_days }} / {{ stats.activity.current_month_progress.total_days }} days
                </span>
              </div>
              <div class="w-full bg-white rounded-full h-4 overflow-hidden">
                <div
                  class="bg-gradient-to-r from-green-500 to-emerald-600 h-full rounded-full transition-all duration-500"
                  :style="{ width: `${stats.activity.current_month_progress.percentage}%` }"
                ></div>
              </div>
            </div>
            <div class="text-5xl font-bold text-green-600">
              {{ stats.activity.current_month_progress.percentage }}%
            </div>
          </div>
        </div>

        <!-- Habits Performance -->
        <div class="bg-white rounded-2xl p-8 border border-gray-200 shadow-lg">
          <h2 class="text-2xl font-bold text-black mb-6">Habits Performance</h2>
          <div class="space-y-4">
            <div
              v-for="habit in stats.habits"
              :key="habit.id"
              class="p-4 bg-gray-50 rounded-xl border border-gray-200 hover:shadow-md transition-shadow"
            >
              <div class="flex items-start justify-between mb-3">
                <div class="flex items-center gap-3">
                  <span class="text-3xl">{{ habit.emoji }}</span>
                  <div>
                    <h3 class="font-bold text-black">{{ habit.name }}</h3>
                    <p class="text-sm text-gray-500">Started {{ formatDate(habit.started_at) }}</p>
                  </div>
                </div>
                <span
                  class="px-3 py-1 text-xs font-semibold rounded-full"
                  :class="habit.status === 'active' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600'"
                >
                  {{ habit.status }}
                </span>
              </div>

              <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div>
                  <div class="text-2xl font-bold text-black">{{ habit.current_streak }}</div>
                  <div class="text-xs text-gray-600">Current Streak</div>
                </div>
                <div>
                  <div class="text-2xl font-bold text-black">{{ habit.longest_streak }}</div>
                  <div class="text-xs text-gray-600">Longest Streak</div>
                </div>
                <div>
                  <div class="text-2xl font-bold text-black">{{ habit.total_checks }}</div>
                  <div class="text-xs text-gray-600">Total Checks</div>
                </div>
                <div>
                  <div class="text-2xl font-bold text-green-600">{{ habit.completion_rate_30d }}%</div>
                  <div class="text-xs text-gray-600">30-Day Rate</div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Activity by Day of Week -->
        <div class="bg-white rounded-2xl p-8 border border-gray-200 shadow-lg">
          <h2 class="text-2xl font-bold text-black mb-6">Activity by Day of Week</h2>
          <div class="space-y-3">
            <div
              v-for="(count, day) in stats.activity.by_day_of_week"
              :key="day"
              class="flex items-center gap-4"
            >
              <div class="w-24 text-sm font-medium text-gray-700">{{ day }}</div>
              <div class="flex-1 bg-gray-100 rounded-full h-8 overflow-hidden">
                <div
                  class="bg-gradient-to-r from-green-400 to-emerald-500 h-full rounded-full flex items-center justify-end pr-3 text-white text-sm font-semibold transition-all duration-500"
                  :style="{ width: `${getBarWidth(count, stats.activity.by_day_of_week)}%` }"
                >
                  <span v-if="count > 0">{{ count }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Entries by Category -->
        <div v-if="stats.entries.by_category.length > 0" class="bg-white rounded-2xl p-8 border border-gray-200 shadow-lg">
          <h2 class="text-2xl font-bold text-black mb-6">Entries by Category</h2>
          <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            <div
              v-for="cat in stats.entries.by_category"
              :key="cat.category?.name"
              class="p-4 rounded-xl border-2 hover:shadow-md transition-shadow"
              :style="{ borderColor: cat.category?.color || '#ccc' }"
            >
              <div class="text-3xl mb-2">{{ cat.category?.icon || '📝' }}</div>
              <div class="text-2xl font-bold text-black mb-1">{{ cat.count }}</div>
              <div class="text-sm text-gray-600">{{ cat.category?.name || 'Uncategorized' }}</div>
            </div>
          </div>
        </div>

        <!-- Check-ins Summary -->
        <div class="bg-white rounded-2xl p-8 border border-gray-200 shadow-lg">
          <h2 class="text-2xl font-bold text-black mb-6">Check-ins Summary</h2>
          <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="p-6 bg-blue-50 rounded-xl border border-blue-200">
              <div class="text-4xl mb-3">📅</div>
              <div class="text-3xl font-bold text-black mb-1">{{ stats.check_ins.by_type.daily || 0 }}</div>
              <div class="text-sm text-gray-600">Daily Check-ins</div>
            </div>
            <div class="p-6 bg-purple-50 rounded-xl border border-purple-200">
              <div class="text-4xl mb-3">📊</div>
              <div class="text-3xl font-bold text-black mb-1">{{ stats.check_ins.by_type.weekly || 0 }}</div>
              <div class="text-sm text-gray-600">Weekly Reviews</div>
            </div>
            <div class="p-6 bg-pink-50 rounded-xl border border-pink-200">
              <div class="text-4xl mb-3">📈</div>
              <div class="text-3xl font-bold text-black mb-1">{{ stats.check_ins.by_type.monthly || 0 }}</div>
              <div class="text-sm text-gray-600">Monthly Reflections</div>
            </div>
          </div>
        </div>
      </div>

      <!-- Loading State -->
      <div v-else class="text-center py-16">
        <div class="text-4xl mb-4">📊</div>
        <p class="text-gray-600">Loading statistics...</p>
      </div>
    </main>
  </div>
</template>

<script setup>
import AppNav from '../Components/AppNav.vue';
import { ref, onMounted } from 'vue';
import { router } from '@inertiajs/vue3';

const stats = ref(null);

const fetchStats = async () => {
  try {
    const response = await window.axios.get('/api/stats');
    stats.value = response.data;
  } catch (error) {
    console.error('Error fetching stats:', error);
  }
};

const getBarWidth = (count, allCounts) => {
  const max = Math.max(...Object.values(allCounts));
  if (max === 0) return 0;
  return Math.max(5, (count / max) * 100);
};

const formatDate = (dateString) => {
  const date = new Date(dateString);
  return date.toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  });
};


onMounted(() => {
  fetchStats();
});
</script>
