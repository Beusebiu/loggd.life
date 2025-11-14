<template>
  <AppLayout>
    <div class="max-w-7xl mx-auto">
      <!-- Compact Header -->
      <div class="sticky top-0 z-10 bg-white/80 backdrop-blur-md border-b border-gray-200 px-3 sm:px-4 py-2">
        <div class="flex items-center justify-between">
          <h2 class="text-base sm:text-lg font-bold text-black">
            Profile
            <span v-if="isPreviewMode" class="ml-2 px-2 py-1 bg-blue-100 text-blue-700 text-xs rounded-full font-normal hidden sm:inline">
              Public Preview
            </span>
          </h2>
          <div class="flex gap-1 sm:gap-2">
            <Link
              v-if="isActualOwner && !isPreviewMode"
              :href="`/@${profileUser.username}?preview=public`"
              class="flex items-center gap-1 sm:gap-1.5 px-2 sm:px-3 py-1.5 text-xs font-medium text-blue-700 bg-blue-50 border border-blue-300 rounded-lg hover:bg-blue-100 transition-colors"
            >
              <span>👁️</span>
              <span class="hidden sm:inline">Preview Public</span>
            </Link>
            <Link
              v-if="isPreviewMode"
              :href="`/@${profileUser.username}`"
              class="flex items-center gap-1 sm:gap-1.5 px-2 sm:px-3 py-1.5 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors"
            >
              <span>←</span>
              <span class="hidden sm:inline">My View</span>
            </Link>
          </div>
        </div>
      </div>

      <div class="px-3 sm:px-4 py-3 sm:py-4 space-y-3 sm:space-y-4">
        <!-- Compact Hero -->
        <div class="bg-gradient-to-br from-white to-green-50 rounded-lg p-3 sm:p-4 border border-gray-200">
          <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div class="flex items-center gap-3 sm:gap-4">
              <div class="w-12 h-12 sm:w-16 sm:h-16 rounded-full bg-gradient-to-br from-green-500 to-emerald-600 flex items-center justify-center text-white font-bold text-lg sm:text-2xl shadow-md flex-shrink-0">
                {{ profileUser.name.charAt(0).toUpperCase() }}
              </div>
              <div class="min-w-0 flex-1">
                <h1 class="text-lg sm:text-xl font-bold text-gray-900 truncate">{{ profileUser.name }}</h1>
                <p class="text-xs sm:text-sm text-gray-600">
                  @{{ profileUser.username }}
                  <span v-if="stats.activeStreak > 0" class="ml-2 px-2 py-0.5 bg-orange-100 text-orange-700 font-semibold rounded text-xs">
                    🔥 {{ stats.activeStreak }} day streak
                  </span>
                </p>
                <p v-if="profileUser.bio" class="text-xs text-gray-600 mt-1">{{ profileUser.bio }}</p>
              </div>
            </div>

            <div class="flex gap-2 sm:gap-3 items-center w-full sm:w-auto">
              <div class="text-center px-2 sm:px-3 py-1.5 sm:py-2 bg-white rounded-lg border border-gray-200 flex-1 sm:flex-initial">
                <div class="text-lg sm:text-2xl font-bold text-gray-900">{{ stats.totalActiveDays }}</div>
                <div class="text-[10px] sm:text-xs text-gray-600">Active</div>
              </div>
              <div class="text-center px-2 sm:px-3 py-1.5 sm:py-2 bg-white rounded-lg border border-gray-200 flex-1 sm:flex-initial">
                <div class="text-lg sm:text-2xl font-bold text-green-600">{{ stats.activeGoals }}</div>
                <div class="text-[10px] sm:text-xs text-gray-600">Goals</div>
              </div>
              <div class="text-center px-2 sm:px-3 py-1.5 sm:py-2 bg-white rounded-lg border border-gray-200 flex-1 sm:flex-initial">
                <div class="text-lg sm:text-2xl font-bold text-amber-600">{{ stats.achievements }}</div>
                <div class="text-[10px] sm:text-xs text-gray-600">Awards</div>
              </div>
              <Link
                v-if="isOwnProfile"
                :href="`/@${profileUser.username}/settings`"
                class="px-2 sm:px-3 py-1.5 sm:py-2 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors hidden sm:block"
              >
                Edit
              </Link>
            </div>
          </div>
        </div>

        <!-- Habits with 365-Day Trackers -->
        <div v-if="publicHabits && publicHabits.length > 0" class="bg-white rounded-lg p-3 sm:p-4 border border-gray-200">
          <div class="flex items-center justify-between mb-3">
            <h2 class="text-base font-bold text-gray-900 flex items-center gap-2">
              <span>🔥</span>
              Habits
            </h2>
            <Link
              v-if="isOwnProfile"
              :href="`/@${profileUser.username}/habits`"
              class="text-xs text-green-600 hover:text-green-700 font-medium"
            >
              Manage →
            </Link>
          </div>

          <div class="space-y-4">
            <div
              v-for="habit in habitsWithYearData"
              :key="habit.id"
              class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow"
            >
              <!-- Habit Header -->
              <div class="px-3 sm:px-4 py-2.5 sm:py-3 border-b border-gray-100">
                <div class="flex items-start sm:items-center justify-between gap-2">
                  <div class="flex items-center gap-2 sm:gap-3 min-w-0 flex-1">
                    <span class="text-xl sm:text-2xl flex-shrink-0">{{ habit.emoji }}</span>
                    <div class="min-w-0 flex-1">
                      <h3 class="text-sm font-semibold text-gray-900 truncate">{{ habit.name }}</h3>
                      <div class="flex items-center gap-2 sm:gap-3 mt-1 text-[11px] sm:text-xs text-gray-500 flex-wrap">
                        <span>{{ formatFrequency(habit.frequency) }}</span>
                        <span
                          v-if="habit.allow_multiple_checks"
                          class="px-2 py-0.5 rounded-full font-medium text-xs"
                          :style="{
                            backgroundColor: `${habit.color}20` || '#10b98120',
                            color: habit.color || '#10b981'
                          }"
                          title="Can be checked multiple times per day"
                        >
                          Multiple checks
                        </span>
                        <span class="flex items-center gap-1">
                          <span :style="{ color: habit.color || '#10b981' }">🔥</span>
                          <span class="font-semibold text-gray-900">{{ habit.current_streak }}</span>
                          <span>day streak</span>
                        </span>
                      </div>
                    </div>
                  </div>
                  <div class="text-right flex-shrink-0">
                    <div class="text-base sm:text-lg font-bold" :style="{ color: habit.color || '#10b981' }">
                      {{ habit.completion_rate }}%
                    </div>
                    <div class="text-[10px] sm:text-xs text-gray-500 hidden sm:block">completion</div>
                  </div>
                </div>
              </div>

              <!-- 365-Day GitHub-style Grid -->
              <HabitYearGrid
                :habit="habit"
                @show-tooltip="(event, day) => showTooltip(event, habit, day)"
                @hide-tooltip="hideTooltip"
                @change-year="(year) => changeHabitYear(habit.id, year)"
              />
            </div>
          </div>
        </div>

        <!-- Goals: Active & Timeline -->
        <div v-if="publicGoals && publicGoals.length > 0" class="bg-white rounded-lg p-3 sm:p-4 border border-gray-200">
          <div class="flex items-center justify-between mb-3">
            <h2 class="text-sm sm:text-base font-bold text-gray-900 flex items-center gap-2">
              <span>🎯</span>
              Goals
              <span class="text-[10px] sm:text-xs font-normal text-gray-500">
                ({{ totalActiveGoals }} active<span v-if="totalCompletedGoals > 0">, {{ totalCompletedGoals }} completed</span>)
              </span>
            </h2>
            <div class="flex items-center gap-2">
              <button
                v-if="publicGoals.length > 4"
                @click="goalsExpanded = !goalsExpanded"
                class="text-[10px] sm:text-xs font-medium text-green-600 hover:text-green-700 transition-colors"
              >
                {{ goalsExpanded ? 'Show less' : 'Show all' }}
              </button>
              <Link
                v-if="isOwnProfile"
                :href="`/@${profileUser.username}/journey/goals`"
                class="text-[10px] sm:text-xs text-green-600 hover:text-green-700 font-medium"
              >
                Manage →
              </Link>
            </div>
          </div>

          <div
            class="space-y-5 transition-all duration-300 overflow-hidden"
            :class="goalsExpanded ? '' : 'max-h-[500px]'"
          >
            <!-- Active Goals Section -->
            <div v-if="activeGoals.length > 0">
              <div class="flex items-center gap-2 mb-3">
                <h3 class="text-xs sm:text-sm font-bold text-gray-800">🚀 Active Goals</h3>
              </div>

              <div class="space-y-4">
                <!-- Loop through each time horizon group -->
                <div
                  v-for="(group, horizonKey) in groupedActiveGoals"
                  :key="horizonKey"
                  class="space-y-2"
                >
                  <!-- Group header -->
                  <div class="flex items-center gap-2 mb-2">
                    <span class="text-sm sm:text-base">{{ group.icon }}</span>
                    <h4 class="text-[10px] sm:text-xs font-bold text-gray-600 uppercase tracking-wide">
                      {{ group.label }}
                    </h4>
                    <span class="text-[10px] sm:text-xs text-gray-500">({{ group.goals.length }})</span>
                  </div>

                  <!-- Goals in this group -->
                  <div class="space-y-2 pl-6">
                    <div
                      v-for="goal in group.goals"
                      :key="goal.id"
                      class="p-2 sm:p-3 border border-gray-200 rounded-lg hover:border-green-300 transition-all"
                    >
                      <div class="flex items-center justify-between mb-2">
                        <div class="flex-1">
                          <h5 class="text-xs sm:text-sm font-semibold text-gray-900">{{ goal.title }}</h5>
                          <div class="flex gap-1.5 mt-1">
                            <span class="px-1.5 py-0.5 bg-purple-100 text-purple-700 text-[10px] sm:text-xs rounded">
                              {{ goal.life_area }}
                            </span>
                          </div>
                        </div>
                        <div class="text-base sm:text-xl font-bold" :class="goal.progress_percentage >= 100 ? 'text-green-600' : 'text-gray-900'">
                          {{ goal.progress_percentage }}%
                        </div>
                      </div>

                      <div class="w-full bg-gray-200 rounded-full h-1.5">
                        <div
                          class="h-1.5 rounded-full transition-all"
                          :class="goal.on_track ? 'bg-green-500' : 'bg-amber-500'"
                          :style="{ width: `${Math.min(goal.progress_percentage, 100)}%` }"
                        ></div>
                      </div>

                      <div class="flex justify-between text-[10px] sm:text-xs text-gray-600 mt-1">
                        <span v-if="goal.tracking_type === 'milestone'">
                          {{ goal.milestones_completed }}/{{ goal.milestones_total }} milestones
                        </span>
                        <span v-if="goal.on_track" class="text-green-600 font-medium">On track</span>
                        <span v-else class="text-amber-600 font-medium">Behind</span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Completed Goals Timeline -->
            <div v-if="completedGoals.length > 0" class="border-t border-gray-200 pt-4">
              <div class="flex items-center gap-2 mb-3">
                <h3 class="text-xs sm:text-sm font-bold text-gray-800">✅ Completed Goals</h3>
              </div>

              <div class="space-y-4">
                <!-- Loop through each timeline period -->
                <div
                  v-for="(period, periodKey) in groupedCompletedGoals"
                  :key="periodKey"
                  class="space-y-2"
                >
                  <!-- Period header -->
                  <div class="flex items-center gap-2 mb-2">
                    <span class="text-sm sm:text-base">🏆</span>
                    <h4 class="text-[10px] sm:text-xs font-bold text-green-700 uppercase tracking-wide">
                      {{ period.label }}
                    </h4>
                    <span class="text-[10px] sm:text-xs text-gray-500">({{ period.goals.length }})</span>
                  </div>

                  <!-- Completed goals in this period -->
                  <div class="space-y-2 pl-6">
                    <div
                      v-for="goal in period.goals"
                      :key="goal.id"
                      class="p-2 sm:p-3 border border-green-200 rounded-lg bg-green-50/50"
                    >
                      <div class="flex items-center justify-between mb-2">
                        <div class="flex-1 min-w-0">
                          <h5 class="text-xs sm:text-sm font-semibold text-gray-700 line-through">
                            {{ goal.title }}
                          </h5>
                          <div class="flex gap-1.5 mt-1">
                            <span class="px-1.5 py-0.5 bg-purple-100 text-purple-700 text-[10px] sm:text-xs rounded">
                              {{ goal.life_area }}
                            </span>
                            <span class="text-[10px] sm:text-xs px-1.5 py-0.5 bg-green-100 text-green-700 rounded font-medium">
                              ✓
                            </span>
                          </div>
                        </div>
                        <div class="text-base sm:text-xl font-bold text-green-600 flex-shrink-0">
                          100%
                        </div>
                      </div>

                      <div class="w-full bg-gray-200 rounded-full h-1.5">
                        <div
                          class="h-1.5 rounded-full bg-green-500 transition-all"
                          style="width: 100%"
                        ></div>
                      </div>

                      <div class="flex justify-between text-[10px] sm:text-xs text-gray-600 mt-1">
                        <span v-if="goal.tracking_type === 'milestone'">
                          {{ goal.milestones_total }}/{{ goal.milestones_total }} milestones
                        </span>
                        <span class="text-green-600 font-medium">Completed</span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Vision Section -->
        <div v-if="visionSnippet && visionSnippet.length > 0" class="bg-gradient-to-br from-indigo-50 to-purple-50 rounded-lg p-3 sm:p-4 border border-indigo-200">
          <div class="flex items-center justify-between mb-3">
            <h2 class="text-sm sm:text-base font-bold text-gray-900 flex items-center gap-2">
              <span>🎯</span>
              My Vision
            </h2>
            <button
              @click="visionExpanded = !visionExpanded"
              class="text-[10px] sm:text-xs font-medium text-indigo-600 hover:text-indigo-700 transition-colors"
            >
              {{ visionExpanded ? 'Show less' : 'Show more' }}
            </button>
          </div>
          <div
            class="space-y-3 sm:space-y-4 transition-all duration-300 overflow-hidden"
            :class="visionExpanded ? '' : 'max-h-40'"
          >
            <div
              v-for="(section, index) in visionSnippet"
              :key="section.label"
              class="bg-white/60 rounded-lg p-2 sm:p-3 border border-indigo-100"
            >
              <h3 class="text-[10px] sm:text-xs font-bold text-indigo-700 uppercase tracking-wide mb-2 flex items-center gap-1">
                <span>{{ getSectionIcon(section.label) }}</span>
                {{ section.label }}
              </h3>
              <div class="text-xs sm:text-sm text-gray-700 leading-relaxed">
                <!-- Handle string content (mission, eulogy) -->
                <p v-if="typeof section.content === 'string'" class="whitespace-pre-line text-gray-800">
                  {{ section.content }}
                </p>

                <!-- Handle Bucket List (array with completed status) -->
                <div v-else-if="Array.isArray(section.content) && section.label === 'Bucket List'" class="space-y-1 sm:space-y-1.5">
                  <div
                    v-for="(item, idx) in section.content"
                    :key="idx"
                    class="flex items-start gap-2 py-1"
                  >
                    <span
                      class="text-sm sm:text-base mt-0.5 flex-shrink-0"
                      :class="item.completed ? 'text-green-600' : 'text-gray-400'"
                    >
                      {{ item.completed ? '✅' : '⬜' }}
                    </span>
                    <span
                      class="text-xs sm:text-sm text-gray-800"
                      :class="item.completed ? 'line-through text-gray-500' : ''"
                    >
                      {{ item.text }}
                    </span>
                  </div>
                </div>

                <!-- Handle Odyssey Plan (object with three paths) -->
                <div v-else-if="typeof section.content === 'object' && section.label === 'Odyssey Plan'" class="space-y-2 sm:space-y-3">
                  <div v-if="section.content.current_path" class="border-l-3 border-blue-400 pl-2 sm:pl-3">
                    <div class="text-[10px] sm:text-xs font-semibold text-blue-700 uppercase mb-1">🎯 Current Path</div>
                    <p class="text-xs sm:text-sm text-gray-700 whitespace-pre-line">{{ section.content.current_path }}</p>
                  </div>
                  <div v-if="section.content.radical_path" class="border-l-3 border-purple-400 pl-2 sm:pl-3">
                    <div class="text-[10px] sm:text-xs font-semibold text-purple-700 uppercase mb-1">🚀 Radical Path</div>
                    <p class="text-xs sm:text-sm text-gray-700 whitespace-pre-line">{{ section.content.radical_path }}</p>
                  </div>
                  <div v-if="section.content.alternative_path" class="border-l-3 border-amber-400 pl-2 sm:pl-3">
                    <div class="text-[10px] sm:text-xs font-semibold text-amber-700 uppercase mb-1">🌟 Alternative Path</div>
                    <p class="text-xs sm:text-sm text-gray-700 whitespace-pre-line">{{ section.content.alternative_path }}</p>
                  </div>
                </div>

                <!-- Handle Future Calendar (object with ideal days) -->
                <div v-else-if="typeof section.content === 'object' && section.label === 'Future Calendar'" class="space-y-2 sm:space-y-3">
                  <div v-for="(dayContent, dayKey) in section.content" :key="dayKey" class="bg-indigo-50 rounded p-2 border border-indigo-100">
                    <div class="text-[10px] sm:text-xs font-semibold text-indigo-800 uppercase mb-1">
                      {{ formatCalendarDay(dayKey) }}
                    </div>
                    <p class="text-[10px] sm:text-xs text-gray-700 whitespace-pre-line leading-relaxed">{{ dayContent }}</p>
                  </div>
                </div>

                <!-- Handle Definition of Success (object with categories) -->
                <div v-else-if="typeof section.content === 'object' && section.label === 'Definition of Success'" class="space-y-1.5 sm:space-y-2">
                  <div v-for="(value, key) in section.content" :key="key" class="flex items-start gap-2">
                    <span class="text-[10px] sm:text-xs font-semibold text-indigo-700 uppercase tracking-wide flex-shrink-0 mt-0.5">
                      {{ formatKey(key) }}:
                    </span>
                    <span class="text-xs sm:text-sm text-gray-800">{{ value }}</span>
                  </div>
                </div>

                <!-- Fallback for other array/object content -->
                <div v-else-if="Array.isArray(section.content)" class="space-y-1">
                  <div v-for="(item, idx) in section.content" :key="idx" class="flex items-start gap-2">
                    <span class="text-gray-400">•</span>
                    <span class="text-xs sm:text-sm text-gray-800">{{ typeof item === 'string' ? item : item.text || item.title || JSON.stringify(item) }}</span>
                  </div>
                </div>
                <div v-else-if="typeof section.content === 'object'" class="space-y-1">
                  <div v-for="(value, key) in section.content" :key="key" class="text-xs sm:text-sm">
                    <span class="font-medium text-gray-800">{{ formatKey(key) }}:</span>
                    <span class="text-gray-700 ml-1">{{ value }}</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Visitor CTA -->
        <div v-if="!$page.props.auth.user" class="bg-gradient-to-r from-green-500 to-emerald-600 rounded-lg p-6 text-center text-white">
          <div class="text-3xl mb-2">🚀</div>
          <h2 class="text-lg font-bold mb-1">Start Your Journey on LOGGD</h2>
          <p class="text-green-100 text-sm mb-4">Track habits • Set goals • Build consistency</p>
          <Link
            href="/register"
            class="inline-block px-6 py-2 bg-white text-green-600 font-semibold text-sm rounded-lg hover:bg-gray-100 transition-colors"
          >
            Sign Up Free
          </Link>
        </div>
      </div>
    </div>

    <!-- Custom Tooltip -->
    <Transition name="tooltip">
      <div
        v-if="tooltip.show"
        class="fixed z-50 pointer-events-none"
        :style="{ left: tooltip.x + 'px', top: tooltip.y + 'px' }"
      >
        <div class="bg-gray-900 text-white px-3 py-2 rounded-lg shadow-xl text-xs transform -translate-x-1/2 -translate-y-full -mt-2 max-w-xs">
          <div class="font-semibold mb-1">{{ formatTooltipDate(tooltip.date) }}</div>
          <div class="flex items-center gap-2">
            <span v-if="tooltip.completed" class="text-green-400">
              ✓ Completed
              <span v-if="tooltip.allowMultiple && tooltip.count > 1">
                ({{ tooltip.count }}×)
              </span>
            </span>
            <span v-else-if="tooltip.tracked" class="text-red-400">✗ Missed</span>
            <span v-else class="text-gray-400">Not tracked</span>
          </div>
          <!-- Arrow -->
          <div class="absolute left-1/2 bottom-0 transform -translate-x-1/2 translate-y-full">
            <div class="w-0 h-0 border-l-4 border-r-4 border-t-4 border-transparent border-t-gray-900"></div>
          </div>
        </div>
      </div>
    </Transition>
  </AppLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import axios from 'axios';
