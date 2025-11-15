<template>
  <div class="mb-8 border-b border-gray-200 dark:border-gray-700">
    <nav class="flex gap-6" aria-label="Journey navigation">
      <Link
        :href="`/@${user.username}/journey`"
        class="pb-4 px-1 text-sm font-medium border-b-2 transition-colors"
        :class="isActive('journey') ? 'border-green-600 dark:border-green-500 text-green-600 dark:text-green-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600'"
      >
        🧭 Vision
      </Link>
      <Link
        :href="`/@${user.username}/journey/goals`"
        class="pb-4 px-1 text-sm font-medium border-b-2 transition-colors"
        :class="isActive('goals') ? 'border-green-600 dark:border-green-500 text-green-600 dark:text-green-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600'"
      >
        🎯 Goals
      </Link>
      <Link
        :href="`/@${user.username}/journey/weekly`"
        class="pb-4 px-1 text-sm font-medium border-b-2 transition-colors"
        :class="isActive('weekly') ? 'border-green-600 dark:border-green-500 text-green-600 dark:text-green-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600'"
      >
        📊 Weekly
      </Link>
      <Link
        :href="`/@${user.username}/journey/daily`"
        class="pb-4 px-1 text-sm font-medium border-b-2 transition-colors"
        :class="isActive('daily') ? 'border-green-600 dark:border-green-500 text-green-600 dark:text-green-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600'"
      >
        📝 Daily
      </Link>
    </nav>
  </div>
</template>

<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();
const user = computed(() => page.props.auth.user);

const isActive = (section) => {
  const currentPath = page.url;

  if (section === 'journey') {
    return currentPath === `/@${user.value.username}/journey`;
  }

  return currentPath.includes(`/journey/${section}`);
};
</script>
