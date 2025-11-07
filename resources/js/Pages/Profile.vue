<template>
  <div class="min-h-screen bg-gradient-to-b from-white to-gray-50">
    <AppNav current-page="profile" />

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div class="space-y-8">
        <!-- Profile Header -->
        <div class="bg-white rounded-2xl p-8 border border-gray-200 shadow-lg">
          <div class="flex items-start justify-between">
            <div>
              <div class="flex items-center gap-3 mb-2">
                <h1 class="text-4xl font-bold text-black">{{ profileUser.name }}</h1>
                <span v-if="!profileUser.profile_public" class="px-3 py-1 bg-gray-100 text-gray-600 text-sm rounded-full">
                  🔒 Private
                </span>
                <span v-else class="px-3 py-1 bg-green-100 text-green-700 text-sm rounded-full">
                  🌐 Public
                </span>
              </div>
              <p class="text-xl text-gray-600 mb-3">@{{ profileUser.username }}</p>
              <p v-if="profileUser.bio" class="text-gray-700 max-w-2xl">{{ profileUser.bio }}</p>
              <p v-else class="text-gray-400 italic">No bio yet</p>
              <p class="text-sm text-gray-500 mt-4">Member since {{ formatDate(profileUser.created_at) }}</p>
            </div>
            <div v-if="isOwnProfile">
              <button class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                Edit Profile
              </button>
            </div>
          </div>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
          <!-- Total Habits -->
          <div class="bg-white rounded-xl p-6 border border-gray-200 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-2">
              <span class="text-3xl">📊</span>
              <span class="text-2xl font-bold text-black">{{ stats.totalHabits }}</span>
            </div>
            <p class="text-sm text-gray-600">Total Habits</p>
            <p class="text-xs text-gray-500 mt-1">{{ stats.activeHabits }} active</p>
          </div>

          <!-- Total Entries -->
          <div class="bg-white rounded-xl p-6 border border-gray-200 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-2">
              <span class="text-3xl">📝</span>
              <span class="text-2xl font-bold text-black">{{ stats.totalEntries }}</span>
            </div>
            <p class="text-sm text-gray-600">Total Entries</p>
            <p class="text-xs text-gray-500 mt-1">Life logged</p>
          </div>

          <!-- Total Checks -->
          <div class="bg-white rounded-xl p-6 border border-gray-200 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-2">
              <span class="text-3xl">✅</span>
              <span class="text-2xl font-bold text-black">{{ stats.totalChecks }}</span>
            </div>
            <p class="text-sm text-gray-600">Total Checks</p>
            <p class="text-xs text-gray-500 mt-1">Habits completed</p>
          </div>

          <!-- Longest Streak -->
          <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-xl p-6 border border-green-200 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-2">
              <span class="text-3xl">🔥</span>
              <span class="text-2xl font-bold text-green-700">{{ stats.longestStreak }}</span>
            </div>
            <p class="text-sm text-green-700 font-medium">Longest Streak</p>
            <p class="text-xs text-green-600 mt-1">Days in a row</p>
          </div>
        </div>

        <!-- Current Streaks -->
        <div v-if="stats.streaks && stats.streaks.length > 0" class="bg-white rounded-2xl p-8 border border-gray-200 shadow-lg">
          <h3 class="text-2xl font-bold text-black mb-6 flex items-center gap-2">
            <span>🔥</span>
            Current Streaks
          </h3>
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <div
              v-for="streak in stats.streaks"
              :key="streak.habit_name"
              class="p-4 bg-gradient-to-br from-gray-50 to-white rounded-xl border border-gray-200 hover:shadow-md transition-shadow"
            >
              <div class="flex items-center justify-between mb-2">
                <span class="text-2xl">{{ streak.habit_emoji }}</span>
                <span class="text-xl font-bold text-green-600">{{ streak.streak }} days</span>
              </div>
              <p class="text-sm font-medium text-gray-800">{{ streak.habit_name }}</p>
            </div>
          </div>
        </div>

        <!-- Contribution Graph -->
        <div class="bg-white rounded-2xl p-8 border border-gray-200 shadow-lg">
          <div class="flex items-center justify-between mb-6">
            <h3 class="text-2xl font-bold text-black">Your Activity</h3>
            <span class="px-3 py-1 bg-green-100 text-green-700 text-sm font-medium rounded-full">365 days</span>
          </div>
          <ContributionGraph :days="365" />
        </div>

        <!-- Quick Actions (Only for own profile) -->
        <div v-if="isOwnProfile" class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <a
            :href="`/@${profileUser.username}/habits`"
            class="group p-8 bg-gradient-to-br from-blue-50 to-indigo-50 rounded-2xl border border-blue-200 hover:shadow-xl transition-all hover:-translate-y-1"
          >
            <div class="text-4xl mb-4 group-hover:scale-110 transition-transform">📊</div>
            <h3 class="text-xl font-bold text-black mb-2">Track Habits</h3>
            <p class="text-gray-600">View and check off your daily habits</p>
          </a>

          <a
            :href="`/@${profileUser.username}/entries`"
            class="group p-8 bg-gradient-to-br from-purple-50 to-pink-50 rounded-2xl border border-purple-200 hover:shadow-xl transition-all hover:-translate-y-1"
          >
            <div class="text-4xl mb-4 group-hover:scale-110 transition-transform">📝</div>
            <h3 class="text-xl font-bold text-black mb-2">Write Entry</h3>
            <p class="text-gray-600">Log your thoughts and daily reflections</p>
          </a>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import ContributionGraph from '../Components/ContributionGraph.vue';
import AppNav from '../Components/AppNav.vue';

const props = defineProps({
  profileUser: Object,
  stats: Object,
  isOwnProfile: Boolean,
});

const page = usePage();

// Check if we're on the profile page (not habits or entries)
const isProfilePage = computed(() => {
  return window.location.pathname === `/@${props.profileUser.username}`;
});

const formatDate = (dateString) => {
  const date = new Date(dateString);
  return date.toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' });
};

</script>
