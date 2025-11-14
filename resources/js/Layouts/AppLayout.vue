<template>
  <div class="min-h-screen bg-white">
    <!-- Mobile Header -->
    <header class="lg:hidden sticky top-0 z-40 bg-white border-b border-gray-200">
      <div class="flex items-center justify-between px-4 py-3">
        <h1 class="text-xl font-bold text-black">LOGGD</h1>
        <button
          @click="mobileMenuOpen = true"
          class="p-2 text-gray-600 hover:bg-gray-100 rounded-lg transition-colors"
          aria-label="Open menu"
        >
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
          </svg>
        </button>
      </div>
    </header>

    <div class="flex max-w-screen-xl mx-auto">
      <!-- Desktop Left Sidebar - Fixed Navigation (Twitter-style) -->
      <aside class="hidden lg:block w-64 flex-shrink-0 border-r border-gray-200 h-screen sticky top-0">
        <div class="flex flex-col h-full px-3 py-4">
          <!-- Logo -->
          <div class="px-3 mb-6">
            <h1 class="text-2xl font-bold text-black">LOGGD</h1>
          </div>

          <!-- Navigation (Logged In) -->
          <nav v-if="user" class="flex-1 space-y-1">
            <Link
              :href="`/@${user.username}`"
              class="flex items-center gap-4 px-4 py-3 rounded-full transition-colors group"
              :class="isActive(`/@${user.username}`)
                ? 'bg-gray-100 font-semibold'
                : 'hover:bg-gray-100'"
            >
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
              </svg>
              <span class="text-lg">Home</span>
            </Link>

            <Link
              :href="`/@${user.username}/habits`"
              class="flex items-center gap-4 px-4 py-3 rounded-full transition-colors group"
              :class="isActive('habits')
                ? 'bg-gray-100 font-semibold'
                : 'hover:bg-gray-100'"
            >
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
              </svg>
              <span class="text-lg">Habits</span>
            </Link>

            <Link
              :href="`/@${user.username}/journey`"
              class="flex items-center gap-4 px-4 py-3 rounded-full transition-colors group"
              :class="isActive('journey')
                ? 'bg-gray-100 font-semibold'
                : 'hover:bg-gray-100'"
            >
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
              </svg>
              <span class="text-lg">Journey</span>
            </Link>

            <Link
              :href="`/@${user.username}/leaderboard`"
              class="flex items-center gap-4 px-4 py-3 rounded-full transition-colors group"
              :class="isActive('leaderboard')
                ? 'bg-gray-100 font-semibold'
                : 'hover:bg-gray-100'"
            >
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
              </svg>
              <span class="text-lg">Leaderboard</span>
            </Link>
          </nav>

          <!-- Logged Out Content -->
          <div v-else class="flex-1 flex flex-col px-3">
            <div class="mb-6">
              <p class="text-sm text-gray-600 leading-relaxed">
                Track your habits, set goals, and build the life you want.
              </p>
            </div>

            <div class="space-y-3 mb-6">
              <Link
                href="/register"
                class="block w-full px-4 py-3 text-center font-semibold text-white bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 rounded-full shadow-md hover:shadow-lg transition-all"
              >
                Sign Up Free
              </Link>
              <Link
                href="/login"
                class="block w-full px-4 py-3 text-center font-medium text-gray-700 border-2 border-gray-300 hover:border-green-500 hover:text-green-700 rounded-full transition-all"
              >
                Login
              </Link>
            </div>

            <div class="flex-1"></div>

            <div class="space-y-2 text-sm text-gray-600 mb-6">
              <div class="flex items-center gap-2">
                <span>✓</span>
                <span>365-day habit tracking</span>
              </div>
              <div class="flex items-center gap-2">
                <span>✓</span>
                <span>Goal management</span>
              </div>
              <div class="flex items-center gap-2">
                <span>✓</span>
                <span>Daily & weekly check-ins</span>
              </div>
              <div class="flex items-center gap-2">
                <span>✓</span>
                <span>Shareable profiles</span>
              </div>
            </div>
          </div>

          <!-- User Menu at Bottom -->
          <div v-if="user" class="mt-auto">
            <button
              @click="toggleUserMenu"
              class="w-full flex items-center gap-3 px-3 py-2 rounded-full hover:bg-gray-100 transition-colors"
            >
              <div class="w-10 h-10 rounded-full bg-gradient-to-br from-green-400 to-emerald-600 flex items-center justify-center text-white font-semibold">
                {{ user.name.charAt(0).toUpperCase() }}
              </div>
              <div class="flex-1 text-left">
                <div class="text-sm font-semibold text-gray-900">{{ user.name }}</div>
                <div class="text-xs text-gray-500">@{{ user.username }}</div>
              </div>
              <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/>
              </svg>
            </button>
          </div>
        </div>
      </aside>

      <!-- Mobile Sidebar Drawer -->
      <Transition name="drawer">
        <div
          v-if="mobileMenuOpen"
          class="fixed inset-0 z-50 lg:hidden"
        >
          <!-- Overlay -->
          <div
            class="absolute inset-0 bg-gray-900/20 backdrop-blur-sm"
            @click="mobileMenuOpen = false"
          ></div>

          <!-- Drawer -->
          <div class="absolute inset-y-0 left-0 w-72 bg-white shadow-xl">
            <div class="flex flex-col h-full">
              <!-- Header -->
              <div class="flex items-center justify-between px-4 py-4 border-b border-gray-200">
                <h1 class="text-2xl font-bold text-black">LOGGD</h1>
                <button
                  @click="mobileMenuOpen = false"
                  class="p-2 text-gray-600 hover:bg-gray-100 rounded-lg transition-colors"
                  aria-label="Close menu"
                >
                  <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                  </svg>
                </button>
              </div>

              <!-- Navigation (Logged In) -->
              <nav v-if="user" class="flex-1 px-3 py-4 space-y-1">
                <Link
                  :href="`/@${user.username}`"
                  @click="mobileMenuOpen = false"
                  class="flex items-center gap-4 px-4 py-3 rounded-full transition-colors"
                  :class="isActive(`/@${user.username}`)
                    ? 'bg-gray-100 font-semibold'
                    : 'hover:bg-gray-100'"
                >
                  <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                  </svg>
                  <span class="text-lg">Home</span>
                </Link>

                <Link
                  :href="`/@${user.username}/habits`"
                  @click="mobileMenuOpen = false"
                  class="flex items-center gap-4 px-4 py-3 rounded-full transition-colors"
                  :class="isActive('habits')
                    ? 'bg-gray-100 font-semibold'
                    : 'hover:bg-gray-100'"
                >
                  <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                  </svg>
                  <span class="text-lg">Habits</span>
                </Link>

                <Link
                  :href="`/@${user.username}/journey`"
                  @click="mobileMenuOpen = false"
                  class="flex items-center gap-4 px-4 py-3 rounded-full transition-colors"
                  :class="isActive('journey')
                    ? 'bg-gray-100 font-semibold'
                    : 'hover:bg-gray-100'"
                >
                  <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                  </svg>
                  <span class="text-lg">Journey</span>
                </Link>

                <Link
                  :href="`/@${user.username}/leaderboard`"
                  @click="mobileMenuOpen = false"
                  class="flex items-center gap-4 px-4 py-3 rounded-full transition-colors"
                  :class="isActive('leaderboard')
                    ? 'bg-gray-100 font-semibold'
                    : 'hover:bg-gray-100'"
                >
                  <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                  </svg>
                  <span class="text-lg">Leaderboard</span>
                </Link>
              </nav>

              <!-- Logged Out Content -->
              <div v-else class="flex-1 flex flex-col px-3 py-4">
                <div class="mb-6 px-3">
                  <p class="text-sm text-gray-600 leading-relaxed">
                    Track your habits, set goals, and build the life you want.
                  </p>
                </div>

                <div class="space-y-3 mb-6 px-3">
                  <Link
                    href="/register"
                    @click="mobileMenuOpen = false"
                    class="block w-full px-4 py-3 text-center font-semibold text-white bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 rounded-full shadow-md hover:shadow-lg transition-all"
                  >
                    Sign Up Free
                  </Link>
                  <Link
                    href="/login"
                    @click="mobileMenuOpen = false"
                    class="block w-full px-4 py-3 text-center font-medium text-gray-700 border-2 border-gray-300 hover:border-green-500 hover:text-green-700 rounded-full transition-all"
                  >
                    Login
                  </Link>
                </div>

                <div class="space-y-2 text-sm text-gray-600 mb-6 px-3">
                  <div class="flex items-center gap-2">
                    <span>✓</span>
                    <span>365-day habit tracking</span>
                  </div>
                  <div class="flex items-center gap-2">
                    <span>✓</span>
                    <span>Goal management</span>
                  </div>
                  <div class="flex items-center gap-2">
                    <span>✓</span>
                    <span>Daily & weekly check-ins</span>
                  </div>
                  <div class="flex items-center gap-2">
                    <span>✓</span>
                    <span>Shareable profiles</span>
                  </div>
                </div>
              </div>

              <!-- User Menu at Bottom -->
              <div v-if="user" class="border-t border-gray-200 p-3">
                <Link
                  :href="`/@${user.username}/settings`"
                  @click="mobileMenuOpen = false"
                  class="flex items-center gap-3 px-3 py-2 hover:bg-gray-100 rounded-lg transition-colors mb-2"
                >
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                  </svg>
                  <span class="text-sm">Settings</span>
                </Link>
                <button
                  @click="logout"
                  class="w-full flex items-center gap-3 px-3 py-2 hover:bg-gray-100 rounded-lg transition-colors text-sm text-red-600"
                >
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                  </svg>
                  <span>Logout</span>
                </button>
              </div>
            </div>
          </div>
        </div>
      </Transition>

      <!-- Main Content Area -->
      <main class="flex-1 min-h-screen lg:border-r border-gray-200">
        <slot />
      </main>

      <!-- Right Sidebar (Optional - for widgets/stats) -->
      <aside v-if="$slots.sidebar" class="w-80 flex-shrink-0 px-6 py-4 hidden xl:block">
        <div class="sticky top-4">
          <slot name="sidebar" />
        </div>
      </aside>
    </div>

    <!-- User Menu Dropdown (outside sidebar for proper z-index) -->
    <Transition name="menu">
      <div
        v-if="showUserMenu && user"
        class="fixed bottom-4 left-4 w-56 bg-white rounded-xl shadow-2xl border border-gray-200 py-2 z-50"
      >
        <Link
          :href="`/@${user.username}/settings`"
          @click="showUserMenu = false"
          class="flex items-center gap-3 px-4 py-2.5 hover:bg-gray-100 transition-colors text-sm"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
          </svg>
          <span>Settings</span>
        </Link>
        <hr class="my-2 border-gray-200" />
        <button
          @click="logout"
          class="w-full flex items-center gap-3 px-4 py-2.5 hover:bg-gray-100 transition-colors text-sm text-red-600"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
          </svg>
          <span>Logout</span>
        </button>
      </div>
    </Transition>

    <!-- Click outside to close user menu -->
    <div
      v-if="showUserMenu"
      class="fixed inset-0 z-40"
      @click="showUserMenu = false"
    ></div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';