import AppLayout from '../Layouts/AppLayout.vue';
import HabitYearGrid from '../Components/habits/HabitYearGrid.vue';

const props = defineProps({
  profileUser: Object,
  stats: Object,
  publicHabits: Array,
  publicGoals: Array,
  activityData: Array,
  visionSnippet: [Array, Object],
  milestones: Array,
  isOwnProfile: Boolean,
  isActualOwner: Boolean,
});

// Check if we're in preview mode
const isPreviewMode = computed(() => {
  return props.isActualOwner && !props.isOwnProfile;
});

// Vision expanded state
const visionExpanded = ref(false);

// Goals expanded state
const goalsExpanded = ref(false);

// Year selection for each habit (defaults to current year)
const selectedYears = ref({});

// Checks data (only for own profile)
const checks = ref({});

// Tooltip state
const tooltip = ref({
  show: false,
  x: 0,
  y: 0,
  date: '',
  completed: false,
  tracked: true,
  count: 0,
  allowMultiple: false,
});

// Fetch all checks data if viewing own profile
onMounted(async () => {
  if (props.isOwnProfile || props.isActualOwner) {
    try {
      const response = await axios.get('/api/habit-checks');
      // Process checks into a map for easy lookup
      response.data.forEach(check => {
        const key = `${check.habit_id}-${check.date}`;
        checks.value[key] = true;
        if (check.count) {
          checks.value[`${key}-count`] = check.count;
        }
      });
    } catch (error) {
      console.error('Error fetching checks:', error);
    }
  }
});

