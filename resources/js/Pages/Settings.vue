<template>
  <div class="min-h-screen bg-gradient-to-b from-white to-gray-50">
    <AppNav current-page="settings" />
    <!-- Main Content -->
    <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Header -->
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-black mb-2">Settings</h1>
        <p class="text-gray-600">Manage your account and preferences</p>
      </div>

      <div class="space-y-6">
        <!-- Profile Settings -->
        <div class="bg-white rounded-2xl p-8 border border-gray-200 shadow-lg">
          <h2 class="text-xl font-bold text-black mb-6">Profile Information</h2>
          <form @submit.prevent="updateProfile" class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
              <input
                v-model="profileForm.name"
                type="text"
                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
              />
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Username</label>
              <div class="flex items-center gap-2">
                <span class="text-gray-500">@</span>
                <input
                  :value="$page.props.auth.user.username"
                  type="text"
                  disabled
                  class="flex-1 px-4 py-2.5 border border-gray-300 rounded-lg bg-gray-50 text-gray-500 cursor-not-allowed"
                />
              </div>
              <p class="mt-1 text-xs text-gray-500">Username cannot be changed</p>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Bio</label>
              <textarea
                v-model="profileForm.bio"
                rows="3"
                maxlength="500"
                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent resize-none"
                placeholder="Tell us about yourself..."
              ></textarea>
              <p class="mt-1 text-xs text-gray-500">{{ profileForm.bio?.length || 0 }}/500 characters</p>
            </div>

            <div>
              <button
                type="submit"
                :disabled="savingProfile"
                class="px-6 py-3 bg-gradient-to-r from-green-500 to-emerald-600 text-white font-semibold rounded-lg hover:from-green-600 hover:to-emerald-700 disabled:opacity-50 transition-all shadow-lg shadow-green-500/30"
              >
                {{ savingProfile ? 'Saving...' : 'Save Profile' }}
              </button>
            </div>
          </form>
        </div>

        <!-- Email Settings -->
        <div class="bg-white rounded-2xl p-8 border border-gray-200 shadow-lg">
          <h2 class="text-xl font-bold text-black mb-6">Email Address</h2>
          <form @submit.prevent="updateEmail" class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
              <input
                v-model="emailForm.email"
                type="email"
                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
              />
              <p v-if="emailError" class="mt-2 text-sm text-red-600">{{ emailError }}</p>
            </div>

            <div>
              <button
                type="submit"
                :disabled="savingEmail"
                class="px-6 py-3 bg-gradient-to-r from-green-500 to-emerald-600 text-white font-semibold rounded-lg hover:from-green-600 hover:to-emerald-700 disabled:opacity-50 transition-all shadow-lg shadow-green-500/30"
              >
                {{ savingEmail ? 'Updating...' : 'Update Email' }}
              </button>
            </div>
          </form>
        </div>

        <!-- Privacy Settings -->
        <div class="bg-white rounded-2xl p-8 border border-gray-200 shadow-lg">
          <h2 class="text-xl font-bold text-black mb-6">Privacy Settings</h2>
          <div class="space-y-4">
            <div class="flex items-start gap-4">
              <input
                v-model="privacyForm.profile_public"
                @change="updatePrivacy"
                type="checkbox"
                id="profile_public"
                class="mt-1 w-5 h-5 text-green-600 border-gray-300 rounded focus:ring-green-500"
              />
              <div class="flex-1">
                <label for="profile_public" class="text-sm font-medium text-gray-900 cursor-pointer">
                  Public Profile
                </label>
                <p class="text-sm text-gray-500 mt-1">
                  Allow others to view your profile at @{{ $page.props.auth.user.username }}. Your contribution graph and public entries will be visible.
                </p>
              </div>
            </div>
          </div>
        </div>

        <!-- Password Settings -->
        <div class="bg-white rounded-2xl p-8 border border-gray-200 shadow-lg">
          <h2 class="text-xl font-bold text-black mb-6">Change Password</h2>
          <form @submit.prevent="updatePassword" class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Current Password</label>
              <input
                v-model="passwordForm.current_password"
                type="password"
                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
              />
              <p v-if="passwordError" class="mt-2 text-sm text-red-600">{{ passwordError }}</p>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">New Password</label>
              <input
                v-model="passwordForm.password"
                type="password"
                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
              />
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Confirm New Password</label>
              <input
                v-model="passwordForm.password_confirmation"
                type="password"
                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
              />
            </div>

            <div>
              <button
                type="submit"
                :disabled="savingPassword"
                class="px-6 py-3 bg-gradient-to-r from-green-500 to-emerald-600 text-white font-semibold rounded-lg hover:from-green-600 hover:to-emerald-700 disabled:opacity-50 transition-all shadow-lg shadow-green-500/30"
              >
                {{ savingPassword ? 'Updating...' : 'Update Password' }}
              </button>
            </div>
          </form>
        </div>

        <!-- Account Info -->
        <div class="bg-gray-50 rounded-2xl p-8 border border-gray-200">
          <h2 class="text-xl font-bold text-black mb-4">Account Information</h2>
          <div class="space-y-2 text-sm">
            <p class="text-gray-600">
              <span class="font-medium">Member since:</span> {{ formatDate($page.props.auth.user.created_at) }}
            </p>
            <p class="text-gray-600">
              <span class="font-medium">Account ID:</span> {{ $page.props.auth.user.id }}
            </p>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import AppNav from '../Components/AppNav.vue';
