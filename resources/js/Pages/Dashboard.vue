<template>
  <div class="min-h-screen bg-white">
    <!-- Simple header -->
    <header class="border-b border-gray-200">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex justify-between items-center">
        <div class="flex items-center space-x-6">
          <h1 class="text-2xl font-semibold text-black">LOGGD</h1>
          <nav class="flex space-x-4">
            <a href="/app" class="text-sm font-medium text-black">Dashboard</a>
            <a href="/app/habits" class="text-sm text-gray-600 hover:text-black">Habits</a>
            <a href="/app/entries" class="text-sm text-gray-600 hover:text-black">Entries</a>
          </nav>
        </div>
        <form @submit.prevent="logout">
          <button type="submit" class="text-sm text-gray-600 hover:text-black">
            Logout
          </button>
        </form>
      </div>
    </header>

    <!-- Main content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div class="space-y-8">
        <!-- Welcome Section -->
        <div class="text-center space-y-4">
          <h2 class="text-3xl font-semibold text-black">
            Welcome to your Dashboard!
          </h2>
          <p class="text-gray-600">
            Track your habits, log your journey, and watch yourself grow.
          </p>
        </div>

        <!-- Contribution Graph -->
        <div class="bg-white border border-gray-200 rounded-lg p-6">
          <h3 class="text-lg font-semibold text-black mb-4">Your Activity</h3>
          <ContributionGraph :days="365" />
        </div>

        <!-- System Status (for development reference) -->
        <div class="p-6 bg-gray-50 rounded-lg">
          <h3 class="text-sm font-medium text-gray-700 mb-3">System Status</h3>
          <p class="text-sm text-gray-500 mb-2">✅ Frontend: Vue.js + Inertia.js + Tailwind CSS</p>
          <p class="text-sm text-gray-500 mb-2">✅ Backend: Laravel 12 + Sanctum + MySQL</p>
          <p class="text-sm text-gray-500 mb-2">✅ Database: 9 tables with relationships</p>
          <p class="text-sm text-gray-500">✅ API: 31 endpoints ready</p>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import { router } from '@inertiajs/vue3';
import ContributionGraph from '../Components/ContributionGraph.vue';

const logout = () => {
  router.post('/logout');
};
</script>