// Separate active and completed goals
const activeGoals = computed(() => {
  return props.publicGoals?.filter(g => g.status === 'active') || [];
});

const completedGoals = computed(() => {
  return props.publicGoals?.filter(g => g.status === 'completed') || [];
});

// Group active goals by time horizon
const groupedActiveGoals = computed(() => {
  if (activeGoals.value.length === 0) return {};

  const groups = {
    '3_year': { label: '3-Year Goals', icon: '🎯', goals: [] },
    'yearly': { label: 'Yearly Goals', icon: '📅', goals: [] },
    'quarterly': { label: 'Quarterly Goals', icon: '⚡', goals: [] },
    'monthly': { label: 'Monthly Goals', icon: '🗓️', goals: [] },
  };

  activeGoals.value.forEach(goal => {
    if (groups[goal.time_horizon]) {
      groups[goal.time_horizon].goals.push(goal);
    }
  });

  // Only return groups that have goals
  return Object.fromEntries(
    Object.entries(groups).filter(([key, group]) => group.goals.length > 0)
  );
});

// Group completed goals by timeline (year/quarter)
const groupedCompletedGoals = computed(() => {
  if (completedGoals.value.length === 0) return {};

  const timeline = {};

  completedGoals.value.forEach(goal => {
    // Use target_date to determine period, fallback to current date
    const date = goal.target_date ? new Date(goal.target_date) : new Date();
    const year = date.getFullYear();
    const quarter = Math.floor(date.getMonth() / 3) + 1;

    // Create period key based on time_horizon
    let periodKey, periodLabel;
    if (goal.time_horizon === 'yearly' || goal.time_horizon === '3_year') {
      periodKey = `${year}`;
      periodLabel = `${year}`;
    } else {
      periodKey = `${year}-Q${quarter}`;
      periodLabel = `Q${quarter} ${year}`;
    }

    if (!timeline[periodKey]) {
      timeline[periodKey] = {
        label: periodLabel,
        sortKey: periodKey,
        goals: []
      };
    }

    timeline[periodKey].goals.push(goal);
  });

  // Sort by period (most recent first)
  return Object.fromEntries(
    Object.entries(timeline).sort((a, b) => b[1].sortKey.localeCompare(a[1].sortKey))
  );
});

