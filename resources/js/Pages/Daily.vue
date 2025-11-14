<template>
  <AppLayout>
    <div class="max-w-7xl mx-auto px-3 sm:px-4 md:px-6 lg:px-8 py-4 sm:py-8">
      <!-- Journey Navigation -->
      <JourneyNav />

      <div class="mb-4 sm:mb-8">
        <h1 class="text-xl sm:text-3xl font-bold text-gray-900 mb-1 sm:mb-2">Daily Check-in</h1>
        <p class="text-xs sm:text-base text-gray-600">Start your morning with planning, end your day with reflection</p>
        <p class="text-[10px] sm:text-sm text-gray-500 mt-1 sm:mt-2 italic">💡 A few minutes each day compounds into massive progress.</p>
      </div>

      <!-- Mobile History Button -->
      <button
        @click="showMobileHistory = true"
        class="lg:hidden mb-3 w-full px-3 py-2 bg-white border-2 border-gray-200 rounded-lg hover:border-green-300 transition text-left flex items-center justify-between"
      >
        <span class="text-xs sm:text-sm font-medium text-gray-900">📅 Check-in History</span>
        <span class="text-xs sm:text-sm text-gray-600">{{ recentCheckIns.length }} days</span>
      </button>

      <div class="flex gap-4 sm:gap-6">
        <!-- History Sidebar (Desktop) -->
        <div class="w-64 hidden lg:block">
          <div class="sticky top-8">
            <h3 class="text-sm font-bold text-gray-900 mb-3">📅 Check-in History</h3>
            <div class="space-y-3 max-h-[calc(100vh-12rem)] overflow-y-auto pr-1">
              <template v-for="(entry, index) in recentCheckIns" :key="entry.date">
                <!-- Year separator -->
                <div v-if="index === 0 || getYear(entry.date) !== getYear(recentCheckIns[index - 1].date)" class="sticky top-0 bg-white pt-3 pb-2 z-10 -mx-1 px-1">
                  <div class="flex items-center gap-2">
                    <div class="flex-1 h-px bg-gradient-to-r from-transparent via-gray-300 to-transparent"></div>
                    <div class="text-xs font-bold text-gray-500 uppercase tracking-wide px-2">{{ getYear(entry.date) }}</div>
                    <div class="flex-1 h-px bg-gradient-to-r from-transparent via-gray-300 to-transparent"></div>
                  </div>
                </div>
                <button
                  @click="selectDate(entry.date)"
                  :class="[
                    'w-full text-left px-3 py-2 rounded-lg border-2 transition',
                    selectedDate === entry.date
                      ? 'border-green-500 bg-green-50'
                      : 'border-gray-200 hover:border-green-300 bg-white'
                  ]"
                >
                  <div class="flex items-center justify-between">
                    <div>
                      <div class="text-sm font-medium text-gray-900">
                        {{ formatDateShort(entry.date) }}
                      </div>
                      <div class="text-xs text-gray-500">
                        {{ entry.date === today ? 'Today' : formatRelative(entry.date) }}
                      </div>
                    </div>
                    <div class="text-lg">
                      {{ entry.today_priority ? '✅' : '📝' }}
                    </div>
                  </div>
                </button>
              </template>
              <div v-if="recentCheckIns.length === 0" class="text-sm text-gray-500 text-center py-4">
                No check-ins yet
              </div>
            </div>
          </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1 max-w-4xl">
          <!-- Check-in Streak -->
          <div class="mb-4 sm:mb-6 bg-gradient-to-r from-green-50 to-emerald-50 rounded-xl border-2 border-green-200 p-3 sm:p-6">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 sm:gap-0">
              <div class="flex items-center gap-3 sm:gap-4">
                <div class="flex items-center justify-center w-12 h-12 sm:w-16 sm:h-16 bg-white rounded-full border-2 border-green-300">
                  <span class="text-2xl sm:text-3xl">🔥</span>
                </div>
                <div>
                  <h3 class="text-base sm:text-lg font-bold text-gray-900">Check-in Streak</h3>
                  <p class="text-xs sm:text-sm text-gray-600 mt-0.5">
                    <span class="text-xl sm:text-2xl font-bold text-green-600">{{ currentStreak }}</span>
                    <span class="text-gray-600 ml-1">{{ currentStreak === 1 ? 'day' : 'days' }}</span>
                  </p>
                </div>
              </div>

              <div class="text-left sm:text-right w-full sm:w-auto">
                <div class="text-xs sm:text-sm text-gray-600 mb-1 sm:mb-0">Last 7 days</div>
                <div class="flex gap-1 mt-2">
                  <div
                    v-for="day in last7Days"
                    :key="day.date"
                    :class="[
                      'w-7 h-7 sm:w-8 sm:h-8 rounded-lg transition-all',
                      day.hasCheckIn ? 'bg-green-600 border-2 border-green-700' : 'bg-white border-2 border-gray-200'
                    ]"
                    :title="day.label"
                  >
                    <div class="w-full h-full flex items-center justify-center">
                      <span v-if="day.hasCheckIn" class="text-white text-sm sm:text-lg">✓</span>
                    </div>
                  </div>
                </div>
                <div class="flex gap-1 mt-1 text-[10px] sm:text-xs text-gray-500">
                  <div v-for="day in last7Days" :key="day.date + '-label'" class="w-7 sm:w-8 text-center">
                    {{ day.dayName }}
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Date Navigation -->
          <div class="mb-4 sm:mb-6 flex flex-col md:flex-row md:items-center gap-2 sm:gap-3">
            <div class="flex gap-2 sm:gap-3">
              <button
                @click="navigateDate(-1)"
                class="flex-1 md:flex-none px-2 sm:px-3 py-1.5 sm:py-2 border border-gray-300 rounded-lg hover:bg-gray-50 text-gray-700 text-xs sm:text-sm"
              >
                <span class="hidden sm:inline">← Previous</span>
                <span class="sm:hidden">← Prev</span>
              </button>
              <button
                @click="navigateDate(1)"
                :disabled="selectedDate === today"
                :class="[
                  'flex-1 md:flex-none px-2 sm:px-3 py-1.5 sm:py-2 border border-gray-300 rounded-lg text-gray-700 text-xs sm:text-sm',
                  selectedDate === today ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-50'
                ]"
              >
                <span class="hidden sm:inline">Next →</span>
                <span class="sm:hidden">Next →</span>
              </button>
            </div>
            <div class="flex items-center gap-2 w-full md:w-auto">
              <input
                v-model="selectedDate"
                @change="loadCheckIn"
                type="date"
                :max="today"
                class="flex-1 px-2 sm:px-4 py-1.5 sm:py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 text-xs sm:text-sm"
              />
              <span class="hidden md:inline text-xs sm:text-sm text-gray-600 whitespace-nowrap">
                {{ selectedDate === today ? '📝 Today' : '📅 Past' }}
              </span>
            </div>
          </div>

          <div class="space-y-4 sm:space-y-6">
            <!-- ==================== MORNING START ==================== -->
            <div class="bg-gradient-to-r from-amber-50 to-orange-50 rounded-xl border-2 border-amber-200 p-3 sm:p-6">
              <div class="flex items-center gap-2 mb-3 sm:mb-4">
                <span class="text-xl sm:text-2xl">🌅</span>
                <h2 class="text-lg sm:text-xl font-bold text-gray-900">Morning Start</h2>
              </div>
              <p class="text-xs sm:text-sm text-gray-600 mb-4 sm:mb-6">Fill this when you wake up to plan your day</p>

              <!-- Yesterday's Quick Review -->
              <div class="mb-6">
                <h3 class="text-md font-bold text-gray-900 mb-3">📊 Yesterday's Quick Review</h3>
                <div class="bg-white rounded-lg border border-amber-200 p-4 space-y-4">
                  <div class="flex items-center gap-3">
                    <label class="flex items-center cursor-pointer">
                      <input
                        type="checkbox"
                        v-model="checkIn.yesterday_priority_completed"
                        @change="saveCheckIn"
                        class="w-5 h-5 text-green-600 border-gray-300 rounded focus:ring-green-500"
                      />
                      <span class="ml-2 text-sm font-medium text-gray-700">Yesterday's priority completed</span>
                    </label>
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">What prevented it / went better than expected?</label>
                    <TextareaWithEmoji
                      v-model="checkIn.yesterday_review"
                      @blur="saveCheckIn"
                      rows="3"
                      placeholder="I got distracted by meetings... OR... I finished early because I time-blocked it!"
                    />
                  </div>
                </div>
              </div>

              <!-- Today's Priority & Tasks -->
              <div class="mb-6">
                <h3 class="text-md font-bold text-gray-900 mb-3">🎯 Today's Focus</h3>
                <div class="bg-white rounded-lg border border-amber-200 p-4 space-y-4">
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Priority (The ONE thing)</label>
                    <div class="mb-2 text-xs text-gray-500 bg-blue-50 border border-blue-200 rounded-lg p-2">
                      💡 Be specific: "Finish user authentication" not "work on app"
                    </div>
                    <input
                      v-model="checkIn.today_priority"
                      @blur="saveCheckIn"
                      type="text"
                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500"
                      placeholder="Write the invoice for client X and send it by 2pm"
                    />
                  </div>

                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Supporting Tasks (Optional)</label>
                    <div class="space-y-2">
                      <div v-for="(task, index) in checkIn.today_tasks" :key="index" class="flex gap-2">
                        <span class="text-gray-400 mt-2">•</span>
                        <input
                          v-model="checkIn.today_tasks[index]"
                          @blur="saveCheckIn"
                          type="text"
                          class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500"
                          placeholder="Reply to 3 emails, review pull request..."
                        />
                        <button
                          @click="checkIn.today_tasks.splice(index, 1); saveCheckIn()"
                          class="text-red-500 hover:text-red-700"
                        >
                          <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                          </svg>
                        </button>
                      </div>
                      <button
                        @click="checkIn.today_tasks.push('')"
                        class="text-sm text-gray-600 hover:text-gray-700 font-medium px-3 py-2 bg-gray-50 rounded-lg"
                      >
                        + Add Task
                      </button>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Active Goals Work -->
              <div>
                <h3 class="text-md font-bold text-gray-900 mb-3">🎯 Goal Work Today</h3>
                <div class="bg-white rounded-lg border border-amber-200 p-4 space-y-4">
                  <div v-if="activeGoals.length === 0" class="text-sm text-gray-500 italic">
                    No active quarterly/monthly goals. <a :href="`/@${user.username}/journey/goals`" class="text-green-600 hover:underline">Create one?</a>
                  </div>

                  <div v-else>
                    <label class="block text-sm font-medium text-gray-700 mb-3">Which goals will you work on today?</label>
                    <div class="space-y-2">
                      <label
                        v-for="goal in activeGoals"
                        :key="goal.id"
                        class="flex items-start gap-3 p-3 border border-gray-200 rounded-lg hover:border-green-300 cursor-pointer transition"
                        :class="selectedGoalIds.includes(goal.id) ? 'bg-green-50 border-green-400' : 'bg-white'"
                      >
                        <input
                          type="checkbox"
                          :value="goal.id"
                          v-model="selectedGoalIds"
                          @change="saveCheckIn"
                          class="mt-0.5 w-5 h-5 text-green-600 border-gray-300 rounded focus:ring-green-500"
                        />
                        <div class="flex-1">
                          <div class="font-medium text-gray-900">{{ goal.title }}</div>
                          <div class="text-xs text-gray-500 mt-0.5">
                            {{ formatTimeHorizon(goal.time_horizon) }} • {{ goal.life_area }}
                            <span v-if="goal.tracking_type === 'metric'">
                              • {{ goal.metric_progress_percentage }}% complete
                            </span>
                            <span v-else-if="goal.tracking_type === 'milestone' && goal.milestones">
                              • {{ goal.milestones.filter(m => m.completed).length }}/{{ goal.milestones.length }} milestones
                            </span>
                          </div>
                        </div>
                      </label>
                    </div>

                    <div v-if="selectedGoalIds.length > 0" class="mt-4">
                      <label class="block text-sm font-medium text-gray-700 mb-2">What specific work will you do?</label>
                      <div class="mb-2 text-xs text-gray-500 bg-blue-50 border border-blue-200 rounded-lg p-2">
                        💡 Be specific about what you'll do on these goals today
                      </div>
                      <TextareaWithEmoji
                        v-model="checkIn.goal_work_details"
                        @blur="saveCheckIn"
                        rows="3"
                        placeholder="Build the user authentication API, reach out to 5 potential clients, write first draft of proposal..."
                      />
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- ==================== TODAY'S HABITS ==================== -->
            <div class="bg-gradient-to-r from-cyan-50 to-teal-50 rounded-xl border-2 border-cyan-200 p-6">
              <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-2">
                  <span class="text-2xl">⚡</span>
                  <h2 class="text-xl font-bold text-gray-900">Today's Habits</h2>
                </div>
                <a :href="`/@${user.username}/habits`" class="text-sm text-teal-600 hover:underline font-medium">Manage Habits →</a>
              </div>
              <p class="text-sm text-gray-600 mb-6">Check off your habits as you complete them throughout the day</p>

              <div v-if="loadingHabits" class="text-center py-8 text-gray-500">
                Loading habits...
              </div>

              <div v-else-if="todaysHabits.length === 0" class="text-center py-8">
                <div class="mb-4">
                  <span class="text-4xl">🌱</span>
                </div>
                <p class="text-gray-600 mb-2">No habits set up yet</p>
                <a :href="`/@${user.username}/habits`" class="text-sm text-teal-600 hover:underline font-medium">Create your first habit →</a>
              </div>

              <div v-else class="space-y-2">
                <div
                  v-for="habit in todaysHabits"
                  :key="habit.id"
                  class="bg-white rounded-lg border-2 p-4 transition"
                  :class="habitChecks[habit.id] ? 'border-teal-400 bg-teal-50' : 'border-gray-200 hover:border-teal-300'"
                >
                  <div class="flex items-center gap-3">
                    <button
                      @click="toggleHabit(habit.id)"
                      class="flex-shrink-0 w-6 h-6 rounded-full border-2 transition flex items-center justify-center"
                      :class="habitChecks[habit.id]
                        ? 'border-teal-500 bg-teal-500'
                        : 'border-gray-300 hover:border-teal-400'"
                    >
                      <svg
                        v-if="habitChecks[habit.id]"
                        class="w-4 h-4 text-white"
                        fill="currentColor"
                        viewBox="0 0 20 20"
                      >
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                      </svg>
                    </button>

                    <div class="flex-1">
                      <div class="flex items-center gap-2">
                        <span v-if="habit.emoji" class="text-lg">{{ habit.emoji }}</span>
                        <span
                          class="font-semibold transition"
                          :class="habitChecks[habit.id] ? 'text-teal-700 line-through' : 'text-gray-900'"
                        >
                          {{ habit.name }}
                        </span>
                      </div>
                      <div v-if="habit.description" class="text-xs text-gray-500 mt-1">
                        {{ habit.description }}
                      </div>
                    </div>

                    <div v-if="habit.currentStreak > 0" class="text-right">
                      <div class="text-xs text-gray-500">Streak</div>
                      <div class="text-sm font-bold text-teal-600">{{ habit.currentStreak }} 🔥</div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- ==================== EVENING REFLECTION ==================== -->
            <div class="bg-gradient-to-r from-indigo-50 to-purple-50 rounded-xl border-2 border-indigo-200 p-6">
              <div class="flex items-center gap-2 mb-4">
                <span class="text-2xl">🌙</span>
                <h2 class="text-xl font-bold text-gray-900">Evening Reflection</h2>
                <span class="text-sm text-gray-500 ml-2">(Optional - fill at end of day)</span>
              </div>
              <p class="text-sm text-gray-600 mb-6">Quick reflection on your day - what went well, what was tough, what you're grateful for</p>

              <!-- Day Reflection -->
              <div class="mb-6">
                <h3 class="text-md font-bold text-gray-900 mb-3">💭 Day Reflection</h3>
                <div class="bg-white rounded-lg border border-indigo-200 p-4">
                  <div class="mb-2 text-xs text-gray-500 bg-blue-50 border border-blue-200 rounded-lg p-2">
                    💡 Keep it simple: Wins, challenges, learnings, gratitude - whatever feels important
                  </div>
                  <TextareaWithEmoji
                    v-model="checkIn.day_reflection"
                    @blur="saveCheckIn"
                    rows="6"
                    placeholder="Today was good! ✅ Finished the client invoice on time. 🤔 Got distracted by emails - need to time-block better. 🙏 Grateful for supportive team and good health."
                  />
                </div>
              </div>

              <!-- Mood -->
              <div>
                <h3 class="text-md font-bold text-gray-900 mb-3">😊 How did you feel today?</h3>
                <div class="bg-white rounded-lg border border-indigo-200 p-4">
                  <div class="flex justify-between items-center mb-2">
                    <label class="text-sm font-medium text-gray-700">Overall Mood</label>
                    <span class="text-lg font-bold text-purple-600">{{ checkIn.mood || 5 }}/10</span>
                  </div>
                  <input
                    v-model.number="checkIn.mood"
                    @change="saveCheckIn"
                    type="range"
                    min="1"
                    max="10"
                    class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-purple-600"
                  />
                  <div class="flex justify-between text-xs text-gray-500 mt-1">
                    <span>Low</span>
                    <span>Great</span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Save Status -->
          <div v-if="saving" class="fixed bottom-4 right-4 bg-green-600 text-white px-4 py-2 rounded-lg shadow-lg">
            Saving...
          </div>
        </div>
      </div>

      <!-- Mobile History Modal -->
      <Transition
        enter-active-class="transition-opacity duration-200"
        leave-active-class="transition-opacity duration-200"
        enter-from-class="opacity-0"
        leave-to-class="opacity-0"
      >
        <div v-if="showMobileHistory" @click="showMobileHistory = false" class="lg:hidden fixed inset-0 bg-black bg-opacity-50 z-40"></div>
      </Transition>

      <Transition
        enter-active-class="transition-transform duration-300"
        leave-active-class="transition-transform duration-300"
        enter-from-class="translate-y-full"
        leave-to-class="translate-y-full"
      >
        <div v-if="showMobileHistory" class="lg:hidden fixed bottom-0 left-0 right-0 bg-white rounded-t-2xl shadow-2xl z-50 h-[85vh] flex flex-col">
          <!-- Modal Header -->
          <div class="sticky top-0 bg-white border-b border-gray-200 p-4 sm:p-6 rounded-t-2xl flex items-center justify-between flex-shrink-0">
            <h3 class="text-base sm:text-lg font-bold text-gray-900">📅 Check-in History</h3>
            <button @click="showMobileHistory = false" class="text-gray-500 hover:text-gray-700 p-2">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>
          </div>

          <!-- Scrollable History List -->
          <div class="relative flex-1 min-h-0 overflow-hidden">
            <div class="overflow-y-scroll h-full p-4 sm:p-6 space-y-3" style="-webkit-overflow-scrolling: touch; overscroll-behavior: contain;">
              <template v-for="(entry, index) in recentCheckIns" :key="entry.date">
                <!-- Year separator -->
                <div v-if="index === 0 || getYear(entry.date) !== getYear(recentCheckIns[index - 1].date)" class="sticky top-0 bg-white pt-3 pb-2 z-10">
                  <div class="flex items-center gap-2">
                    <div class="flex-1 h-px bg-gradient-to-r from-transparent via-gray-300 to-transparent"></div>
                    <div class="text-xs font-bold text-gray-600 uppercase tracking-wide px-2 bg-white">{{ getYear(entry.date) }}</div>
                    <div class="flex-1 h-px bg-gradient-to-r from-transparent via-gray-300 to-transparent"></div>
                  </div>
                </div>
                <button
                  @click="selectDate(entry.date); showMobileHistory = false"
                  :class="[
                    'w-full text-left px-3 py-2 rounded-lg border-2 transition',
                    selectedDate === entry.date
                      ? 'border-green-500 bg-green-50'
                      : 'border-gray-200 hover:border-green-300 bg-white'
                  ]"
                >
                  <div class="flex items-center justify-between">
                    <div>
                      <div class="text-sm font-medium text-gray-900">
                        {{ formatDateShort(entry.date) }}
                      </div>
                      <div class="text-xs text-gray-500">
                        {{ entry.date === today ? 'Today' : formatRelative(entry.date) }}
                      </div>
                    </div>
                    <div class="text-lg">
                      {{ entry.today_priority ? '✅' : '📝' }}
                    </div>
                  </div>
                </button>
              </template>
              <div v-if="recentCheckIns.length === 0" class="text-sm text-gray-500 text-center py-4">
                No check-ins yet
              </div>
            </div>
            <!-- Scroll fade indicator -->
            <div v-if="recentCheckIns.length > 8" class="absolute bottom-0 left-0 right-0 h-12 bg-gradient-to-t from-white to-transparent pointer-events-none"></div>
          </div>
        </div>
      </Transition>
    </div>
  </AppLayout>
