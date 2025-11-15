<template>
  <AppLayout>
    <div class="max-w-7xl mx-auto">
      <!-- Page Header -->
      <div class="sticky top-0 z-10 bg-white/80 dark:bg-gray-800/80 backdrop-blur-md border-b border-gray-200 dark:border-gray-700 px-4 py-3">
        <h2 class="text-xl font-bold text-black dark:text-white">Settings</h2>
      </div>

      <div class="px-4 sm:px-6 lg:px-8 py-6">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">

          <!-- Profile Section -->
          <div class="bg-white dark:bg-gray-800 rounded-xl p-5 border border-gray-200 dark:border-gray-700 hover:border-gray-300 dark:hover:border-gray-600 transition-colors">
            <div class="flex items-center gap-2 mb-4">
              <span class="text-xl">👤</span>
              <h3 class="text-base font-bold text-gray-900 dark:text-white">Profile</h3>
            </div>
            <form @submit.prevent="updateProfile" class="space-y-3">
              <div>
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-300 mb-1.5">Full Name</label>
                <input
                  v-model="profileForm.name"
                  type="text"
                  class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-green-500 focus:border-transparent transition-shadow"
                />
              </div>

              <div>
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-300 mb-1.5">Username</label>
                <div class="flex items-center gap-2">
                  <span class="text-gray-400 dark:text-gray-500 text-sm">@</span>
                  <input
                    :value="$page.props.auth.user.username"
                    type="text"
                    disabled
                    class="flex-1 px-3 py-2 text-sm border border-gray-200 dark:border-gray-700 rounded-lg bg-gray-50 dark:bg-gray-900 text-gray-500 dark:text-gray-400 cursor-not-allowed"
                  />
                </div>
              </div>

              <div>
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-300 mb-1.5">Bio</label>
                <textarea
                  v-model="profileForm.bio"
                  rows="2"
                  maxlength="500"
                  class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-green-500 focus:border-transparent resize-none transition-shadow"
                  placeholder="Tell us about yourself..."
                ></textarea>
                <p class="mt-1 text-xs text-gray-400 dark:text-gray-500">{{ profileForm.bio?.length || 0 }}/500</p>
              </div>

              <div class="pt-1">
                <button
                  type="submit"
                  :disabled="savingProfile"
                  class="w-full px-4 py-2 text-sm bg-black dark:bg-white text-white dark:text-black font-medium rounded-lg hover:bg-gray-800 dark:hover:bg-gray-100 disabled:opacity-50 disabled:cursor-not-allowed transition-all"
                >
                  <span v-if="savingProfile" class="flex items-center justify-center gap-2">
                    <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                      <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Saving...
                  </span>
                  <span v-else>Save Changes</span>
                </button>
              </div>
            </form>
          </div>

          <!-- Email Section -->
          <div class="bg-white dark:bg-gray-800 rounded-xl p-5 border border-gray-200 dark:border-gray-700 hover:border-gray-300 dark:hover:border-gray-600 transition-colors">
            <div class="flex items-center gap-2 mb-4">
              <span class="text-xl">📧</span>
              <h3 class="text-base font-bold text-gray-900 dark:text-white">Email</h3>
            </div>
            <form @submit.prevent="updateEmail" class="space-y-3">
              <div>
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-300 mb-1.5">Email Address</label>
                <input
                  v-model="emailForm.email"
                  type="email"
                  class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-green-500 focus:border-transparent transition-shadow"
                />
                <Transition name="fade">
                  <p v-if="emailError" class="mt-1.5 text-xs text-red-600 dark:text-red-400 flex items-center gap-1">
                    <span>⚠️</span>
                    {{ emailError }}
                  </p>
                </Transition>
              </div>

              <div class="pt-1">
                <button
                  type="submit"
                  :disabled="savingEmail"
                  class="w-full px-4 py-2 text-sm bg-black dark:bg-white text-white dark:text-black font-medium rounded-lg hover:bg-gray-800 dark:hover:bg-gray-100 disabled:opacity-50 disabled:cursor-not-allowed transition-all"
                >
                  <span v-if="savingEmail" class="flex items-center justify-center gap-2">
                    <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                      <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Updating...
                  </span>
                  <span v-else>Update Email</span>
                </button>
              </div>
            </form>
          </div>

          <!-- Appearance Section -->
          <div class="bg-white dark:bg-gray-800 rounded-xl p-5 border border-gray-200 dark:border-gray-700 hover:border-gray-300 dark:hover:border-gray-600 transition-colors">
            <div class="flex items-center gap-2 mb-4">
              <span class="text-xl">🎨</span>
              <h3 class="text-base font-bold text-gray-900 dark:text-white">Appearance</h3>
            </div>
            <div class="space-y-3">
              <div class="flex items-center justify-between">
                <div>
                  <label class="block text-sm font-medium text-gray-900 dark:text-white">Dark Mode</label>
                  <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Switch between light and dark theme</p>
                </div>
                <button
                  @click="toggleDarkMode"
                  :class="darkModeEnabled ? 'bg-green-600' : 'bg-gray-200 dark:bg-gray-700'"
                  class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800"
                >
                  <span
                    :class="darkModeEnabled ? 'translate-x-6' : 'translate-x-1'"
                    class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform"
                  />
                </button>
              </div>
            </div>
          </div>

          <!-- Password Section - Full Width -->
          <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-xl p-5 border border-gray-200 dark:border-gray-700 hover:border-gray-300 dark:hover:border-gray-600 transition-colors">
            <div class="flex items-center justify-between mb-4">
              <div class="flex items-center gap-2">
                <span class="text-xl">🔑</span>
                <h3 class="text-base font-bold text-gray-900 dark:text-white">Change Password</h3>
              </div>
              <button
                v-if="!showPasswordForm"
                @click="showPasswordForm = true"
                class="text-xs text-green-600 dark:text-green-400 hover:text-green-700 dark:hover:text-green-300 font-medium"
              >
                Show Form
              </button>
            </div>

            <Transition name="expand">
              <form v-if="showPasswordForm" @submit.prevent="updatePassword" class="grid grid-cols-1 md:grid-cols-3 gap-3">
                <div>
                  <label class="block text-xs font-medium text-gray-600 dark:text-gray-300 mb-1.5">Current Password</label>
                  <input
                    v-model="passwordForm.current_password"
                    type="password"
                    class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-green-500 focus:border-transparent transition-shadow"
                  />
                </div>

                <div>
                  <label class="block text-xs font-medium text-gray-600 dark:text-gray-300 mb-1.5">New Password</label>
                  <input
                    v-model="passwordForm.password"
                    type="password"
                    class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-green-500 focus:border-transparent transition-shadow"
                  />
                </div>

                <div>
                  <label class="block text-xs font-medium text-gray-600 dark:text-gray-300 mb-1.5">Confirm Password</label>
                  <input
                    v-model="passwordForm.password_confirmation"
                    type="password"
                    class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-green-500 focus:border-transparent transition-shadow"
                  />
                </div>

                <div class="md:col-span-3">
                  <Transition name="fade">
                    <p v-if="passwordError" class="mb-2 text-xs text-red-600 dark:text-red-400 flex items-center gap-1">
                      <span>⚠️</span>
                      {{ passwordError }}
                    </p>
                  </Transition>
                  <div class="flex gap-2">
                    <button
                      type="submit"
                      :disabled="savingPassword"
                      class="px-4 py-2 text-sm bg-black dark:bg-white text-white dark:text-black font-medium rounded-lg hover:bg-gray-800 dark:hover:bg-gray-100 disabled:opacity-50 disabled:cursor-not-allowed transition-all"
                    >
                      <span v-if="savingPassword" class="flex items-center gap-2">
                        <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Updating...
                      </span>
                      <span v-else>Update Password</span>
                    </button>
                    <button
                      type="button"
                      @click="cancelPasswordChange"
                      class="px-4 py-2 text-sm border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 font-medium rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-all"
                    >
                      Cancel
                    </button>
                  </div>
                </div>
              </form>
            </Transition>

            <div v-if="!showPasswordForm" class="text-xs text-gray-500 dark:text-gray-400">
              Click "Show Form" to change your password
            </div>
          </div>

          <!-- Account Info -->
          <div class="lg:col-span-2 bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-800 dark:to-gray-700 rounded-xl p-5 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center gap-2 mb-3">
              <span class="text-xl">ℹ️</span>
              <h3 class="text-base font-bold text-gray-900 dark:text-white">Account Information</h3>
            </div>
            <div class="flex flex-wrap gap-x-8 gap-y-2 text-xs">
              <div>
                <span class="text-gray-500 dark:text-gray-400">Member since:</span>
                <span class="ml-1 font-medium text-gray-900 dark:text-white">{{ formatDate($page.props.auth.user.created_at) }}</span>
              </div>
              <div>
                <span class="text-gray-500 dark:text-gray-400">Account ID:</span>
                <span class="ml-1 font-mono font-medium text-gray-900 dark:text-white">{{ $page.props.auth.user.id }}</span>
              </div>
            </div>
          </div>

        </div>
      </div>
    </div>

    <!-- Toast Notification -->
    <Transition name="toast">
      <div
        v-if="toast.show"
        class="fixed bottom-4 right-4 z-50 bg-gray-900 text-white px-4 py-3 rounded-lg shadow-2xl flex items-center gap-3 max-w-sm"
      >
        <span class="text-xl">{{ toast.type === 'success' ? '✓' : '⚠️' }}</span>
        <span class="text-sm font-medium">{{ toast.message }}</span>
      </div>
    </Transition>
  </AppLayout>