// Count total active goals
const totalActiveGoals = computed(() => {
  return activeGoals.value.length;
});

const totalCompletedGoals = computed(() => {
  return completedGoals.value.length;
});

// Format frequency
const formatFrequency = (frequency) => {
  const map = {
    'daily': 'Every day',
    'weekdays': 'Weekdays',
    'weekends': 'Weekends',
    'custom': 'Custom'
  };
  return map[frequency] || frequency;
};

// Get available years for a habit (years from start to current year)
const getAvailableYears = (habit) => {
  if (!habit.start_date) return [new Date().getFullYear()];

  const startYear = new Date(habit.start_date).getFullYear();
  const currentYear = new Date().getFullYear();
  const years = [];

  // Build list from current year down to start year
  for (let year = currentYear; year >= startYear; year--) {
    years.push(year);
  }

  return years;
};

// Check if a habit should be tracked on a given day of week
const shouldTrackDay = (habit, dayOfWeek) => {
  if (habit.frequency === 'daily') return true;
  if (habit.frequency === 'weekdays') return dayOfWeek >= 1 && dayOfWeek <= 5;
  if (habit.frequency === 'weekends') return dayOfWeek === 0 || dayOfWeek === 6;
  if (habit.frequency === 'custom' && habit.tracking_days) {
    const days = ['sun', 'mon', 'tue', 'wed', 'thu', 'fri', 'sat'];
    return habit.tracking_days[days[dayOfWeek]];
  }
  return false;
};