</template>

<script setup>
import AppLayout from '../Layouts/AppLayout.vue';
import JourneyNav from '../Components/journey/JourneyNav.vue';
import TextareaWithEmoji from '../Components/TextareaWithEmoji.vue';
import { usePage } from '@inertiajs/vue3';
import { ref, computed, onMounted, watch } from 'vue';
import { formatDateShort, formatRelative, formatTimeHorizon } from '../utils/dateFormatters';

const page = usePage();
const user = computed(() => page.props.auth.user);

const today = new Date().toISOString().split('T')[0];
const selectedDate = ref(today);
const saving = ref(false);
const recentCheckIns = ref([]);
const showMobileHistory = ref(false);
const activeGoals = ref([]);
const selectedGoalIds = ref([]);
const allCheckIns = ref({}); // Map of date -> check-in data for grid

// Habits data
const todaysHabits = ref([]);
const habitChecks = ref({}); // Map of habit_id -> boolean (is checked today)
const loadingHabits = ref(false);

const checkIn = ref({
  date: today,
  // Morning Planning
  yesterday_priority_completed: false,
  yesterday_review: '',
  today_priority: '',
  today_tasks: [],
  goals_worked_on: [],
  goal_work_details: '',
  // Evening Reflection
  day_reflection: '',
  mood: 5,
});