</template>

<script setup>
import AppLayout from '../Layouts/AppLayout.vue';
import { ref, reactive, onMounted, computed } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import axios from 'axios';

const page = usePage();

const profileForm = reactive({
  name: '',
  bio: '',
});

const emailForm = reactive({
  email: '',
});

const passwordForm = reactive({
  current_password: '',
  password: '',
  password_confirmation: '',
});

const savingProfile = ref(false);
const savingEmail = ref(false);
const savingPassword = ref(false);
const showPasswordForm = ref(false);
const emailError = ref('');
const passwordError = ref('');

// Use computed to always reflect the current dark mode state from Inertia props
const darkModeEnabled = computed(() => page.props.auth.user?.dark_mode || false);

const toast = reactive({
  show: false,
  type: 'success',
  message: '',
});

const showToast = (message, type = 'success') => {
  toast.message = message;
  toast.type = type;
  toast.show = true;

  setTimeout(() => {
    toast.show = false;
  }, 3000);
};

const loadUserData = async () => {
  try {
    const response = await axios.get('/api/user');
    const user = response.data;

    profileForm.name = user.name;
    profileForm.bio = user.bio || '';
    emailForm.email = user.email;
    // darkModeEnabled is now a computed property from Inertia props
  } catch (error) {
    console.error('Error loading user data:', error);
  }
};

