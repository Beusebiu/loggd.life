<template>
  <div class="min-h-screen bg-gradient-to-b from-white to-gray-50">
    <AppNav />
    <!-- Main Content -->
    <main class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Header with Stats -->
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-black mb-4">Timeline</h1>

        <!-- Stats Cards -->
        <div v-if="stats" class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
          <div class="bg-white rounded-xl p-4 border border-gray-200 shadow-sm">
            <div class="text-2xl font-bold text-black">{{ stats.active_days }}</div>
            <div class="text-xs text-gray-600">Active Days</div>
          </div>
          <div class="bg-white rounded-xl p-4 border border-gray-200 shadow-sm">
            <div class="text-2xl font-bold text-black">{{ stats.entries_count }}</div>
            <div class="text-xs text-gray-600">Entries</div>
          </div>
          <div class="bg-white rounded-xl p-4 border border-gray-200 shadow-sm">
            <div class="text-2xl font-bold text-black">{{ stats.check_ins_count }}</div>
            <div class="text-xs text-gray-600">Check-ins</div>
          </div>
          <div class="bg-white rounded-xl p-4 border border-gray-200 shadow-sm">
            <div class="text-2xl font-bold text-black">{{ stats.habit_checks_count }}</div>
            <div class="text-xs text-gray-600">Habit Checks</div>
          </div>
        </div>

        <!-- Date Range Selector -->
        <div class="flex gap-4 items-center">
          <div class="flex gap-2">
            <button
              v-for="range in dateRanges"
              :key="range.value"
              @click="selectRange(range.value)"
              class="px-4 py-2 rounded-lg text-sm font-medium transition-all"
              :class="selectedRange === range.value
                ? 'bg-green-500 text-white shadow-lg shadow-green-500/30'
                : 'bg-white text-gray-700 border border-gray-200 hover:bg-gray-50'"
            >
              {{ range.label }}
            </button>
          </div>
        </div>
      </div>

      <!-- Timeline Feed -->
      <div v-if="timelineItems.length > 0" class="space-y-6">
        <div
          v-for="item in timelineItems"
          :key="`${item.type}-${item.id || item.date}`"
          class="relative"
        >
          <!-- Timeline Dot -->
          <div class="absolute left-0 top-6 w-3 h-3 rounded-full bg-green-500 ring-4 ring-white"></div>

          <!-- Timeline Content -->
          <div class="ml-8 bg-white rounded-2xl p-6 border border-gray-200 shadow-sm hover:shadow-md transition-shadow">
            <!-- Entry -->
            <div v-if="item.type === 'entry'">
              <div class="flex items-start justify-between mb-3">
                <div class="flex items-center gap-2">
                  <span class="text-2xl">📝</span>
                  <div>
                    <h3 class="font-semibold text-black">Entry</h3>
                    <p class="text-sm text-gray-500">{{ formatDate(item.date) }}</p>
                  </div>
                  <span v-if="item.category" class="ml-2 px-2 py-1 text-xs rounded-full" :style="{ backgroundColor: item.category.color + '20', color: item.category.color }">
                    {{ item.category.icon }} {{ item.category.name }}
                  </span>
                </div>
              </div>
              <p class="text-gray-800 whitespace-pre-wrap">{{ item.content }}</p>
            </div>

            <!-- Check-in -->
            <div v-else-if="item.type === 'check-in'">
              <div class="flex items-start justify-between mb-3">
                <div class="flex items-center gap-2">
                  <span class="text-2xl">{{ getCheckInIcon(item.check_in_type) }}</span>
                  <div>
                    <h3 class="font-semibold text-black">{{ getCheckInLabel(item.check_in_type) }}</h3>
                    <p class="text-sm text-gray-500">{{ formatDate(item.date) }}</p>
                  </div>
                </div>
              </div>
              <div class="space-y-3">
                <div v-for="(value, key) in item.content" :key="key" class="text-sm">
                  <span class="font-medium text-gray-700">{{ formatKey(key) }}:</span>
                  <span class="text-gray-800 ml-2">{{ value }}</span>
                </div>
              </div>
            </div>

            <!-- Habit Summary -->
            <div v-else-if="item.type === 'habit-summary'">
              <div class="flex items-start justify-between mb-3">
                <div class="flex items-center gap-2">
                  <span class="text-2xl">✅</span>
                  <div>
                    <h3 class="font-semibold text-black">{{ item.count }} Habits Completed</h3>
                    <p class="text-sm text-gray-500">{{ formatDate(item.date) }}</p>
                  </div>
                </div>
              </div>
              <div class="flex flex-wrap gap-2">
                <span
                  v-for="habit in item.habits"
                  :key="habit.id"
                  class="px-3 py-1.5 bg-green-50 text-green-700 rounded-lg text-sm font-medium border border-green-200"
                >
                  {{ habit.emoji }} {{ habit.name }}
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div v-else class="text-center py-16">
        <div class="text-6xl mb-4">📅</div>
        <h3 class="text-xl font-semibold text-gray-900 mb-2">No activity yet</h3>
        <p class="text-gray-600 mb-6">Start tracking habits, writing entries, or doing check-ins</p>
        <div class="flex gap-4 justify-center">
          <a
            :href="`/@${$page.props.auth.user.username}/habits`"
            class="px-6 py-3 bg-gradient-to-r from-green-500 to-emerald-600 text-white font-semibold rounded-lg hover:from-green-600 hover:to-emerald-700 transition-all shadow-lg shadow-green-500/30"
          >
            Track Habits
          </a>
          <a
            :href="`/@${$page.props.auth.user.username}/entries`"
            class="px-6 py-3 border-2 border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 transition-all"
          >
            Write Entry
          </a>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import AppNav from '../Components/AppNav.vue';