// Sync selectedGoalIds with checkIn.goals_worked_on
watch(selectedGoalIds, (newIds) => {
  checkIn.value.goals_worked_on = newIds;
}, { deep: true });

// Last 7 days for streak display
const last7Days = computed(() => {
  const result = [];
  const todayDate = new Date();

  for (let i = 6; i >= 0; i--) {
    const date = new Date(todayDate);
    date.setDate(date.getDate() - i);
    const dateString = date.toISOString().split('T')[0];
    const hasCheckIn = allCheckIns.value[dateString] !== undefined;

    result.push({
      date: dateString,
      hasCheckIn,
      dayName: date.toLocaleDateString('en-US', { weekday: 'short' }),
      label: date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' })
    });
  }

  return result;
});

// Current streak calculation
const currentStreak = computed(() => {
  const todayDate = new Date();
  const todayString = todayDate.toISOString().split('T')[0];

  // Start from today if there's a check-in, otherwise start from yesterday
  let currentDate = new Date(todayDate);
  if (!allCheckIns.value[todayString]) {
    // No check-in for today yet, start counting from yesterday
    currentDate.setDate(currentDate.getDate() - 1);
  }

  let streak = 0;
  while (true) {
    const dateString = currentDate.toISOString().split('T')[0];
    if (allCheckIns.value[dateString]) {
      streak++;
      currentDate.setDate(currentDate.getDate() - 1);
    } else {
      break;
    }
  }

  return streak;
});

