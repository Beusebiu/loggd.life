<template>
  <div class="min-h-screen bg-gradient-to-br from-gray-50 via-white to-green-50 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <!-- Logo and Header -->
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
      <a href="/" class="flex justify-center">
        <h1 class="text-4xl font-bold text-black tracking-tight">LOGGD</h1>
      </a>
      <h2 class="mt-6 text-center text-3xl font-bold text-black">
        Create your account
      </h2>
      <p class="mt-2 text-center text-sm text-gray-600">
        Start tracking your growth journey today
      </p>
    </div>

    <!-- Registration Form -->
    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
      <div class="bg-white py-8 px-4 shadow-2xl shadow-gray-200/50 sm:rounded-2xl sm:px-10 border border-gray-200">
        <form @submit.prevent="submit" class="space-y-6">
          <!-- Name Field -->
          <div>
            <label for="name" class="block text-sm font-medium text-gray-700">
              Full Name
            </label>
            <div class="mt-1">
              <input
                id="name"
                v-model="form.name"
                type="text"
                required
                autocomplete="name"
                class="appearance-none block w-full px-3 py-2.5 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all"
                :class="{ 'border-red-500': form.errors.name }"
                placeholder="John Doe"
              />
              <p v-if="form.errors.name" class="mt-2 text-sm text-red-600">
                {{ form.errors.name }}
              </p>
            </div>
          </div>

          <!-- Username Field -->
          <div>
            <label for="username" class="block text-sm font-medium text-gray-700">
              Username
            </label>
            <div class="mt-1 relative">
              <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <span class="text-gray-500 sm:text-sm">@</span>
              </div>
              <input
                id="username"
                v-model="form.username"
                type="text"
                required
                autocomplete="username"
                class="appearance-none block w-full pl-7 pr-3 py-2.5 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all"
                :class="{ 'border-red-500': form.errors.username }"
                placeholder="johndoe"
                @input="validateUsername"
              />
              <p v-if="form.errors.username" class="mt-2 text-sm text-red-600">
                {{ form.errors.username }}
              </p>
              <p v-else class="mt-2 text-xs text-gray-500">
                Your unique identifier. Letters, numbers, dashes, and underscores only.
              </p>
            </div>
          </div>

          <!-- Email Field -->
          <div>
            <label for="email" class="block text-sm font-medium text-gray-700">
              Email address
            </label>
            <div class="mt-1">
              <input
                id="email"
                v-model="form.email"
                type="email"
                required
                autocomplete="email"
                class="appearance-none block w-full px-3 py-2.5 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all"
                :class="{ 'border-red-500': form.errors.email }"
                placeholder="you@example.com"
              />
              <p v-if="form.errors.email" class="mt-2 text-sm text-red-600">
                {{ form.errors.email }}
              </p>
            </div>
          </div>

          <!-- Password Field -->
          <div>
            <label for="password" class="block text-sm font-medium text-gray-700">
              Password
            </label>
            <div class="mt-1">
              <input
                id="password"
                v-model="form.password"
                type="password"
                required
                autocomplete="new-password"
                class="appearance-none block w-full px-3 py-2.5 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all"
                :class="{ 'border-red-500': form.errors.password }"
                placeholder="••••••••"
              />
              <p v-if="form.errors.password" class="mt-2 text-sm text-red-600">
                {{ form.errors.password }}
              </p>
            </div>
          </div>

          <!-- Confirm Password Field -->
          <div>
            <label for="password_confirmation" class="block text-sm font-medium text-gray-700">
              Confirm Password
            </label>
            <div class="mt-1">
              <input
                id="password_confirmation"
                v-model="form.password_confirmation"
                type="password"
                required
                autocomplete="new-password"
                class="appearance-none block w-full px-3 py-2.5 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all"
                placeholder="••••••••"
              />
            </div>
          </div>

          <!-- Submit Button -->
          <div>
            <button
              type="submit"
              :disabled="form.processing"
              class="w-full flex justify-center items-center gap-2 py-3 px-4 border border-transparent rounded-lg shadow-lg shadow-green-500/30 text-sm font-semibold text-white bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 disabled:opacity-50 disabled:cursor-not-allowed transition-all hover:-translate-y-0.5"
            >
              <span v-if="form.processing">Creating account...</span>
              <span v-else>Create Account</span>
              <svg v-if="!form.processing" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
              </svg>
            </button>
          </div>
        </form>

        <!-- Divider -->
        <div class="mt-6">
          <div class="relative">
            <div class="absolute inset-0 flex items-center">
              <div class="w-full border-t border-gray-300"></div>
            </div>
            <div class="relative flex justify-center text-sm">
              <span class="px-2 bg-white text-gray-500">Already have an account?</span>
            </div>
          </div>

          <div class="mt-6">
            <a
              href="/login"
              class="w-full flex justify-center py-3 px-4 border-2 border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all"
            >
              Sign in instead
            </a>
          </div>
        </div>
      </div>

      <!-- Footer Links -->
      <div class="mt-8 text-center text-sm text-gray-600">
        <p>
          By signing up, you agree to our
          <a href="#" class="font-medium text-green-600 hover:text-green-500">Terms of Service</a>
          and
          <a href="#" class="font-medium text-green-600 hover:text-green-500">Privacy Policy</a>
        </p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3';

const form = useForm({
  name: '',
  username: '',
  email: '',
  password: '',
  password_confirmation: '',
});

const validateUsername = () => {
  // Convert to lowercase and remove spaces
  form.username = form.username.toLowerCase().replace(/\s/g, '');
};

const submit = () => {
  form.post('/register', {
    onFinish: () => {
      form.reset('password', 'password_confirmation');
    },
  });
};
</script>