import { ref, onMounted, watch } from 'vue';
import { router } from '@inertiajs/vue3';

const dateRanges = [
  { value: 7, label: '7 days' },
  { value: 30, label: '30 days' },
  { value: 90, label: '90 days' },
  { value: 365, label: '1 year' },
];

const selectedRange = ref(30);
const timelineItems = ref([]);
const stats = ref(null);

const fetchTimeline = async () => {
  try {
    const from = new Date();
    from.setDate(from.getDate() - selectedRange.value);
    const fromStr = from.toISOString().split('T')[0];
    const toStr = new Date().toISOString().split('T')[0];

    const response = await window.axios.get('/api/timeline', {
      params: { from: fromStr, to: toStr }
    });
    timelineItems.value = response.data.data;
  } catch (error) {
    console.error('Error fetching timeline:', error);
  }
};

const fetchStats = async () => {
  try {
    const from = new Date();
    from.setDate(from.getDate() - selectedRange.value);
    const fromStr = from.toISOString().split('T')[0];
    const toStr = new Date().toISOString().split('T')[0];

    const response = await window.axios.get('/api/timeline/stats', {
      params: { from: fromStr, to: toStr }
    });
    stats.value = response.data;
  } catch (error) {
    console.error('Error fetching stats:', error);
  }
};

const selectRange = (days) => {
  selectedRange.value = days;
};

const getCheckInIcon = (type) => {
  const icons = {
    daily: '📅',
    weekly: '📊',
    monthly: '📈',
  };
  return icons[type] || '📝';
};

const getCheckInLabel = (type) => {
  const labels = {
    daily: 'Daily Check-in',
    weekly: 'Weekly Review',
    monthly: 'Monthly Reflection',
  };
  return labels[type] || 'Check-in';
};

const formatKey = (key) => {
  return key.split('_').map(word => word.charAt(0).toUpperCase() + word.slice(1)).join(' ');
};

const formatDate = (dateString) => {
  const date = new Date(dateString);
  const today = new Date();
  const yesterday = new Date(today);
  yesterday.setDate(yesterday.getDate() - 1);

  if (date.toDateString() === today.toDateString()) {
    return 'Today';
  } else if (date.toDateString() === yesterday.toDateString()) {
    return 'Yesterday';
  }

  return date.toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  });
};


watch(selectedRange, () => {
  fetchTimeline();
  fetchStats();
});

onMounted(() => {
  fetchTimeline();
  fetchStats();
});
</script>