const loadActiveGoals = async () => {
  try {
    const response = await window.axios.get('/api/journey/goals');
    // Flatten the grouped goals and filter for quarterly/monthly active goals
    const allGoals = Object.values(response.data).flat();
    activeGoals.value = allGoals.filter(goal =>
      (goal.time_horizon === 'quarterly' || goal.time_horizon === 'monthly') &&
      goal.status === 'active'
    );
  } catch (error) {
    console.error('Error loading active goals:', error);
  }
};

// Load today's habits
const loadHabits = async () => {
  loadingHabits.value = true;
  try {
    // Fetch active habits
    const habitsResponse = await window.axios.get('/api/habits', {
      params: { status: 'active' }
    });
    const habits = habitsResponse.data;

    // Fetch stats for each habit to get current streak
    const habitsWithStats = await Promise.all(
      habits.map(async (habit) => {
        try {
          const statsResponse = await window.axios.get(`/api/habits/${habit.id}/stats`);
          return {
            ...habit,
            currentStreak: statsResponse.data.current_streak || 0
          };
        } catch (error) {
          return { ...habit, currentStreak: 0 };
        }
      })
    );

    todaysHabits.value = habitsWithStats;

    // Load checks for selected date
    await loadHabitChecks();
  } catch (error) {
    console.error('Error loading habits:', error);
  } finally {
    loadingHabits.value = false;
  }
};

