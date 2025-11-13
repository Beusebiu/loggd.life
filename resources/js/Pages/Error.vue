<template>
  <div class="min-h-screen bg-gradient-to-br from-gray-50 via-white to-green-50/30 flex items-center justify-center px-4 py-12">
    <div class="max-w-2xl w-full">
      <!-- Decorative elements -->
      <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute top-20 left-10 w-72 h-72 bg-green-100 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob"></div>
        <div class="absolute top-40 right-10 w-72 h-72 bg-gray-200 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob animation-delay-2000"></div>
        <div class="absolute -bottom-20 left-1/2 w-72 h-72 bg-green-50 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob animation-delay-4000"></div>
      </div>

      <!-- Error Card -->
      <div class="relative bg-white/80 backdrop-blur-sm rounded-3xl shadow-2xl border border-gray-200/50 overflow-hidden">
        <!-- Header with gradient -->
        <div class="bg-gradient-to-br from-slate-100 via-gray-50 to-green-50 px-8 py-16 text-center relative overflow-hidden">
          <!-- Decorative pattern -->
          <div class="absolute inset-0 opacity-5">
            <div class="absolute inset-0" style="background-image: radial-gradient(circle, #000 1px, transparent 1px); background-size: 20px 20px;"></div>
          </div>

          <div class="relative">
            <!-- Error Code -->
            <div class="text-8xl font-black text-gray-900 mb-4 tracking-tight">
              {{ status }}
            </div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ title }}</h1>
            <p class="text-gray-600 font-medium text-lg">{{ description }}</p>
          </div>
        </div>

        <!-- Content -->
        <div class="px-8 py-10 text-center relative">
          <div class="w-20 h-20 rounded-2xl bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center mx-auto mb-5 shadow-lg">
            <span class="text-5xl">{{ emoji }}</span>
          </div>
          <p class="text-gray-600 leading-relaxed max-w-md mx-auto mb-6">
            {{ message }}
          </p>

          <!-- Helpful suggestions for 404 -->
          <div v-if="status === 404" class="bg-green-50 border border-green-200 rounded-xl p-4 max-w-md mx-auto mb-6">
            <p class="text-sm text-gray-700 font-medium mb-2">Looking for something?</p>
            <ul class="text-sm text-gray-600 space-y-1 text-left">
              <li class="flex items-center gap-2">
                <span class="text-green-600">•</span>
                <span>Check the URL for typos</span>
              </li>
              <li class="flex items-center gap-2">
                <span class="text-green-600">•</span>
                <span>The page may have been moved or deleted</span>
              </li>
              <li class="flex items-center gap-2">
                <span class="text-green-600">•</span>
                <span>Try starting from the home page</span>
              </li>
            </ul>
          </div>
        </div>

        <!-- Divider -->
        <div class="px-8">
          <div class="h-px bg-gradient-to-r from-transparent via-gray-200 to-transparent"></div>
        </div>

        <!-- Actions -->
        <div class="px-8 py-8 flex flex-col sm:flex-row gap-3">
          <button
            @click="goBack"
            class="flex-1 px-6 py-4 bg-white hover:bg-gray-50 text-gray-700 font-semibold rounded-xl border-2 border-gray-200 hover:border-green-300 transition-all shadow-md hover:shadow-lg transform hover:-translate-y-0.5 text-center"
          >
            <span class="flex items-center justify-center gap-2">
              <span>←</span>
              Go Back
            </span>
          </button>
          <a
            :href="homeUrl"
            class="flex-1 group relative px-6 py-4 bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white font-semibold rounded-xl transition-all shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 text-center overflow-hidden"
          >
            <span class="relative z-10 flex items-center justify-center gap-2">
              <span>🏠</span>
              Go to Home
            </span>
            <div class="absolute inset-0 bg-gradient-to-r from-white/0 via-white/10 to-white/0 transform translate-x-[-100%] group-hover:translate-x-[100%] transition-transform duration-1000"></div>
          </a>
        </div>
      </div>

      <!-- Additional Info -->
      <div class="mt-8 text-center relative">
        <div class="inline-block px-6 py-3 bg-white/60 backdrop-blur-sm rounded-full shadow-sm border border-gray-200/50">
          <p class="text-sm text-gray-600">
            Need help?
            <a href="mailto:support@loggd.life" class="text-green-600 hover:text-green-700 font-bold hover:underline transition-colors ml-1">
              Contact support
            </a>
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';

const props = defineProps({
  status: Number,
});

const page = usePage();
const user = computed(() => page.props.auth?.user);

const homeUrl = computed(() => {
  if (user.value) {
    return `/@${user.value.username}`;
  }
  return '/';
});

const title = computed(() => {
  const titles = {
    503: 'Service Unavailable',
    500: 'Server Error',
    404: 'Page Not Found',
    403: 'Forbidden',
  };
  return titles[props.status] || 'Error';
});

const description = computed(() => {
  const descriptions = {
    503: 'Temporarily Down for Maintenance',
    500: 'Something Went Wrong',
    404: 'This Page Doesn\'t Exist',
    403: 'Access Denied',
  };
  return descriptions[props.status] || 'An error occurred';
});

const emoji = computed(() => {
  const emojis = {
    503: '🔧',
    500: '😵',
    404: '🔍',
    403: '🔒',
  };
  return emojis[props.status] || '⚠️';
});

const message = computed(() => {
  const messages = {
    503: 'We\'re making things better! Our service is temporarily unavailable. Please check back in a few minutes.',
    500: 'Oops! Something unexpected happened on our end. Don\'t worry, our team has been notified and is working on a fix.',
    404: 'The page you\'re looking for doesn\'t exist. It may have been moved, deleted, or the URL might be incorrect.',
    403: 'You don\'t have permission to access this page. Please check your credentials or contact support if you believe this is an error.',
  };
  return messages[props.status] || 'An unexpected error occurred. Please try again or contact support if the problem persists.';
});

const goBack = () => {
  window.history.back();
};
</script>

<style scoped>
@keyframes blob {
  0% {
    transform: translate(0px, 0px) scale(1);
  }
  33% {
    transform: translate(30px, -50px) scale(1.1);
  }
  66% {
    transform: translate(-20px, 20px) scale(0.9);
  }
  100% {
    transform: translate(0px, 0px) scale(1);
  }
}

.animate-blob {
  animation: blob 7s infinite;
}

.animation-delay-2000 {
  animation-delay: 2s;
}

.animation-delay-4000 {
  animation-delay: 4s;
}
</style>