// Build year_data for a habit for a specific year
const buildYearDataForHabit = (habit, year = null) => {
  const yearData = [];
  const today = new Date();
  const habitStart = new Date(habit.start_date);

  if (year) {
    // Specific year: generate full year (Jan 1 - Dec 31)
    const startDate = new Date(year, 0, 1);
    const endDate = new Date(year, 11, 31);

    const currentDate = new Date(startDate);
    while (currentDate <= endDate) {
      const dateString = currentDate.toISOString().split('T')[0];
      const beforeHabitStart = currentDate < habitStart;
      const afterToday = currentDate > today;

      if (beforeHabitStart || afterToday) {
        yearData.push({
          date: dateString,
          tracked: false,
          completed: false,
          count: 0,
          beforeStart: beforeHabitStart,
          future: afterToday,
        });
      } else {
        const dayOfWeek = currentDate.getDay();
        const tracked = shouldTrackDay(habit, dayOfWeek);
        const checkKey = `${habit.id}-${dateString}`;
        const completed = checks.value[checkKey] || false;
        const count = checks.value[`${checkKey}-count`] || (completed ? 1 : 0);

        yearData.push({
          date: dateString,
          tracked,
          completed,
          count,
          beforeStart: false,
          future: false,
        });
      }

      currentDate.setDate(currentDate.getDate() + 1);
    }
  } else {
    // Default: last 365 days
    const endDate = new Date(today);
    const startDate = new Date(today);
    startDate.setDate(startDate.getDate() - 364);

    const currentDate = new Date(startDate);
    while (currentDate <= endDate) {
      const dateString = currentDate.toISOString().split('T')[0];
      const beforeHabitStart = currentDate < habitStart;

      if (beforeHabitStart) {
        yearData.push({
          date: dateString,
          tracked: false,
          completed: false,
          count: 0,
          beforeStart: true,
          future: false,
        });
      } else {
        const dayOfWeek = currentDate.getDay();
        const tracked = shouldTrackDay(habit, dayOfWeek);
        const checkKey = `${habit.id}-${dateString}`;
        const completed = checks.value[checkKey] || false;
        const count = checks.value[`${checkKey}-count`] || (completed ? 1 : 0);

        yearData.push({
          date: dateString,
          tracked,
          completed,
          count,
          beforeStart: false,
          future: false,
        });
      }

      currentDate.setDate(currentDate.getDate() + 1);
    }
  }

  return yearData;
};