import { ref, reactive, onMounted } from 'vue';
import { router } from '@inertiajs/vue3';

const profileForm = reactive({
  name: '',
  bio: '',
});

const emailForm = reactive({
  email: '',
});

const privacyForm = reactive({
  profile_public: false,
});

const passwordForm = reactive({
  current_password: '',
  password: '',
  password_confirmation: '',
});

const savingProfile = ref(false);
const savingEmail = ref(false);
const savingPassword = ref(false);
const emailError = ref('');
const passwordError = ref('');

const loadUserData = async () => {
  try {
    const response = await window.axios.get('/api/user');
    const user = response.data;

    profileForm.name = user.name;
    profileForm.bio = user.bio || '';
    emailForm.email = user.email;
    privacyForm.profile_public = user.profile_public;
  } catch (error) {
    console.error('Error loading user data:', error);
  }
};

const updateProfile = async () => {
  savingProfile.value = true;
  try {
    await window.axios.patch('/api/settings/profile', profileForm);
    alert('Profile updated successfully!');
  } catch (error) {
    console.error('Error updating profile:', error);
    alert('Failed to update profile');
  } finally {
    savingProfile.value = false;
  }
};

const updateEmail = async () => {
  savingEmail.value = true;
  emailError.value = '';
  try {
    await window.axios.patch('/api/settings/email', emailForm);
    alert('Email updated successfully!');
  } catch (error) {
    if (error.response?.data?.errors?.email) {
      emailError.value = error.response.data.errors.email[0];
    } else {
      alert('Failed to update email');
    }
  } finally {
    savingEmail.value = false;
  }
};

const updatePrivacy = async () => {
  try {
    await window.axios.patch('/api/settings/privacy', privacyForm);
  } catch (error) {
    console.error('Error updating privacy:', error);
    alert('Failed to update privacy settings');
  }
};

const updatePassword = async () => {
  savingPassword.value = true;
  passwordError.value = '';
  try {
    await window.axios.patch('/api/settings/password', passwordForm);
    alert('Password updated successfully!');
    passwordForm.current_password = '';
    passwordForm.password = '';
    passwordForm.password_confirmation = '';
  } catch (error) {
    if (error.response?.data?.message) {
      passwordError.value = error.response.data.message;
    } else {
      alert('Failed to update password');
    }
  } finally {
    savingPassword.value = false;
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
