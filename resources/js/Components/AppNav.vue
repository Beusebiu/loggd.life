<template>
  <header class="bg-white/80 backdrop-blur-md border-b border-gray-200 sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex justify-between items-center">
      <div class="flex items-center space-x-6">
        <a href="/" class="text-2xl font-bold text-black tracking-tight">LOGGD</a>
        <nav v-if="$page.props.auth?.user" class="flex space-x-4">
          <a
            :href="`/@${$page.props.auth.user.username}`"
            class="text-sm px-3 py-2 rounded-lg"
            :class="isActive('profile') ? 'font-medium bg-gray-100 text-black' : 'text-gray-600 hover:text-black hover:bg-gray-50'"
          >
            Profile
          </a>
          <a
            :href="`/@${$page.props.auth.user.username}/habits`"
            class="text-sm px-3 py-2 rounded-lg"
            :class="isActive('habits') ? 'font-medium bg-gray-100 text-black' : 'text-gray-600 hover:text-black hover:bg-gray-50'"
          >
            Habits
          </a>
          <a
            :href="`/@${$page.props.auth.user.username}/entries`"
            class="text-sm px-3 py-2 rounded-lg"
            :class="isActive('entries') ? 'font-medium bg-gray-100 text-black' : 'text-gray-600 hover:text-black hover:bg-gray-50'"
          >
            Entries
          </a>
          <a
            :href="`/@${$page.props.auth.user.username}/check-ins`"
            class="text-sm px-3 py-2 rounded-lg"
            :class="isActive('check-ins') ? 'font-medium bg-gray-100 text-black' : 'text-gray-600 hover:text-black hover:bg-gray-50'"
          >
            Check-ins
          </a>
          <a
            :href="`/@${$page.props.auth.user.username}/timeline`"
            class="text-sm px-3 py-2 rounded-lg"
            :class="isActive('timeline') ? 'font-medium bg-gray-100 text-black' : 'text-gray-600 hover:text-black hover:bg-gray-50'"
          >
            Timeline
          </a>
          <a
            :href="`/@${$page.props.auth.user.username}/stats`"
            class="text-sm px-3 py-2 rounded-lg"
            :class="isActive('stats') ? 'font-medium bg-gray-100 text-black' : 'text-gray-600 hover:text-black hover:bg-gray-50'"
          >
            Stats
          </a>
        </nav>
      </div>
      <div v-if="$page.props.auth?.user" class="flex items-center gap-4">
        <a
          :href="`/@${$page.props.auth.user.username}/settings`"
          class="text-sm text-gray-600 hover:text-black px-4 py-2 rounded-lg hover:bg-gray-50"
        >
          Settings
        </a>
        <form @submit.prevent="logout">
          <button type="submit" class="text-sm text-gray-600 hover:text-black px-4 py-2">
            Logout
          </button>
        </form>
      </div>
      <div v-else class="flex space-x-3">
        <a href="/login" class="px-5 py-2.5 text-sm font-medium text-gray-700 hover:text-black transition-colors">
          Login
        </a>
        <a href="/register" class="px-6 py-2.5 text-sm font-semibold bg-gradient-to-r from-green-500 to-emerald-600 text-white rounded-lg hover:from-green-600 hover:to-emerald-700 transition-all shadow-lg shadow-green-500/20">
          Sign Up
        </a>
      </div>
    </div>
  </header>
</template>

<script setup>
import { router } from '@inertiajs/vue3';

defineProps({
  currentPage: {
    type: String,
    default: ''
  }
});

const isActive = (page) => {
  const path = window.location.pathname;

  if (page === 'profile') {
    // Profile is active if we're at /@username with no additional path
    return path.match(/^\/@[^/]+$/) !== null;
  }

  return path.includes(`/${page}`);
};

const logout = () => {
  router.post('/logout');
};
</script>