// Transform publicHabits to include year selection data
const habitsWithYearData = computed(() => {
  if (!props.publicHabits) return [];

  return props.publicHabits.map(habit => {
    const availableYears = getAvailableYears(habit);
    const selectedYear = selectedYears.value[habit.id] || new Date().getFullYear();

    // If viewing own profile and we have checks data, build year_data dynamically
    let yearData = habit.year_data; // Default to backend-provided data
    if ((props.isOwnProfile || props.isActualOwner) && Object.keys(checks.value).length > 0) {
      yearData = buildYearDataForHabit(habit, selectedYear);
    }

    return {
      ...habit,
      year_data: yearData,
      available_years: availableYears,
      selected_year: selectedYear,
    };
  });
});

// Handle year change for a habit
const changeHabitYear = (habitId, year) => {
  selectedYears.value[habitId] = year;
};

// Generate 365-day grid for a specific habit
// Get today's check count for a habit
const getTodayCheckCount = (habit) => {
  const today = new Date().toISOString().split('T')[0];
  const todayData = habit.year_data?.find(d => d.date === today);
  return todayData?.count || 0;
};

// Show tooltip on hover
const showTooltip = (event, habit, day) => {
  if (!day || !day.date) return;

  const rect = event.target.getBoundingClientRect();

  // Debug logging
  if (habit.allow_multiple_checks && day.count > 1) {
    console.log('Tooltip data:', {
      habit: habit.name,
      date: day.date,
      count: day.count,
      allowMultiple: habit.allow_multiple_checks
    });
  }

  tooltip.value = {
    show: true,
    x: rect.left + rect.width / 2,
    y: rect.top,
    date: day.date,
    completed: day.completed || false,
    tracked: day.tracked !== false,
    count: day.count || 1,
    allowMultiple: habit.allow_multiple_checks || false,
  };
};

