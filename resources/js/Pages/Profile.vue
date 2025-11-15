<template>
  <AppLayout>
    <div class="max-w-7xl mx-auto">
      <div class="px-3 sm:px-4 py-3 sm:py-4 space-y-3 sm:space-y-4">
        <!-- Unified Profile Header with Activity Grid -->
        <ProfileHeader
          v-if="levelInfo"
          :user-name="profileUser.name"
          :username="profileUser.username"
          :bio="profileUser.bio"
          :level-info="levelInfo"
          :current-streak="profileUser.current_streak || 0"
          :is-own-profile="isOwnProfile"
          :activity-grid-data="activityGridData || []"
        />

        <!-- Habits with 365-Day Trackers -->
        <div v-if="publicHabits && publicHabits.length > 0" class="bg-white dark:bg-gray-800 rounded-lg p-3 sm:p-4 border border-gray-200 dark:border-gray-700 transition-colors">
          <div class="flex items-center justify-between mb-3">
            <h2 class="text-base font-bold text-gray-900 dark:text-white flex items-center gap-2">
              <span>🔥</span>
              Habits
            </h2>
            <Link
              v-if="isOwnProfile"
              :href="`/@${profileUser.username}/habits`"
              class="text-xs text-green-600 dark:text-green-400 hover:text-green-700 dark:hover:text-green-300 active:text-green-800 dark:active:text-green-500 font-medium"
            >
              Manage →
            </Link>
          </div>

          <div class="space-y-4">
            <div
              v-for="habit in habitsWithYearData"
              :key="habit.id"
              class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden hover:shadow-md active:shadow-lg transition-all"
            >
              <!-- Habit Header -->
              <div class="px-3 sm:px-4 py-2.5 sm:py-3 border-b border-gray-100 dark:border-gray-700">
                <div class="flex items-start sm:items-center justify-between gap-2">
                  <div class="flex items-center gap-2 sm:gap-3 min-w-0 flex-1">
                    <span class="text-xl sm:text-2xl flex-shrink-0">{{ habit.emoji }}</span>
                    <div class="min-w-0 flex-1">
                      <h3 class="text-sm font-semibold text-gray-900 dark:text-white truncate">{{ habit.name }}</h3>
                      <div class="flex items-center gap-2 sm:gap-3 mt-1 text-[11px] sm:text-xs text-gray-500 dark:text-gray-400 flex-wrap">
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
                          <span class="font-semibold text-gray-900 dark:text-white">{{ habit.current_streak }}</span>
                          <span>day streak</span>
                        </span>
                      </div>
                    </div>
                  </div>
                  <div class="text-right flex-shrink-0">
                    <div class="text-base sm:text-lg font-bold" :style="{ color: habit.color || '#10b981' }">
                      {{ habit.completion_rate }}%
                    </div>
                    <div class="text-[10px] sm:text-xs text-gray-500 dark:text-gray-400 hidden sm:block">completion</div>
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
        <div v-if="publicGoals && publicGoals.length > 0" class="bg-white dark:bg-gray-800 rounded-lg p-3 sm:p-4 border border-gray-200 dark:border-gray-700 transition-colors">
          <div class="flex items-center justify-between mb-3">
            <h2 class="text-sm sm:text-base font-bold text-gray-900 dark:text-white flex items-center gap-2">
              <span>🎯</span>
              Goals
              <span class="text-[10px] sm:text-xs font-normal text-gray-500 dark:text-gray-400">
                ({{ totalActiveGoals }} active<span v-if="totalCompletedGoals > 0">, {{ totalCompletedGoals }} completed</span>)
              </span>
            </h2>
            <div class="flex items-center gap-2">
              <button
                v-if="publicGoals.length > 4"
                @click="goalsExpanded = !goalsExpanded"
                class="text-[10px] sm:text-xs font-medium text-green-600 dark:text-green-400 hover:text-green-700 dark:hover:text-green-300 active:text-green-800 dark:active:text-green-500 transition-colors"
              >
                {{ goalsExpanded ? 'Show less' : 'Show all' }}
              </button>
              <Link
                v-if="isOwnProfile"
                :href="`/@${profileUser.username}/journey/goals`"
                class="text-[10px] sm:text-xs text-green-600 dark:text-green-400 hover:text-green-700 dark:hover:text-green-300 active:text-green-800 dark:active:text-green-500 font-medium"
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
                <h3 class="text-xs sm:text-sm font-bold text-gray-800 dark:text-gray-200">🚀 Active Goals</h3>
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
                    <h4 class="text-[10px] sm:text-xs font-bold text-gray-600 dark:text-gray-400 uppercase tracking-wide">
                      {{ group.label }}
                    </h4>
                    <span class="text-[10px] sm:text-xs text-gray-500 dark:text-gray-400">({{ group.goals.length }})</span>
                  </div>

                  <!-- Goals in this group -->
                  <div class="space-y-2 pl-6">
                    <div
                      v-for="goal in group.goals"
                      :key="goal.id"
                      class="p-2 sm:p-3 border border-gray-200 dark:border-gray-700 rounded-lg hover:border-green-300 dark:hover:border-green-600 active:border-green-400 dark:active:border-green-500 active:bg-green-50/30 dark:active:bg-green-900/20 transition-all"
                    >
                      <div class="flex items-center justify-between mb-2">
                        <div class="flex-1">
                          <h5 class="text-xs sm:text-sm font-semibold text-gray-900 dark:text-white">{{ goal.title }}</h5>
                          <div class="flex gap-1.5 mt-1">
                            <span class="px-1.5 py-0.5 bg-purple-100 dark:bg-purple-900 text-purple-700 dark:text-purple-300 text-[10px] sm:text-xs rounded">
                              {{ goal.life_area }}
                            </span>
                          </div>
                        </div>
                        <div class="text-base sm:text-xl font-bold" :class="goal.progress_percentage >= 100 ? 'text-green-600 dark:text-green-400' : 'text-gray-900 dark:text-white'">
                          {{ goal.progress_percentage }}%
                        </div>
                      </div>

                      <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-1.5">
                        <div
                          class="h-1.5 rounded-full transition-all"
                          :class="goal.on_track ? 'bg-green-500' : 'bg-amber-500'"
                          :style="{ width: `${Math.min(goal.progress_percentage, 100)}%` }"
                        ></div>
                      </div>

                      <div class="flex justify-between text-[10px] sm:text-xs text-gray-600 dark:text-gray-400 mt-1">
                        <span v-if="goal.tracking_type === 'milestone'">
                          {{ goal.milestones_completed }}/{{ goal.milestones_total }} milestones
                        </span>
                        <span v-if="goal.on_track" class="text-green-600 dark:text-green-400 font-medium">On track</span>
                        <span v-else class="text-amber-600 dark:text-amber-400 font-medium">Behind</span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Completed Goals Timeline -->
            <div v-if="completedGoals.length > 0" class="border-t border-gray-200 dark:border-gray-700 pt-4">
              <div class="flex items-center gap-2 mb-3">
                <h3 class="text-xs sm:text-sm font-bold text-gray-800 dark:text-gray-200">✅ Completed Goals</h3>
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
                    <h4 class="text-[10px] sm:text-xs font-bold text-green-700 dark:text-green-400 uppercase tracking-wide">
                      {{ period.label }}
                    </h4>
                    <span class="text-[10px] sm:text-xs text-gray-500 dark:text-gray-400">({{ period.goals.length }})</span>
                  </div>

                  <!-- Completed goals in this period -->
                  <div class="space-y-2 pl-6">
                    <div
                      v-for="goal in period.goals"
                      :key="goal.id"
                      class="p-2 sm:p-3 border border-green-200 dark:border-green-800 rounded-lg bg-green-50/50 dark:bg-green-900/20"
                    >
                      <div class="flex items-center justify-between mb-2">
                        <div class="flex-1 min-w-0">
                          <h5 class="text-xs sm:text-sm font-semibold text-gray-700 dark:text-gray-300 line-through">
                            {{ goal.title }}
                          </h5>
                          <div class="flex gap-1.5 mt-1">
                            <span class="px-1.5 py-0.5 bg-purple-100 dark:bg-purple-900 text-purple-700 dark:text-purple-300 text-[10px] sm:text-xs rounded">
                              {{ goal.life_area }}
                            </span>
                            <span class="text-[10px] sm:text-xs px-1.5 py-0.5 bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-300 rounded font-medium">
                              ✓
                            </span>
                          </div>
                        </div>
                        <div class="text-base sm:text-xl font-bold text-green-600 dark:text-green-400 flex-shrink-0">
                          100%
                        </div>
                      </div>

                      <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-1.5">
                        <div
                          class="h-1.5 rounded-full bg-green-500 transition-all"
                          style="width: 100%"
                        ></div>
                      </div>

                      <div class="flex justify-between text-[10px] sm:text-xs text-gray-600 dark:text-gray-400 mt-1">
                        <span v-if="goal.tracking_type === 'milestone'">
                          {{ goal.milestones_total }}/{{ goal.milestones_total }} milestones
                        </span>
                        <span class="text-green-600 dark:text-green-400 font-medium">Completed</span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Vision Section -->
        <div v-if="visionSnippet && visionSnippet.length > 0" class="bg-gradient-to-br from-indigo-50 to-purple-50 dark:from-indigo-950 dark:to-purple-950 rounded-lg p-3 sm:p-4 border border-indigo-200 dark:border-indigo-800 transition-colors">
          <div class="flex items-center justify-between mb-3">
            <h2 class="text-sm sm:text-base font-bold text-gray-900 dark:text-white flex items-center gap-2">
              <span>🎯</span>
              My Vision
            </h2>
            <button
              @click="visionExpanded = !visionExpanded"
              class="text-[10px] sm:text-xs font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 active:text-indigo-800 dark:active:text-indigo-500 transition-colors"
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
              class="bg-white/60 dark:bg-gray-800/60 rounded-lg p-2 sm:p-3 border border-indigo-100 dark:border-indigo-900"
            >
              <h3 class="text-[10px] sm:text-xs font-bold text-indigo-700 dark:text-indigo-400 uppercase tracking-wide mb-2 flex items-center gap-1">
                <span>{{ getSectionIcon(section.label) }}</span>
                {{ section.label }}
              </h3>
              <div class="text-xs sm:text-sm text-gray-700 dark:text-gray-300 leading-relaxed">
                <!-- Handle string content (mission, eulogy) -->
                <p v-if="typeof section.content === 'string'" class="whitespace-pre-line text-gray-800 dark:text-gray-200">
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
                      class="text-xs sm:text-sm text-gray-800 dark:text-gray-200"
                      :class="item.completed ? 'line-through text-gray-500 dark:text-gray-500' : ''"
                    >
                      {{ item.text }}
                    </span>
                  </div>
                </div>

                <!-- Handle Odyssey Plan (object with three paths) -->
                <div v-else-if="typeof section.content === 'object' && section.label === 'Odyssey Plan'" class="space-y-2 sm:space-y-3">
                  <div v-if="section.content.current_path" class="border-l-3 border-blue-400 pl-2 sm:pl-3">
                    <div class="text-[10px] sm:text-xs font-semibold text-blue-700 dark:text-blue-400 uppercase mb-1">🎯 Current Path</div>
                    <p class="text-xs sm:text-sm text-gray-700 dark:text-gray-300 whitespace-pre-line">{{ section.content.current_path }}</p>
                  </div>
                  <div v-if="section.content.radical_path" class="border-l-3 border-purple-400 pl-2 sm:pl-3">
                    <div class="text-[10px] sm:text-xs font-semibold text-purple-700 dark:text-purple-400 uppercase mb-1">🚀 Radical Path</div>
                    <p class="text-xs sm:text-sm text-gray-700 dark:text-gray-300 whitespace-pre-line">{{ section.content.radical_path }}</p>
                  </div>
                  <div v-if="section.content.alternative_path" class="border-l-3 border-amber-400 pl-2 sm:pl-3">
                    <div class="text-[10px] sm:text-xs font-semibold text-amber-700 dark:text-amber-400 uppercase mb-1">🌟 Alternative Path</div>
                    <p class="text-xs sm:text-sm text-gray-700 dark:text-gray-300 whitespace-pre-line">{{ section.content.alternative_path }}</p>
                  </div>
                </div>

                <!-- Handle Future Calendar (object with ideal days) -->
                <div v-else-if="typeof section.content === 'object' && section.label === 'Future Calendar'" class="space-y-2 sm:space-y-3">
                  <div v-for="(dayContent, dayKey) in section.content" :key="dayKey" class="bg-indigo-50 dark:bg-indigo-950 rounded p-2 border border-indigo-100 dark:border-indigo-900">
                    <div class="text-[10px] sm:text-xs font-semibold text-indigo-800 dark:text-indigo-400 uppercase mb-1">
                      {{ formatCalendarDay(dayKey) }}
                    </div>
                    <p class="text-[10px] sm:text-xs text-gray-700 dark:text-gray-300 whitespace-pre-line leading-relaxed">{{ dayContent }}</p>
                  </div>
                </div>

                <!-- Handle Definition of Success (object with categories) -->
                <div v-else-if="typeof section.content === 'object' && section.label === 'Definition of Success'" class="space-y-1.5 sm:space-y-2">
                  <div v-for="(value, key) in section.content" :key="key" class="flex items-start gap-2">
                    <span class="text-[10px] sm:text-xs font-semibold text-indigo-700 dark:text-indigo-400 uppercase tracking-wide flex-shrink-0 mt-0.5">
                      {{ formatKey(key) }}:
                    </span>
                    <span class="text-xs sm:text-sm text-gray-800 dark:text-gray-200">{{ value }}</span>
                  </div>
                </div>

                <!-- Fallback for other array/object content -->
                <div v-else-if="Array.isArray(section.content)" class="space-y-1">
                  <div v-for="(item, idx) in section.content" :key="idx" class="flex items-start gap-2">
                    <span class="text-gray-400">•</span>
                    <span class="text-xs sm:text-sm text-gray-800 dark:text-gray-200">{{ typeof item === 'string' ? item : item.text || item.title || JSON.stringify(item) }}</span>
                  </div>
                </div>
                <div v-else-if="typeof section.content === 'object'" class="space-y-1">
                  <div v-for="(value, key) in section.content" :key="key" class="text-xs sm:text-sm">
                    <span class="font-medium text-gray-800 dark:text-gray-200">{{ formatKey(key) }}:</span>
                    <span class="text-gray-700 dark:text-gray-300 ml-1">{{ value }}</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Visitor CTA -->
        <div v-if="!$page.props.auth.user" class="bg-gradient-to-r from-green-500 to-emerald-600 dark:from-green-700 dark:to-emerald-800 rounded-lg p-6 text-center text-white transition-colors">
          <div class="text-3xl mb-2">🚀</div>
          <h2 class="text-lg font-bold mb-1">Start Your Journey on LOGGD</h2>
          <p class="text-green-100 dark:text-green-200 text-sm mb-4">Track habits • Set goals • Build consistency</p>
          <Link
            href="/register"
            class="inline-block px-6 py-2 bg-white dark:bg-gray-800 text-green-600 dark:text-green-400 font-semibold text-sm rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 active:bg-gray-200 dark:active:bg-gray-600 transition-colors"
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
        <div class="bg-gray-900 dark:bg-gray-800 text-white px-3 py-2 rounded-lg shadow-xl text-xs transform -translate-x-1/2 -translate-y-full -mt-2 max-w-xs">
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
            <div class="w-0 h-0 border-l-4 border-r-4 border-t-4 border-transparent border-t-gray-900 dark:border-t-gray-800"></div>
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
import ActivityGrid from '../Components/gamification/ActivityGrid.vue';
import ProfileHeader from '../Components/profile/ProfileHeader.vue';

