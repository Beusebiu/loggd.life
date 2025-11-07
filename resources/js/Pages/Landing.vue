<template>
  <div class="min-h-screen bg-gradient-to-b from-white to-gray-50">
    <!-- Navigation -->
    <nav class="bg-white/80 backdrop-blur-md border-b border-gray-200 sticky top-0 z-50">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex justify-between items-center">
        <div class="text-2xl font-bold text-black tracking-tight">LOGGD</div>
        <div class="flex space-x-3">
          <a href="/login" class="px-5 py-2.5 text-sm font-medium text-gray-700 hover:text-black transition-colors">
            Login
          </a>
          <a href="/register" class="px-6 py-2.5 text-sm font-semibold bg-gradient-to-r from-green-500 to-emerald-600 text-white rounded-lg hover:from-green-600 hover:to-emerald-700 transition-all shadow-lg shadow-green-500/20 hover:shadow-xl hover:shadow-green-500/30 hover:-translate-y-0.5">
            Sign Up
          </a>
        </div>
      </div>
    </nav>

    <!-- Hero Section -->
    <section class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-20 pb-32 text-center overflow-hidden">
      <!-- Decorative background elements -->
      <div class="absolute top-20 left-10 w-72 h-72 bg-green-200 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob"></div>
      <div class="absolute top-40 right-10 w-72 h-72 bg-emerald-200 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob animation-delay-2000"></div>
      <div class="absolute -bottom-8 left-1/2 w-72 h-72 bg-teal-200 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob animation-delay-4000"></div>

      <div class="relative z-10">
        <div class="inline-block mb-6 px-4 py-2 bg-green-100 text-green-700 rounded-full text-sm font-semibold">
          ✨ Your Personal Growth OS
        </div>
        <h1 class="text-6xl sm:text-7xl lg:text-8xl font-bold text-black mb-8 leading-tight">
          Log your life.<br />
          <span class="bg-gradient-to-r from-green-600 to-emerald-600 bg-clip-text text-transparent">Watch yourself grow.</span>
        </h1>
        <p class="text-xl sm:text-2xl text-gray-600 mb-10 max-w-3xl mx-auto leading-relaxed">
          A flexible system to track your life. Daily logs, weekly reviews, video journals, and visual progress. Finally, a tracking system that adapts to you.
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
          <a href="/register" class="group px-8 py-4 bg-gradient-to-r from-green-500 to-emerald-600 text-white text-lg font-semibold rounded-xl hover:from-green-600 hover:to-emerald-700 transition-all shadow-2xl shadow-green-500/30 hover:shadow-green-500/50 hover:-translate-y-1 flex items-center gap-2">
            Get Started Free
            <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
            </svg>
          </a>
          <a href="#features" class="px-8 py-4 bg-white text-gray-700 text-lg font-semibold rounded-xl hover:bg-gray-50 transition-all border-2 border-gray-200 hover:border-gray-300">
            Learn More
          </a>
        </div>
      </div>
    </section>

    <!-- Demo Graph Section -->
    <section class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
      <div class="relative bg-white rounded-2xl p-8 sm:p-12 border border-gray-200 shadow-2xl shadow-gray-200/50 hover:shadow-gray-300/50 transition-shadow">
        <!-- Glow effect -->
        <div class="absolute -inset-1 bg-gradient-to-r from-green-600 to-emerald-600 rounded-2xl blur-lg opacity-10"></div>

        <div class="relative">
          <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-semibold text-gray-900">Your activity, visualized</h3>
            <span class="px-3 py-1 bg-green-100 text-green-700 text-sm font-medium rounded-full">365 days</span>
          </div>

          <!-- Simple demo contribution graph -->
          <div class="flex space-x-1 overflow-x-auto pb-2">
            <div v-for="week in 52" :key="week" class="flex flex-col space-y-1">
              <div
                v-for="day in 7"
                :key="`${week}-${day}`"
                :class="[
                  'w-3 h-3 rounded-sm transition-all hover:scale-125 cursor-pointer',
                  getRandomColor()
                ]"
              />
            </div>
          </div>

          <div class="flex items-center justify-between mt-6 pt-4 border-t border-gray-100">
            <div class="flex items-center space-x-2 text-xs text-gray-500">
              <span>Less</span>
              <div class="flex space-x-1">
                <div class="w-3 h-3 bg-gray-100 border border-gray-200 rounded-sm"></div>
                <div class="w-3 h-3 bg-green-200 rounded-sm"></div>
                <div class="w-3 h-3 bg-green-400 rounded-sm"></div>
                <div class="w-3 h-3 bg-green-600 rounded-sm"></div>
                <div class="w-3 h-3 bg-green-800 rounded-sm"></div>
              </div>
              <span>More</span>
            </div>
            <div class="text-sm text-gray-600">
              <span class="font-semibold text-green-600">237 contributions</span> in the last year
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="relative py-24 overflow-hidden">
      <!-- Background pattern -->
      <div class="absolute inset-0 bg-gradient-to-b from-white via-gray-50 to-white"></div>

      <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-20">
          <h2 class="text-4xl sm:text-5xl font-bold text-black mb-4">
            Everything you need to track your <span class="bg-gradient-to-r from-green-600 to-emerald-600 bg-clip-text text-transparent">growth</span>
          </h2>
          <p class="text-xl text-gray-600 max-w-2xl mx-auto">
            Built for flexibility. Designed for real life. Made to help you actually stick with it.
          </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
          <!-- Feature 1 -->
          <div class="group relative bg-white rounded-2xl p-8 shadow-lg hover:shadow-2xl transition-all hover:-translate-y-2 border border-gray-100">
            <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-green-100 to-emerald-100 rounded-bl-full opacity-50"></div>
            <div class="relative">
              <div class="text-5xl mb-6 transform group-hover:scale-110 transition-transform">📊</div>
              <h3 class="text-2xl font-bold text-black mb-4">Visual proof of progress</h3>
              <p class="text-gray-600 text-lg leading-relaxed">
                GitHub-style contribution graph shows your entire journey. See patterns, track streaks, and watch your life fill up with activity over time.
              </p>
            </div>
          </div>

          <!-- Feature 2 -->
          <div class="group relative bg-white rounded-2xl p-8 shadow-lg hover:shadow-2xl transition-all hover:-translate-y-2 border border-gray-100">
            <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-blue-100 to-indigo-100 rounded-bl-full opacity-50"></div>
            <div class="relative">
              <div class="text-5xl mb-6 transform group-hover:scale-110 transition-transform">📝</div>
              <h3 class="text-2xl font-bold text-black mb-4">Log your way</h3>
              <p class="text-gray-600 text-lg leading-relaxed">
                Daily check-ins, weekly reviews, quick notes, or video journals. Flexible templates that adapt to how you actually work, not how you "should" work.
              </p>
            </div>
          </div>

          <!-- Feature 3 -->
          <div class="group relative bg-white rounded-2xl p-8 shadow-lg hover:shadow-2xl transition-all hover:-translate-y-2 border border-gray-100">
            <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-purple-100 to-pink-100 rounded-bl-full opacity-50"></div>
            <div class="relative">
              <div class="text-5xl mb-6 transform group-hover:scale-110 transition-transform">🎥</div>
              <h3 class="text-2xl font-bold text-black mb-4">Watch yourself grow</h3>
              <p class="text-gray-600 text-lg leading-relaxed">
                Record video journals and look back months later. See how far you've come. "One year ago today" memories show your actual transformation.
              </p>
            </div>
          </div>

          <!-- Feature 4 -->
          <div class="group relative bg-white rounded-2xl p-8 shadow-lg hover:shadow-2xl transition-all hover:-translate-y-2 border border-gray-100">
            <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-orange-100 to-yellow-100 rounded-bl-full opacity-50"></div>
            <div class="relative">
              <div class="text-5xl mb-6 transform group-hover:scale-110 transition-transform">🌐</div>
              <h3 class="text-2xl font-bold text-black mb-4">Share your journey (optional)</h3>
              <p class="text-gray-600 text-lg leading-relaxed">
                Public profile to build in public. Share your contribution graph, progress updates, and inspire others while staying accountable.
              </p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- CTA Section -->
    <section class="relative bg-gradient-to-br from-gray-900 via-black to-gray-900 text-white py-32 overflow-hidden">
      <!-- Animated background elements -->
      <div class="absolute top-0 left-1/4 w-96 h-96 bg-green-500 rounded-full mix-blend-multiply filter blur-3xl opacity-10 animate-blob"></div>
      <div class="absolute bottom-0 right-1/4 w-96 h-96 bg-emerald-500 rounded-full mix-blend-multiply filter blur-3xl opacity-10 animate-blob animation-delay-2000"></div>

      <div class="relative max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-4xl sm:text-5xl font-bold mb-6 leading-tight">
          Ready to start tracking<br />your life?
        </h2>
        <p class="text-xl text-gray-400 mb-10 max-w-2xl mx-auto">
          Join thousands of people building better habits and tracking their personal growth journey.
        </p>
        <a href="/register" class="group inline-flex items-center gap-3 px-10 py-5 bg-gradient-to-r from-green-500 to-emerald-600 text-white text-lg font-bold rounded-xl hover:from-green-600 hover:to-emerald-700 transition-all shadow-2xl shadow-green-500/30 hover:shadow-green-500/50 hover:-translate-y-1">
          Get Started Free
          <svg class="w-6 h-6 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
          </svg>
        </a>
        <p class="mt-6 text-sm text-gray-500">
          No credit card required • Free forever
        </p>
      </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-gray-300 py-12 text-center">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-xl font-semibold text-white mb-4">Loggd</div>
        <p class="text-sm mb-6">© 2025 Loggd. All rights reserved.</p>
        <div class="flex justify-center space-x-6 text-sm">
          <a href="/login" class="hover:text-white transition-colors">Login</a>
          <a href="/register" class="hover:text-white transition-colors">Sign Up</a>
          <a href="#" class="hover:text-white transition-colors">Privacy</a>
          <a href="#" class="hover:text-white transition-colors">Terms</a>
        </div>
      </div>
    </footer>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const getRandomColor = () => {
  const colors = [
    'bg-gray-100 border border-gray-200',
    'bg-green-200',
    'bg-green-400',
    'bg-green-600',
    'bg-green-800',
  ];

  // Generate realistic pattern (more activity in recent months)
  const random = Math.random();
  if (random < 0.2) return colors[0]; // 20% empty
  if (random < 0.5) return colors[1]; // 30% light
  if (random < 0.75) return colors[2]; // 25% medium
  if (random < 0.9) return colors[3]; // 15% high
  return colors[4]; // 10% very high
};
</script>