// Hide tooltip
const hideTooltip = () => {
  tooltip.value.show = false;
};

// Format tooltip date
const formatTooltipDate = (dateString) => {
  if (!dateString) return '';
  const date = new Date(dateString + 'T00:00:00');
  return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
};

// Format object keys for display (e.g., "bucket_list" -> "Bucket List")
const formatKey = (key) => {
  return key
    .split('_')
    .map(word => word.charAt(0).toUpperCase() + word.slice(1))
    .join(' ');
};

// Get icon for vision section
const getSectionIcon = (label) => {
  const icons = {
    'Mission': '🎯',
    'Eulogy': '🕊️',
    'Bucket List': '📋',
    'Definition of Success': '🏆',
    'Odyssey Plan': '🗺️',
    'Future Calendar': '📅',
  };
  return icons[label] || '✨';
};

// Format calendar day keys (e.g., "ideal_sunday" -> "Ideal Sunday")
const formatCalendarDay = (dayKey) => {
  return dayKey
    .split('_')
    .map(word => word.charAt(0).toUpperCase() + word.slice(1))
    .join(' ');
};
</script>

<style scoped>
/* Tooltip transition */
.tooltip-enter-active,
.tooltip-leave-active {
  transition: opacity 0.15s ease;
}

.tooltip-enter-from,
.tooltip-leave-to {
  opacity: 0;
}
</style>
