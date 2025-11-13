<template>
  <AppLayout>
    <div class="min-h-screen bg-gradient-to-br from-gray-50 via-white to-green-50/30 flex items-center justify-center px-4 py-12">
      <div class="max-w-lg w-full">
        <!-- Decorative elements -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
          <div class="absolute top-20 left-10 w-72 h-72 bg-green-100 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob"></div>
          <div class="absolute top-40 right-10 w-72 h-72 bg-gray-200 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob animation-delay-2000"></div>
          <div class="absolute -bottom-20 left-1/2 w-72 h-72 bg-green-50 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob animation-delay-4000"></div>
        </div>

        <!-- Private Profile Card -->
        <div class="relative bg-white/80 backdrop-blur-sm rounded-3xl shadow-2xl border border-gray-200/50 overflow-hidden">
          <!-- Header with gradient -->
          <div class="bg-gradient-to-br from-slate-100 via-gray-50 to-green-50 px-8 py-16 text-center relative overflow-hidden">
            <!-- Decorative pattern -->
            <div class="absolute inset-0 opacity-5">
              <div class="absolute inset-0" style="background-image: radial-gradient(circle, #000 1px, transparent 1px); background-size: 20px 20px;"></div>
            </div>

            <div class="relative">
              <div class="w-24 h-24 rounded-full bg-gradient-to-br from-gray-400 to-gray-500 flex items-center justify-center text-white font-bold text-4xl mx-auto mb-5 shadow-xl ring-4 ring-white/50">
                {{ profileUser.name.charAt(0).toUpperCase() }}
              </div>
              <h1 class="text-3xl font-bold text-gray-900 mb-1">{{ profileUser.name }}</h1>
              <p class="text-gray-600 font-medium">@{{ profileUser.username }}</p>
            </div>
          </div>

          <!-- Message -->
          <div class="px-8 py-10 text-center relative">
            <div class="w-20 h-20 rounded-2xl bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center mx-auto mb-5 shadow-lg">
              <span class="text-5xl">🔒</span>
            </div>
            <h2 class="text-2xl font-bold text-gray-900 mb-3">This Profile is Private</h2>
            <p class="text-gray-600 leading-relaxed max-w-sm mx-auto">
              {{ profileUser.name }} has chosen to keep their profile private. Only they can view their habits, goals, and progress.
            </p>
          </div>

          <!-- Divider -->
          <div class="px-8">
            <div class="h-px bg-gradient-to-r from-transparent via-gray-200 to-transparent"></div>
          </div>

          <!-- Actions -->
          <div class="px-8 py-8 flex flex-col gap-3">
            <Link
              href="/"
              class="group relative w-full px-6 py-4 bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white font-semibold rounded-xl transition-all shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 text-center overflow-hidden"
            >
              <span class="relative z-10 flex items-center justify-center gap-2">
                <span>←</span>
                Go to Home
              </span>
              <div class="absolute inset-0 bg-gradient-to-r from-white/0 via-white/10 to-white/0 transform translate-x-[-100%] group-hover:translate-x-[100%] transition-transform duration-1000"></div>
            </Link>
            <Link
              v-if="!$page.props.auth.user"
              href="/register"
              class="w-full px-6 py-4 bg-white hover:bg-gray-50 text-gray-700 font-semibold rounded-xl border-2 border-gray-200 hover:border-green-300 transition-all shadow-md hover:shadow-lg transform hover:-translate-y-0.5 text-center"
            >
              <span class="flex items-center justify-center gap-2">
                <span>🚀</span>
                Create Your Own Profile
              </span>
            </Link>
          </div>
        </div>

        <!-- Additional Info -->
        <div v-if="$page.props.auth.user && $page.props.auth.user.id === profileUser.id" class="mt-8 text-center relative">
          <div class="inline-block px-6 py-3 bg-white/60 backdrop-blur-sm rounded-full shadow-sm border border-gray-200/50">
            <p class="text-sm text-gray-600">
              Want to make your profile public?
              <Link :href="`/@${$page.props.auth.user.username}/settings`" class="text-green-600 hover:text-green-700 font-bold hover:underline transition-colors">
                Update settings
              </Link>
            </p>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import AppLayout from '../Layouts/AppLayout.vue';

const props = defineProps({
  profileUser: Object,
});
</script>