// Load habit checks for the selected date
const loadHabitChecks = async () => {
  try {
    const checksMap = {};

    for (const habit of todaysHabits.value) {
      try {
        const response = await window.axios.get(`/api/habits/${habit.id}/checks`, {
          params: {
            from: selectedDate.value,
            to: selectedDate.value
          }
        });

        const checks = response.data;
        checksMap[habit.id] = checks.some(check => check.checked === true);
      } catch (error) {
        checksMap[habit.id] = false;
      }
    }

    habitChecks.value = checksMap;
  } catch (error) {
    console.error('Error loading habit checks:', error);
  }
};

// Toggle habit completion
const toggleHabit = async (habitId) => {
  try {
    await window.axios.post(`/api/habits/${habitId}/check`, {
      date: selectedDate.value
    });

    // Toggle the local state
    habitChecks.value[habitId] = !habitChecks.value[habitId];

    // Reload habits to update streaks
    await loadHabits();
  } catch (error) {
    console.error('Error toggling habit:', error);
  }
};

const loadCheckIn = async () => {
  try {
    const response = await window.axios.get(`/api/journey/daily/${selectedDate.value}`);
    const data = response.data;

    // Merge with defaults to ensure all fields exist and null values become empty strings
    checkIn.value = {
      date: data.date || selectedDate.value,
      // Morning Planning
      yesterday_priority_completed: data.yesterday_priority_completed || false,
      yesterday_review: data.yesterday_review || '',
      today_priority: data.today_priority || '',
      today_tasks: data.today_tasks || [],
      goal_work_today: data.goal_work_today || false,
      goal_work_details: data.goal_work_details || '',
      // Evening Reflection
      day_reflection: data.day_reflection || '',
      mood: data.mood || 5,
      // Other
      goals_worked_on: data.goals_worked_on || [],
      goal_specific_tasks: data.goal_specific_tasks || [],
      habit_completions: data.habit_completions || [],
      notes: data.notes || '',
      is_public: data.is_public || false,
      share_wins_to_feed: data.share_wins_to_feed || false,
    };

    // Sync selected goals
    selectedGoalIds.value = checkIn.value.goals_worked_on || [];
  } catch (error) {
    console.error('Error loading check-in:', error);
  }
};