const updateProfile = async () => {
  savingProfile.value = true;
  try {
    await axios.patch('/api/settings/profile', profileForm);
    showToast('Profile updated successfully!');
  } catch (error) {
    console.error('Error updating profile:', error);
    showToast('Failed to update profile', 'error');
  } finally {
    savingProfile.value = false;
  }
};

const updateEmail = async () => {
  savingEmail.value = true;
  emailError.value = '';
  try {
    await axios.patch('/api/settings/email', emailForm);
    showToast('Email updated successfully!');
  } catch (error) {
    if (error.response?.data?.errors?.email) {
      emailError.value = error.response.data.errors.email[0];
    } else {
      showToast('Failed to update email', 'error');
    }
  } finally {
    savingEmail.value = false;
  }
};

const updatePassword = async () => {
  savingPassword.value = true;
  passwordError.value = '';
  try {
    await axios.patch('/api/settings/password', passwordForm);
    showToast('Password updated successfully!');
    passwordForm.current_password = '';
    passwordForm.password = '';
    passwordForm.password_confirmation = '';
    showPasswordForm.value = false;
  } catch (error) {
    if (error.response?.data?.message) {
      passwordError.value = error.response.data.message;
    } else {
      showToast('Failed to update password', 'error');
    }
  } finally {
    savingPassword.value = false;
  }
};

const cancelPasswordChange = () => {
  passwordForm.current_password = '';
  passwordForm.password = '';
  passwordForm.password_confirmation = '';
  passwordError.value = '';
  showPasswordForm.value = false;
};

const toggleDarkMode = async () => {
  const newValue = !darkModeEnabled.value;

  try {
    await axios.patch('/api/settings/dark-mode', { dark_mode: newValue });
    showToast(newValue ? 'Dark mode enabled' : 'Dark mode disabled');

    // Reload page to apply dark mode changes using Inertia
    setTimeout(() => {
      router.visit(window.location.pathname, {
        preserveScroll: true,
        preserveState: false,
      });
    }, 300);
  } catch (error) {
    console.error('Error toggling dark mode:', error);
    showToast('Failed to toggle dark mode', 'error');
  }
};

const formatDate = (dateString) => {
  const date = new Date(dateString);
  return date.toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  });
};

onMounted(() => {
  loadUserData();
});
</script>

<style scoped>
/* Fade transition for errors and toasts */
.fade-enter-active,
.fade-leave-active {
  transition: all 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
  transform: translateY(-4px);
}

/* Toast transition */
.toast-enter-active,
.toast-leave-active {
  transition: all 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55);
}

.toast-enter-from,
.toast-leave-to {
  opacity: 0;
  transform: translateX(100px) scale(0.8);
}

/* Expand transition for password form */
.expand-enter-active,
.expand-leave-active {
  transition: all 0.3s ease;
  overflow: hidden;
}

.expand-enter-from,
.expand-leave-to {
  opacity: 0;
  max-height: 0;
}

.expand-enter-to,
.expand-leave-from {
  opacity: 1;
  max-height: 500px;
}
</style>
