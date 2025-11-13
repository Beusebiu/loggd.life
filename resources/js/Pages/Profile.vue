<template>
  <AppLayout>
    <div class="max-w-7xl mx-auto">
      <!-- Compact Header -->
      <div class="sticky top-0 z-10 bg-white/80 backdrop-blur-md border-b border-gray-200 px-4 py-2">
        <div class="flex items-center justify-between">
          <h2 class="text-lg font-bold text-black">
            Profile
            <span v-if="isPreviewMode" class="ml-2 px-2 py-1 bg-blue-100 text-blue-700 text-xs rounded-full font-normal">
              Public Preview
            </span>
          </h2>
          <div class="flex gap-2">
            <Link
              v-if="isActualOwner && !isPreviewMode"
              :href="`/@${profileUser.username}?preview=public`"
              class="flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-blue-700 bg-blue-50 border border-blue-300 rounded-lg hover:bg-blue-100 transition-colors"
            >
              <span>👁️</span>
              Preview Public
            </Link>
            <Link
              v-if="isPreviewMode"
              :href="`/@${profileUser.username}`"
              class="flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors"
            >
              <span>←</span>
              My View
            </Link>
          </div>
        </div>
      </div>

      <div class="px-4 py-4 space-y-4">
        <!-- Compact Hero -->
        <div class="bg-gradient-to-br from-white to-green-50 rounded-lg p-4 border border-gray-200">
          <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
              <div class="w-16 h-16 rounded-full bg-gradient-to-br from-green-500 to-emerald-600 flex items-center justify-center text-white font-bold text-2xl shadow-md">
                {{ profileUser.name.charAt(0).toUpperCase() }}
              </div>
              <div>
                <h1 class="text-xl font-bold text-gray-900">{{ profileUser.name }}</h1>
                <p class="text-sm text-gray-600">
                  @{{ profileUser.username }}
                  <span v-if="stats.activeStreak > 0" class="ml-2 px-2 py-0.5 bg-orange-100 text-orange-700 font-semibold rounded text-xs">
                    🔥 {{ stats.activeStreak }} day streak
                  </span>
                </p>
                <p v-if="profileUser.bio" class="text-xs text-gray-600 mt-1">{{ profileUser.bio }}</p>
              </div>
            </div>

            <div class="flex gap-3 items-center">
              <div class="text-center px-3 py-2 bg-white rounded-lg border border-gray-200">
                <div class="text-2xl font-bold text-gray-900">{{ stats.totalActiveDays }}</div>
                <div class="text-xs text-gray-600">Active Days</div>
              </div>
              <div class="text-center px-3 py-2 bg-white rounded-lg border border-gray-200">
                <div class="text-2xl font-bold text-green-600">{{ stats.activeGoals }}</div>
                <div class="text-xs text-gray-600">Goals</div>
              </div>
              <div class="text-center px-3 py-2 bg-white rounded-lg border border-gray-200">
                <div class="text-2xl font-bold text-amber-600">{{ stats.achievements }}</div>
                <div class="text-xs text-gray-600">Achievements</div>
              </div>
              <Link
                v-if="isOwnProfile"
                :href="`/@${profileUser.username}/settings`"
                class="px-3 py-2 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors"
              >
                Edit
              </Link>
            </div>
          </div>
        </div>

        <!-- Habits with 365-Day Trackers -->
        <div v-if="publicHabits && publicHabits.length > 0" class="bg-white rounded-lg p-4 border border-gray-200">
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
              v-for="habit in publicHabits"
              :key="habit.id"
              class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow"
            >
              <!-- Habit Header -->
              <div class="px-4 py-3 border-b border-gray-100">
                <div class="flex items-center justify-between">
                  <div class="flex items-center gap-3">
                    <span class="text-2xl">{{ habit.emoji }}</span>
                    <div>
                      <h3 class="text-sm font-semibold text-gray-900">{{ habit.name }}</h3>
                      <div class="flex items-center gap-3 mt-1 text-xs text-gray-500">
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
                  <div class="text-right">
                    <div class="text-lg font-bold" :style="{ color: habit.color || '#10b981' }">
                      {{ habit.completion_rate }}%
                    </div>
                    <div class="text-xs text-gray-500">completion</div>
                  </div>
                </div>
              </div>

              <!-- 365-Day GitHub-style Grid -->
              <HabitYearGrid
                :habit="habit"
                @show-tooltip="(event, day) => showTooltip(event, habit, day)"
                @hide-tooltip="hideTooltip"
              />
            </div>
          </div>
        </div>

        <!-- Goals: Active & Timeline -->
        <div v-if="publicGoals && publicGoals.length > 0" class="bg-white rounded-lg p-4 border border-gray-200">
          <div class="flex items-center justify-between mb-3">
            <h2 class="text-base font-bold text-gray-900 flex items-center gap-2">
              <span>🎯</span>
              Goals
              <span class="text-xs font-normal text-gray-500">
                ({{ totalActiveGoals }} active<span v-if="totalCompletedGoals > 0">, {{ totalCompletedGoals }} completed</span>)
              </span>
            </h2>
            <div class="flex items-center gap-2">
              <button
                v-if="publicGoals.length > 4"
                @click="goalsExpanded = !goalsExpanded"
                class="text-xs font-medium text-green-600 hover:text-green-700 transition-colors"
              >
                {{ goalsExpanded ? 'Show less' : 'Show all' }}
              </button>
              <Link
                v-if="isOwnProfile"
                :href="`/@${profileUser.username}/journey/goals`"
                class="text-xs text-green-600 hover:text-green-700 font-medium"
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
                <h3 class="text-sm font-bold text-gray-800">🚀 Active Goals</h3>
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
                    <span class="text-base">{{ group.icon }}</span>
                    <h4 class="text-xs font-bold text-gray-600 uppercase tracking-wide">
                      {{ group.label }}
                    </h4>
                    <span class="text-xs text-gray-500">({{ group.goals.length }})</span>
                  </div>

                  <!-- Goals in this group -->
                  <div class="space-y-2 pl-6">
                    <div
                      v-for="goal in group.goals"
                      :key="goal.id"
                      class="p-3 border border-gray-200 rounded-lg hover:border-green-300 transition-all"
                    >
                      <div class="flex items-center justify-between mb-2">
                        <div class="flex-1">
                          <h5 class="text-sm font-semibold text-gray-900">{{ goal.title }}</h5>
                          <div class="flex gap-1.5 mt-1">
                            <span class="px-1.5 py-0.5 bg-purple-100 text-purple-700 text-xs rounded">
                              {{ goal.life_area }}
                            </span>
                          </div>
                        </div>
                        <div class="text-xl font-bold" :class="goal.progress_percentage >= 100 ? 'text-green-600' : 'text-gray-900'">
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

                      <div class="flex justify-between text-xs text-gray-600 mt-1">
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
                <h3 class="text-sm font-bold text-gray-800">✅ Completed Goals</h3>
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
                    <span class="text-base">🏆</span>
                    <h4 class="text-xs font-bold text-green-700 uppercase tracking-wide">
                      {{ period.label }}
                    </h4>
                    <span class="text-xs text-gray-500">({{ period.goals.length }})</span>
                  </div>

                  <!-- Completed goals in this period -->
                  <div class="space-y-2 pl-6">
                    <div
                      v-for="goal in period.goals"
                      :key="goal.id"
                      class="p-3 border border-green-200 rounded-lg bg-green-50/50"
                    >
                      <div class="flex items-center justify-between mb-2">
                        <div class="flex-1">
                          <div class="flex items-center gap-2">
                            <h5 class="text-sm font-semibold text-gray-700 line-through">
                              {{ goal.title }}
                            </h5>
                            <span class="text-xs px-1.5 py-0.5 bg-green-100 text-green-700 rounded font-medium">
                              ✓ Done
                            </span>
                          </div>
                          <div class="flex gap-1.5 mt-1">
                            <span class="px-1.5 py-0.5 bg-purple-100 text-purple-700 text-xs rounded">
                              {{ goal.life_area }}
                            </span>
                          </div>
                        </div>
                        <div class="text-xl font-bold text-green-600">
                          100%
                        </div>
                      </div>

                      <div class="w-full bg-gray-200 rounded-full h-1.5">
                        <div
                          class="h-1.5 rounded-full bg-green-500 transition-all"
                          style="width: 100%"
                        ></div>
                      </div>

                      <div class="flex justify-between text-xs text-gray-600 mt-1">
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
        <div v-if="visionSnippet && visionSnippet.length > 0" class="bg-gradient-to-br from-indigo-50 to-purple-50 rounded-lg p-4 border border-indigo-200">
          <div class="flex items-center justify-between mb-3">
            <h2 class="text-base font-bold text-gray-900 flex items-center gap-2">
              <span>🎯</span>
              My Vision
            </h2>
            <button
              @click="visionExpanded = !visionExpanded"
              class="text-xs font-medium text-indigo-600 hover:text-indigo-700 transition-colors"
            >
              {{ visionExpanded ? 'Show less' : 'Show more' }}
            </button>
          </div>
          <div
            class="space-y-4 transition-all duration-300 overflow-hidden"
            :class="visionExpanded ? '' : 'max-h-40'"
          >
            <div
              v-for="(section, index) in visionSnippet"
              :key="section.label"
              class="bg-white/60 rounded-lg p-3 border border-indigo-100"
            >
              <h3 class="text-xs font-bold text-indigo-700 uppercase tracking-wide mb-2 flex items-center gap-1">
                <span>{{ getSectionIcon(section.label) }}</span>
                {{ section.label }}
              </h3>
              <div class="text-sm text-gray-700 leading-relaxed">
                <!-- Handle string content (mission, eulogy) -->
                <p v-if="typeof section.content === 'string'" class="whitespace-pre-line text-gray-800">
                  {{ section.content }}
                </p>

                <!-- Handle Bucket List (array with completed status) -->
                <div v-else-if="Array.isArray(section.content) && section.label === 'Bucket List'" class="space-y-1.5">
                  <div
                    v-for="(item, idx) in section.content"
                    :key="idx"
                    class="flex items-start gap-2 py-1"
                  >
                    <span
                      class="text-base mt-0.5 flex-shrink-0"
                      :class="item.completed ? 'text-green-600' : 'text-gray-400'"
                    >
                      {{ item.completed ? '✅' : '⬜' }}
                    </span>
                    <span
                      class="text-gray-800"
                      :class="item.completed ? 'line-through text-gray-500' : ''"
                    >
                      {{ item.text }}
                    </span>
                  </div>
                </div>

                <!-- Handle Odyssey Plan (object with three paths) -->
                <div v-else-if="typeof section.content === 'object' && section.label === 'Odyssey Plan'" class="space-y-3">
                  <div v-if="section.content.current_path" class="border-l-3 border-blue-400 pl-3">
                    <div class="text-xs font-semibold text-blue-700 uppercase mb-1">🎯 Current Path</div>
                    <p class="text-sm text-gray-700 whitespace-pre-line">{{ section.content.current_path }}</p>
                  </div>
                  <div v-if="section.content.radical_path" class="border-l-3 border-purple-400 pl-3">
                    <div class="text-xs font-semibold text-purple-700 uppercase mb-1">🚀 Radical Path</div>
                    <p class="text-sm text-gray-700 whitespace-pre-line">{{ section.content.radical_path }}</p>
                  </div>
                  <div v-if="section.content.alternative_path" class="border-l-3 border-amber-400 pl-3">
                    <div class="text-xs font-semibold text-amber-700 uppercase mb-1">🌟 Alternative Path</div>
                    <p class="text-sm text-gray-700 whitespace-pre-line">{{ section.content.alternative_path }}</p>
                  </div>
                </div>

                <!-- Handle Future Calendar (object with ideal days) -->
                <div v-else-if="typeof section.content === 'object' && section.label === 'Future Calendar'" class="space-y-3">
                  <div v-for="(dayContent, dayKey) in section.content" :key="dayKey" class="bg-indigo-50 rounded p-2 border border-indigo-100">
                    <div class="text-xs font-semibold text-indigo-800 uppercase mb-1">
                      {{ formatCalendarDay(dayKey) }}
                    </div>
                    <p class="text-xs text-gray-700 whitespace-pre-line leading-relaxed">{{ dayContent }}</p>
                  </div>
                </div>

                <!-- Handle Definition of Success (object with categories) -->
                <div v-else-if="typeof section.content === 'object' && section.label === 'Definition of Success'" class="space-y-2">
                  <div v-for="(value, key) in section.content" :key="key" class="flex items-start gap-2">
                    <span class="text-xs font-semibold text-indigo-700 uppercase tracking-wide flex-shrink-0 mt-0.5">
                      {{ formatKey(key) }}:
                    </span>
                    <span class="text-sm text-gray-800">{{ value }}</span>
                  </div>
                </div>

                <!-- Fallback for other array/object content -->
                <div v-else-if="Array.isArray(section.content)" class="space-y-1">
                  <div v-for="(item, idx) in section.content" :key="idx" class="flex items-start gap-2">
                    <span class="text-gray-400">•</span>
                    <span class="text-gray-800">{{ typeof item === 'string' ? item : item.text || item.title || JSON.stringify(item) }}</span>
                  </div>
                </div>
                <div v-else-if="typeof section.content === 'object'" class="space-y-1">
                  <div v-for="(value, key) in section.content" :key="key" class="text-sm">
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
import { ref, computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
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