const loadRecentCheckIns = async () => {
  try {
    // Request more check-ins to ensure we have enough for streak calculation
    const response = await window.axios.get('/api/journey/daily', {
      params: { limit: 100 }
    });
    recentCheckIns.value = response.data;

    // Build allCheckIns map for grid
    // API returns dates as "2025-11-13T00:00:00.000000Z", extract just "2025-11-13"
    const checkInsMap = {};
    response.data.forEach(checkIn => {
      const dateOnly = checkIn.date.split('T')[0]; // Extract YYYY-MM-DD from ISO timestamp
      checkInsMap[dateOnly] = checkIn;
    });
    allCheckIns.value = checkInsMap;
  } catch (error) {
    console.error('Error loading recent check-ins:', error);
  }
};

const saveCheckIn = async () => {
  saving.value = true;

  try {
    checkIn.value.date = selectedDate.value;
    await window.axios.post('/api/journey/daily', checkIn.value);
    await loadRecentCheckIns(); // Refresh the history
    setTimeout(() => { saving.value = false; }, 500);
  } catch (error) {
    console.error('Error saving check-in:', error);
    saving.value = false;
  }
};

const selectDate = (date) => {
  selectedDate.value = date;
  loadCheckIn();
  loadHabitChecks();
};

const navigateDate = (days) => {
  const current = new Date(selectedDate.value);
  current.setDate(current.getDate() + days);
  selectedDate.value = current.toISOString().split('T')[0];
  loadCheckIn();
  loadHabitChecks();
};

const getYear = (dateString) => {
  return new Date(dateString).getFullYear();
};

onMounted(() => {
  loadCheckIn();
  loadRecentCheckIns();
  loadActiveGoals();
  loadHabits();
});
</script>
