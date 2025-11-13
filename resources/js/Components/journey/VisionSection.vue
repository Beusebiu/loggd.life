<template>
  <div class="bg-white rounded-xl border-2 border-gray-200 p-6 hover:border-green-300 transition-colors">
    <div class="flex justify-between items-start mb-4">
      <div>
        <h2 class="text-xl font-bold text-gray-900">{{ title }}</h2>
        <p class="text-sm text-gray-600 mt-1">{{ subtitle }}</p>
      </div>
      <button
        @click="$emit('togglePrivacy')"
        class="p-2 rounded-lg hover:bg-gray-100 transition"
        :title="isPrivate ? 'Make Public' : 'Make Private'"
      >
        <svg v-if="isPrivate" class="w-5 h-5 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
        </svg>
        <svg v-else class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
          <path d="M10 2a5 5 0 00-5 5v2a2 2 0 00-2 2v5a2 2 0 002 2h10a2 2 0 002-2v-5a2 2 0 00-2-2H7V7a3 3 0 015.905-.75 1 1 0 001.937-.5A5.002 5.002 0 0010 2z"/>
        </svg>
      </button>
    </div>
    <slot></slot>
  </div>
</template>

<script setup>
defineProps({
  title: String,
  subtitle: String,
  isPrivate: Boolean,
});

defineEmits(['togglePrivacy']);
</script>