const page = usePage();
const user = computed(() => page.props.auth?.user);

const showUserMenu = ref(false);
const mobileMenuOpen = ref(false);

const toggleUserMenu = () => {
  showUserMenu.value = !showUserMenu.value;
};

const isActive = (path) => {
  const currentPath = window.location.pathname;
  if (path.startsWith('/@')) {
    // Exact match for home
    return currentPath === path;
  }
  return currentPath.includes(path);
};

const logout = () => {
  showUserMenu.value = false;
  mobileMenuOpen.value = false;
  router.post('/logout');
};
</script>

<style scoped>
/* Menu transition */
.menu-enter-active,
.menu-leave-active {
  transition: all 0.2s ease;
}

.menu-enter-from {
  opacity: 0;
  transform: translateY(10px);
}

.menu-leave-to {
  opacity: 0;
  transform: translateY(5px);
}

/* Drawer transition */
.drawer-enter-active,
.drawer-leave-active {
  transition: all 0.3s ease;
}

.drawer-enter-from .absolute.inset-0,
.drawer-leave-to .absolute.inset-0 {
  opacity: 0;
}

.drawer-enter-from .absolute.inset-y-0,
.drawer-leave-to .absolute.inset-y-0 {
  transform: translateX(-100%);
}

.drawer-enter-active .absolute.inset-y-0,
.drawer-leave-active .absolute.inset-y-0 {
  transition: transform 0.3s ease;
}
</style>