const props = defineProps({
  profileUser: Object,
  stats: Object,
  publicHabits: Array,
  publicGoals: Array,
  activityData: Array,
  visionSnippet: [Array, Object],
  milestones: Array,
  levelInfo: Object,
  activityGridData: Array,
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

// Tier-specific styling for activity grid container - ENHANCED
const activityGridClasses = computed(() => {
  if (!props.levelInfo) return 'bg-white rounded-lg p-3 sm:p-4 border border-gray-200';

  const level = props.levelInfo.level;
  const baseClasses = 'rounded-lg p-3 sm:p-4 border-2 transition-all duration-300';

  if (level >= 51) {
    return `${baseClasses} bg-gradient-to-br from-purple-900 via-fuchsia-800 to-amber-700 border-yellow-400 shadow-2xl shadow-purple-900/50`;
  }
  if (level >= 41) {
    return `${baseClasses} bg-gradient-to-br from-red-900 via-orange-800 to-amber-700 border-orange-400 shadow-2xl shadow-red-900/50`;
  }
  if (level >= 31) {
    return `${baseClasses} bg-gradient-to-br from-blue-900 via-cyan-800 to-sky-700 border-cyan-400 shadow-2xl shadow-blue-900/50`;
  }
  if (level >= 21) {
    return `${baseClasses} bg-gradient-to-br from-violet-900 via-purple-800 to-fuchsia-700 border-purple-400 shadow-2xl shadow-purple-900/50`;
  }
  if (level >= 11) {
    return `${baseClasses} bg-gradient-to-br from-emerald-800 via-green-700 to-teal-600 border-emerald-400 shadow-xl shadow-emerald-900/40`;
  }
  if (level >= 6) {
    return `${baseClasses} bg-gradient-to-br from-green-50 to-emerald-100 border-green-300 shadow-md`;
  }
  return `${baseClasses} bg-white border-gray-200`;
});

const activityTitleColor = computed(() => {
  if (!props.levelInfo) return 'text-gray-900';

  const level = props.levelInfo.level;

  if (level >= 51) return 'text-transparent bg-clip-text bg-gradient-to-r from-yellow-200 via-pink-200 to-purple-200';
  if (level >= 41) return 'text-transparent bg-clip-text bg-gradient-to-r from-yellow-200 via-orange-200 to-red-200';
  if (level >= 31) return 'text-cyan-100';
  if (level >= 21) return 'text-purple-100';
  if (level >= 11) return 'text-emerald-100';
  return 'text-gray-900';
});
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
