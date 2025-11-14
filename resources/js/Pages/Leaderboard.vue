<template>
  <AppLayout>
    <div class="max-w-6xl mx-auto px-3 sm:px-4 md:px-6 lg:px-8 py-4 sm:py-8">
      <!-- Page Header -->
      <div class="mb-6 sm:mb-8">
        <h1 class="text-2xl sm:text-4xl font-bold text-gray-900 mb-2 sm:mb-3">Global Leaderboard</h1>
        <p class="text-sm sm:text-lg text-gray-600">See how you rank among the community's top achievers</p>
        <p class="text-xs sm:text-sm text-gray-500 mt-2 italic">Compete, get inspired, and celebrate each other's progress</p>
      </div>

      <!-- Filter Tabs -->
      <div class="mb-6 border-b border-gray-200">
        <div class="flex gap-4 sm:gap-6 overflow-x-auto">
          <button
            v-for="filter in filters"
            :key="filter.value"
            @click="selectedFilter = filter.value"
            class="pb-3 px-1 text-sm sm:text-base font-medium border-b-2 transition-colors whitespace-nowrap"
            :class="selectedFilter === filter.value
              ? 'border-green-600 text-green-600'
              : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
          >
            {{ filter.icon }} {{ filter.label }}
          </button>
        </div>
      </div>

      <!-- Your Rank Card (if authenticated user is in the list) -->
      <div v-if="currentUserRank" class="mb-6">
        <div class="bg-gradient-to-r from-green-50 to-emerald-50 border-2 border-green-200 rounded-xl p-4 sm:p-6">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-xs sm:text-sm font-medium text-green-800 mb-1">Your Rank</p>
              <p class="text-2xl sm:text-3xl font-bold text-green-900">#{{ currentUserRank.rank }}</p>
            </div>
            <div class="text-right">
              <p class="text-xs sm:text-sm text-gray-600">Level {{ currentUserRank.level }}</p>
              <p class="text-base sm:text-lg font-bold text-gray-900">{{ formatPoints(currentUserRank.points) }} pts</p>
              <p class="text-xs text-gray-500 mt-1">{{ currentUserRank.streak }} day streak</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Top 3 Podium -->
      <div v-if="topThree.length" class="mb-6 sm:mb-8">
        <div class="grid grid-cols-3 gap-2 sm:gap-4">
          <!-- 2nd Place -->
          <div v-if="topThree[1]" class="order-1">
            <div class="bg-gradient-to-br from-slate-100 to-slate-200 rounded-xl p-3 sm:p-4 text-center border-2 border-slate-300 mt-8">
              <div class="text-3xl sm:text-4xl mb-2">🥈</div>
              <Link :href="`/@${topThree[1].username}`" class="block hover:opacity-80 transition">
                <div class="w-12 h-12 sm:w-16 sm:h-16 mx-auto mb-2 rounded-full bg-gradient-to-br from-slate-400 to-slate-500 flex items-center justify-center text-white font-bold text-lg sm:text-2xl">
                  {{ topThree[1].name.charAt(0).toUpperCase() }}
                </div>
                <h3 class="font-bold text-sm sm:text-base text-gray-900 truncate">{{ topThree[1].name }}</h3>
                <p class="text-xs text-gray-600 truncate">@{{ topThree[1].username }}</p>
              </Link>
              <div class="mt-2 pt-2 border-t border-slate-300">
                <p class="text-xs sm:text-sm font-semibold text-gray-900">Level {{ topThree[1].current_level }}</p>
                <p class="text-xs text-gray-600">{{ formatPoints(topThree[1].total_points) }} pts</p>
              </div>
            </div>
          </div>

          <!-- 1st Place -->
          <div v-if="topThree[0]" class="order-2">
            <div class="bg-gradient-to-br from-yellow-100 to-amber-200 rounded-xl p-3 sm:p-4 text-center border-2 border-yellow-400">
              <div class="text-4xl sm:text-5xl mb-2">🥇</div>
              <Link :href="`/@${topThree[0].username}`" class="block hover:opacity-80 transition">
                <div class="w-16 h-16 sm:w-20 sm:h-20 mx-auto mb-2 rounded-full bg-gradient-to-br from-yellow-400 to-amber-500 flex items-center justify-center text-white font-bold text-xl sm:text-3xl ring-4 ring-yellow-300">
                  {{ topThree[0].name.charAt(0).toUpperCase() }}
                </div>
                <h3 class="font-bold text-base sm:text-lg text-gray-900 truncate">{{ topThree[0].name }}</h3>
                <p class="text-xs sm:text-sm text-gray-600 truncate">@{{ topThree[0].username }}</p>
              </Link>
              <div class="mt-2 pt-2 border-t border-yellow-400">
                <p class="text-sm sm:text-base font-semibold text-gray-900">Level {{ topThree[0].current_level }}</p>
                <p class="text-xs sm:text-sm text-gray-600">{{ formatPoints(topThree[0].total_points) }} pts</p>
              </div>
            </div>
          </div>

          <!-- 3rd Place -->
          <div v-if="topThree[2]" class="order-3">
            <div class="bg-gradient-to-br from-orange-100 to-amber-200 rounded-xl p-3 sm:p-4 text-center border-2 border-orange-300 mt-8">
              <div class="text-3xl sm:text-4xl mb-2">🥉</div>
              <Link :href="`/@${topThree[2].username}`" class="block hover:opacity-80 transition">
                <div class="w-12 h-12 sm:w-16 sm:h-16 mx-auto mb-2 rounded-full bg-gradient-to-br from-orange-400 to-amber-500 flex items-center justify-center text-white font-bold text-lg sm:text-2xl">
                  {{ topThree[2].name.charAt(0).toUpperCase() }}
                </div>
                <h3 class="font-bold text-sm sm:text-base text-gray-900 truncate">{{ topThree[2].name }}</h3>
                <p class="text-xs text-gray-600 truncate">@{{ topThree[2].username }}</p>
              </Link>
              <div class="mt-2 pt-2 border-t border-orange-300">
                <p class="text-xs sm:text-sm font-semibold text-gray-900">Level {{ topThree[2].current_level }}</p>
                <p class="text-xs text-gray-600">{{ formatPoints(topThree[2].total_points) }} pts</p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Leaderboard Table -->
      <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <!-- Header -->
        <div class="bg-gray-50 px-4 sm:px-6 py-3 border-b border-gray-200">
          <div class="flex items-center justify-between">
            <h2 class="text-sm sm:text-base font-semibold text-gray-900">All Rankings</h2>
            <div class="text-xs sm:text-sm text-gray-600">
              Showing {{ paginatedUsers.length }} of {{ filteredUsers.length }}
            </div>
          </div>
        </div>

        <!-- List -->
        <div class="divide-y divide-gray-100">
          <div
            v-for="(user, index) in paginatedUsers"
            :key="user.id"
            class="px-4 sm:px-6 py-3 sm:py-4 hover:bg-gray-50 transition"
            :class="{ 'bg-green-50/30': user.username === currentUser?.username }"
          >
            <div class="flex items-center gap-3 sm:gap-4">
              <!-- Rank -->
              <div class="flex-shrink-0 w-8 sm:w-12 text-center">
                <div class="text-base sm:text-lg font-bold text-gray-500">
                  {{ (currentPage - 1) * perPage + index + 1 }}
                </div>
              </div>

              <!-- Avatar -->
              <div class="flex-shrink-0">
                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-full bg-gradient-to-br from-green-400 to-emerald-500 flex items-center justify-center text-white font-bold text-sm sm:text-lg">
                  {{ user.name.charAt(0).toUpperCase() }}
                </div>
              </div>

              <!-- User Info -->
              <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2">
                  <Link
                    :href="`/@${user.username}`"
                    class="font-semibold text-gray-900 hover:text-green-600 transition text-sm sm:text-base truncate"
                  >
                    {{ user.name }}
                  </Link>
                  <span class="text-xs text-gray-500 hidden sm:inline">@{{ user.username }}</span>
                  <span v-if="user.username === currentUser?.username" class="px-2 py-0.5 text-xs font-medium bg-green-100 text-green-800 rounded-full">You</span>
                </div>
                <div class="flex items-center gap-2 sm:gap-3 mt-1">
                  <div class="text-xs sm:text-sm text-gray-600">
                    Level <span class="font-semibold text-green-600">{{ user.current_level }}</span>
                  </div>
                  <div class="text-xs text-gray-400">•</div>
                  <div class="text-xs sm:text-sm text-gray-600">
                    <span class="font-semibold">{{ formatPoints(user.total_points) }}</span> pts
                  </div>
                </div>
              </div>

              <!-- Stats -->
              <div class="hidden sm:flex flex-col items-end gap-1">
                <div class="text-xs text-gray-500">
                  Streak: <span class="font-semibold text-orange-600">{{ user.current_streak }}</span>
                </div>
                <div class="text-xs text-gray-400">
                  Best: {{ user.longest_streak }}
                </div>
              </div>
            </div>
          </div>

          <!-- Empty State -->
          <div v-if="filteredUsers.length === 0" class="px-4 sm:px-6 py-12 text-center">
            <div class="text-4xl mb-3">🏆</div>
            <p class="text-sm text-gray-600">No users found</p>
          </div>
        </div>

        <!-- Pagination -->
        <div v-if="totalPages > 1" class="bg-gray-50 px-4 sm:px-6 py-4 border-t border-gray-200">
          <div class="flex items-center justify-between">
            <button
              @click="currentPage--"
              :disabled="currentPage === 1"
              class="px-3 py-2 text-sm font-medium rounded-lg transition"
              :class="currentPage === 1
                ? 'text-gray-400 cursor-not-allowed'
                : 'text-gray-700 hover:bg-gray-200'"
            >
              Previous
            </button>
            <div class="flex items-center gap-2">
              <button
                v-for="page in visiblePages"
                :key="page"
                @click="currentPage = page"
                class="w-8 h-8 sm:w-10 sm:h-10 rounded-lg text-sm font-medium transition"
                :class="currentPage === page
                  ? 'bg-green-600 text-white'
                  : 'text-gray-700 hover:bg-gray-200'"
              >
                {{ page }}
              </button>
            </div>
            <button
              @click="currentPage++"
              :disabled="currentPage === totalPages"
              class="px-3 py-2 text-sm font-medium rounded-lg transition"
              :class="currentPage === totalPages
                ? 'text-gray-400 cursor-not-allowed'
                : 'text-gray-700 hover:bg-gray-200'"
            >
              Next
            </button>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';

const page = usePage();

const props = defineProps({
  users: {
    type: Array,
    default: () => [],
  },
});

const currentUser = computed(() => page.props.auth.user);
const selectedFilter = ref('points');
const currentPage = ref(1);
const perPage = 25;

const filters = [
  { label: 'Top Points', value: 'points', icon: '🏆' },
  { label: 'Top Level', value: 'level', icon: '📊' },
  { label: 'Longest Streak', value: 'streak', icon: '🔥' },
];

const filteredUsers = computed(() => {
  let sorted = [...props.users];

  switch (selectedFilter.value) {
    case 'level':
      sorted.sort((a, b) => b.current_level - a.current_level || b.total_points - a.total_points);
      break;
    case 'streak':
      sorted.sort((a, b) => b.longest_streak - a.longest_streak || b.total_points - a.total_points);
      break;
    case 'points':
    default:
      sorted.sort((a, b) => b.total_points - a.total_points);
      break;
  }

  return sorted;
});

const topThree = computed(() => filteredUsers.value.slice(0, 3));

const totalPages = computed(() => Math.ceil((filteredUsers.value.length - 3) / perPage));

const paginatedUsers = computed(() => {
  const start = (currentPage.value - 1) * perPage + 3; // Skip top 3
  const end = start + perPage;
  return filteredUsers.value.slice(start, end);
});

const visiblePages = computed(() => {
  const pages = [];
  const maxVisible = 5;
  let start = Math.max(1, currentPage.value - Math.floor(maxVisible / 2));
  let end = Math.min(totalPages.value, start + maxVisible - 1);

  if (end - start + 1 < maxVisible) {
    start = Math.max(1, end - maxVisible + 1);
  }

  for (let i = start; i <= end; i++) {
    pages.push(i);
  }

  return pages;
});

const currentUserRank = computed(() => {
  const userIndex = filteredUsers.value.findIndex(u => u.username === currentUser.value?.username);
  if (userIndex === -1) return null;

  const user = filteredUsers.value[userIndex];
  return {
    rank: userIndex + 1,
    level: user.current_level,
    points: user.total_points,
    streak: user.current_streak,
  };
});

const formatPoints = (points) => {
  if (points >= 1000000) {
    return `${(points / 1000000).toFixed(1)}M`;
  }
  if (points >= 1000) {
    return `${(points / 1000).toFixed(1)}K`;
  }
  return points.toString();
};
</script>
