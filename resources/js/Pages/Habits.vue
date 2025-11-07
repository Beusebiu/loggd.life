<template>
  <div class="min-h-screen bg-gray-50">
    <AppNav current-page="habits" />

    <main class="max-w-7xl mx-auto px-4 py-6">
      <!-- Header with Quick Add -->
      <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Habits</h1>
        <button
          @click="showCreateModal = true"
          class="px-4 py-2 bg-black text-white text-sm font-medium rounded-lg hover:bg-gray-800 transition-colors"
        >
          + New Habit
        </button>
      </div>

      <!-- Loading State -->
      <LoadingSpinner v-if="loading" />

      <!-- Active Habits -->
      <div v-else-if="activeHabits.length > 0" class="space-y-4">
        <HabitCard
          v-for="(habit, index) in activeHabits"
          :key="habit.id"
          :habit="habit"
          :index="index"
          :stats="habitStats[habit.id]"
          :animating-streaks="animatingStreaks"
          :animating-checks="animatingChecks"
          :month-labels="getMonthLabels()"
          :year-grid="getFullYearGrid()"
          :week-range="getCurrentWeekRange(habit.id)"
          :week-offset="getWeekOffset(habit.id)"
          :week-days="getCurrentWeekDays(habit.id)"
          :format-start-date="formatStartDate"
          :get-square-class="getSquareClass"
          :get-square-style="getSquareStyle"
          :format-day-name="formatDayName"
          :is-future-date="isFutureDate"
          :is-checked="isChecked"
          :is-today="isToday"
          :format-date-full="formatDateFull"
          :has-note="hasNote"
          :get-check-count="getCheckCount"
          :format-date-with-month="formatDateWithMonth"
          @edit="openEditModal"
          @archive="confirmArchive"
          @show-tooltip="({ event, habitId, date }) => showTooltip(event, habitId, date)"
          @hide-tooltip="hideTooltip"
          @previous-week="goToPreviousWeek"
          @this-week="goToThisWeek"
          @next-week="goToNextWeek"
          @toggle-check="({ habitId, date }) => toggleCheck(habitId, date)"
          @open-note="({ habitId, date }) => openNoteModal(habitId, date)"
          @open-multi-check="({ habit, date }) => openMultiCheckModal(habit, date)"
        />
      </div>

      <!-- Empty State -->
      <EmptyState
        v-else
        icon="🎯"
        title="No habits yet"
        description="Start tracking your daily habits to see your progress"
      >
        <template #action>
          <button
            @click="showCreateModal = true"
            class="px-6 py-2 bg-black text-white text-sm font-medium rounded-lg hover:bg-gray-800 transition-colors"
          >
            Create Your First Habit
          </button>
        </template>
      </EmptyState>

      <!-- Archived Habits Section -->
      <div v-if="archivedHabits.length > 0" class="mt-12 pt-8 border-t-2 border-gray-200">
        <div class="flex items-center justify-between mb-4">
          <h2 class="text-lg font-semibold text-gray-700">Archived Habits</h2>
          <button
            @click="showArchived = !showArchived"
            class="text-sm text-gray-500 hover:text-gray-700"
          >
            {{ showArchived ? 'Hide' : 'Show' }} ({{ archivedHabits.length }})
          </button>
        </div>

        <div v-if="showArchived" class="space-y-3">
          <div
            v-for="habit in archivedHabits"
            :key="habit.id"
            class="flex items-center justify-between px-4 py-3 bg-gray-100 rounded-lg"
          >
            <div class="flex items-center gap-3">
              <span class="text-xl opacity-60">{{ habit.emoji || '📌' }}</span>
              <div>
                <span class="text-sm font-medium text-gray-700">{{ habit.name }}</span>
                <div class="text-xs text-gray-500 mt-0.5">
                  Archived • Started {{ formatStartDate(habit.start_date) }}
                </div>
              </div>
            </div>
            <div class="flex items-center gap-2">
              <button
                @click="reactivateHabit(habit.id)"
                class="px-3 py-1.5 text-xs bg-white border border-gray-300 text-gray-700 rounded hover:bg-gray-50 transition-colors"
              >
                Reactivate
              </button>
              <button
                @click="confirmDelete(habit.id)"
                class="px-3 py-1.5 text-xs text-red-600 hover:bg-red-50 rounded transition-colors"
              >
                Delete
              </button>
            </div>
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
          <div class="flex items-center gap-2 mb-1">
            <span v-if="isChecked(tooltip.habitId, tooltip.date)" class="text-green-400">
              ✓ Completed
              <span v-if="getTooltipHabit(tooltip.habitId)?.allow_multiple_checks && getCheckCount(tooltip.habitId, tooltip.date) > 1">
                ({{ getCheckCount(tooltip.habitId, tooltip.date) }}x)
              </span>
            </span>
            <span v-else class="text-gray-400">○ Not done</span>
          </div>
          <div v-if="tooltip.notes && tooltip.notes.length > 0" class="mt-1.5 pt-1.5 border-t border-gray-700">
            <div class="text-gray-400 text-[10px] mb-0.5">
              {{ tooltip.notes.length > 1 ? 'Notes:' : 'Note:' }}
            </div>
            <div class="space-y-1">
              <div
                v-for="(note, index) in tooltip.notes"
                :key="index"
                class="text-gray-200 text-xs"
              >
                <span v-if="tooltip.notes.length > 1" class="text-gray-500">• </span>{{ note }}
              </div>
            </div>
          </div>
          <!-- Arrow -->
          <div class="absolute left-1/2 bottom-0 transform -translate-x-1/2 translate-y-full">
            <div class="w-0 h-0 border-l-4 border-r-4 border-t-4 border-transparent border-t-gray-900"></div>
          </div>
        </div>
        </div>
      </Transition>
    </main>

    <!-- Create/Edit Modal -->
    <Transition name="modal">
      <div
        v-if="showCreateModal || editingHabit"
        class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4"
        @click.self="closeModal"
      >
        <Transition name="modal-content">
          <div v-if="showCreateModal || editingHabit" class="bg-white rounded-xl shadow-2xl max-w-md w-full">
        <div class="px-6 py-4 border-b border-gray-200">
          <h3 class="text-lg font-semibold text-gray-900">
            {{ editingHabit ? 'Edit Habit' : 'Create New Habit' }}
          </h3>
        </div>

        <div class="px-6 py-5 space-y-4">
          <!-- Emoji Picker -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Emoji</label>
            <button
              type="button"
              @click="openEmojiPicker"
              class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg hover:border-gray-400 transition-colors flex items-center justify-center gap-2 text-3xl"
            >
              {{ habitForm.emoji }}
            </button>
          </div>

          <!-- Habit Name -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Habit Name</label>
            <input
              v-model="habitForm.name"
              type="text"
              placeholder="e.g., Morning workout"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-transparent"
              autofocus
            />
          </div>

          <!-- Color Picker -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Color</label>
            <div class="flex gap-2">
              <button
                v-for="color in habitColors"
                :key="color"
                @click="habitForm.color = color"
                class="w-10 h-10 rounded-lg transition-all"
                :class="habitForm.color === color ? 'ring-2 ring-offset-2 ring-black' : 'hover:scale-110'"
                :style="{ backgroundColor: color }"
              ></button>
            </div>
          </div>

          <!-- Description (optional) -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Description <span class="text-gray-400 text-xs">(optional)</span>
            </label>
            <textarea
              v-model="habitForm.description"
              rows="2"
              placeholder="Why is this habit important?"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-transparent resize-none"
            ></textarea>
          </div>

          <!-- Allow Multiple Checks Per Day -->
          <div>
            <label class="flex items-center cursor-pointer">
              <input
                v-model="habitForm.allow_multiple_checks"
                type="checkbox"
                class="w-5 h-5 text-black border-gray-300 rounded focus:ring-2 focus:ring-black"
              />
              <span class="ml-3 text-sm font-medium text-gray-700">
                Allow multiple checks per day
              </span>
            </label>
            <p class="mt-1 ml-8 text-xs text-gray-500">
              Enable this to track multiple completions per day with timestamps
            </p>
          </div>
        </div>

        <div class="px-6 py-4 border-t border-gray-200 flex gap-3">
          <button
            @click="saveHabit"
            :disabled="!habitForm.name"
            class="flex-1 px-4 py-2 bg-black text-white font-medium rounded-lg hover:bg-gray-800 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
          >
            {{ editingHabit ? 'Save Changes' : 'Create Habit' }}
          </button>
          <button
            @click="closeModal"
            class="px-4 py-2 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors"
          >
            Cancel
          </button>
        </div>
        </div>
        </Transition>
      </div>
    </Transition>

    <!-- Note Modal -->
    <Transition name="modal">
      <div
        v-if="noteModal.show"
        class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4"
        @click.self="closeNoteModal"
      >
        <Transition name="modal-content">
          <div v-if="noteModal.show" class="bg-white rounded-xl shadow-2xl max-w-md w-full">
        <div class="px-6 py-4 border-b border-gray-200">
          <h3 class="text-lg font-semibold text-gray-900">
            Note for {{ formatDateFull(noteModal.date) }}
          </h3>
        </div>

        <div class="px-6 py-5">
          <textarea
            v-model="noteModal.note"
            rows="4"
            placeholder="Add a note about this day..."
            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-transparent resize-none"
            autofocus
          ></textarea>
        </div>

        <div class="px-6 py-4 border-t border-gray-200 flex gap-3">
          <button
            @click="saveNote"
            class="flex-1 px-4 py-2 bg-black text-white font-medium rounded-lg hover:bg-gray-800 transition-colors"
          >
            Save Note
          </button>
          <button
            @click="closeNoteModal"
            class="px-4 py-2 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors"
          >
            Cancel
          </button>
        </div>
        </div>
        </Transition>
      </div>
    </Transition>

    <!-- Multi-Check Modal (for habits with multiple checks per day) -->
    <Transition name="modal">
      <div
        v-if="multiCheckModal.show"
        class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4"
        @click.self="closeMultiCheckModal"
      >
        <Transition name="modal-content">
          <div v-if="multiCheckModal.show" class="bg-white rounded-xl shadow-2xl max-w-md w-full">
        <div class="px-6 py-4 border-b border-gray-200">
          <h3 class="text-lg font-semibold text-gray-900">
            {{ multiCheckModal.habit?.emoji }} {{ multiCheckModal.habit?.name }}
          </h3>
          <p class="text-sm text-gray-600 mt-1">{{ formatDateFull(multiCheckModal.date) }}</p>
        </div>

        <div class="px-6 py-5">
          <!-- Add New Check -->
          <div class="mb-4 pb-4 border-b border-gray-200">
            <label class="block text-sm font-medium text-gray-700 mb-2">Add Check</label>
            <div class="space-y-2">
              <input
                v-model="multiCheckModal.newCheckTime"
                type="time"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-transparent"
              />
              <input
                v-model="multiCheckModal.newCheckNote"
                type="text"
                placeholder="Add a note (optional)"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-transparent"
              />
              <button
                @click="addMultiCheck"
                class="w-full px-4 py-2 bg-black text-white font-medium rounded-lg hover:bg-gray-800 transition-colors"
              >
                + Add Check
              </button>
            </div>
          </div>

          <!-- List of Checks -->
          <div v-if="multiCheckModal.checks.length > 0">
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Checks Today ({{ multiCheckModal.checks.length }})
            </label>
            <div class="space-y-2 max-h-64 overflow-y-auto">
              <TransitionGroup name="list">
                <div
                  v-for="check in multiCheckModal.checks"
                  :key="check.id"
                  class="flex items-center justify-between px-3 py-2 bg-gray-50 rounded-lg"
                >
                <div class="flex items-center gap-3">
                  <span class="text-green-500 text-lg">✓</span>
                  <div>
                    <div class="text-sm font-medium text-gray-900">
                      {{ check.time ? formatTime(check.time) : 'No time' }}
                    </div>
                    <div v-if="check.note" class="text-xs text-gray-600 mt-0.5">{{ check.note }}</div>
                  </div>
                </div>
                <button
                  @click="deleteMultiCheck(check.id)"
                  class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded transition-colors"
                  title="Delete check"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                  </svg>
                </button>
              </div>
              </TransitionGroup>
            </div>
          </div>

          <div v-else class="text-center py-8 text-gray-500 text-sm">
            No checks yet for this day
          </div>
        </div>

        <div class="px-6 py-4 border-t border-gray-200">
          <button
            @click="closeMultiCheckModal"
            class="w-full px-4 py-2 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors"
          >
            Close
          </button>
        </div>
        </div>
        </Transition>
      </div>
    </Transition>

    <!-- Emoji Picker Popover -->
    <div
      v-if="showEmojiPicker"
      class="fixed z-50"
      :style="{ top: emojiPickerPosition.top + 'px', left: emojiPickerPosition.left + 'px' }"
    >
      <div class="bg-white rounded-lg shadow-xl border border-gray-200" ref="emojiPickerRef">
        <!-- Emoji picker element will be mounted here -->
      </div>
    </div>

    <!-- Click outside to close emoji picker -->
    <div
      v-if="showEmojiPicker"
      class="fixed inset-0 z-40"
      @click="showEmojiPicker = false"
    ></div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch, nextTick } from 'vue';
import axios from 'axios';
import AppNav from '../Components/AppNav.vue';
import HabitCard from '../Components/habits/HabitCard.vue';
import LoadingSpinner from '../Components/ui/LoadingSpinner.vue';
import EmptyState from '../Components/ui/EmptyState.vue';
import { formatStartDate, formatDayName, formatDateFull, formatTooltipDate, formatTime, isToday, isFutureDate } from '../utils/dates.js';
import 'emoji-picker-element';

const loading = ref(true);
const habits = ref([]);
const checks = ref({});
const checkNotes = ref({});
const habitStats = ref({});
const showArchived = ref(false);
const weekOffsets = ref({}); // Per-habit week offsets: { habitId: offset }

const showCreateModal = ref(false);
const editingHabit = ref(null);

const habitForm = ref({
  name: '',
  emoji: '✅',
  color: '#10B981',
  description: '',
  allow_multiple_checks: false,
});

const noteModal = ref({
  show: false,
  habitId: null,
  date: null,
  note: '',
});

const tooltip = ref({
  show: false,
  habitId: null,
  date: null,
  x: 0,
  y: 0,
  notes: [],
  loading: false,
});

const multiCheckModal = ref({
  show: false,
  habitId: null,
  habit: null,
  date: null,
  checks: [],
  newCheckTime: '',
  newCheckNote: '',
});

const animatingChecks = ref({});
const animatingStreaks = ref({});

// Emoji Picker
const showEmojiPicker = ref(false);
const emojiPickerPosition = ref({ top: 0, left: 0 });
const emojiPickerRef = ref(null);
let pickerInstance = null;

const habitColors = ['#10B981', '#3B82F6', '#8B5CF6', '#F59E0B', '#EF4444', '#EC4899', '#06B6D4', '#84CC16'];

const activeHabits = computed(() => habits.value.filter(h => h.status === 'active'));
const archivedHabits = computed(() => habits.value.filter(h => h.status === 'archived'));

// Fetch all habits and checks
const fetchHabits = async () => {
  try {
    loading.value = true;

    // Fetch active habits
    const [habitsRes, archivedRes, checksRes] = await Promise.all([
      axios.get('/api/habits'),
      axios.get('/api/habits?status=archived'),
      axios.get('/api/habit-checks'),
    ]);

    habits.value = [...habitsRes.data, ...archivedRes.data];

    // Process checks
    checks.value = {};
    checkNotes.value = {};

    // Group checks by habit-date for counting
    const checksByHabitDate = {};
    checksRes.data.forEach(check => {
      const key = `${check.habit_id}-${check.date}`;
      if (!checksByHabitDate[key]) {
        checksByHabitDate[key] = [];
      }
      checksByHabitDate[key].push(check);
    });

    // Store checks - for multi-check habits, day is checked if ANY check exists
    Object.entries(checksByHabitDate).forEach(([key, checkList]) => {
      // A day is "checked" if at least one check is marked as checked
      checks.value[key] = checkList.some(check => check.checked);

      // Store note from first check (or combine them?)
      const notedCheck = checkList.find(c => c.note);
      if (notedCheck) {
        checkNotes.value[key] = notedCheck.note;
      }

      // For multi-check habits, store the count
      if (checkList.length > 1) {
        checks.value[`${key}-count`] = checkList.length;
      }
    });

    // Fetch stats for each habit
    await Promise.all(
      habits.value.map(habit => fetchHabitStats(habit.id))
    );
  } catch (error) {
    console.error('Error fetching habits:', error);
  } finally {
    loading.value = false;
  }
};

const fetchHabitStats = async (habitId) => {
  try {
    const response = await axios.get(`/api/habits/${habitId}/stats`);
    habitStats.value[habitId] = response.data;
  } catch (error) {
    console.error('Error fetching habit stats:', error);
  }
};

// Generate full year grid (365 days, GitHub-style)
const getFullYearGrid = () => {
  const weeks = [];
  const today = new Date();
  const startDate = new Date(today);
  startDate.setDate(startDate.getDate() - 364);

  // Adjust to previous Sunday
  const dayOfWeek = startDate.getDay();
  startDate.setDate(startDate.getDate() - dayOfWeek);

  let currentDate = new Date(startDate);

  while (currentDate <= today) {
    const week = { start: currentDate.toISOString().split('T')[0], days: [] };

    for (let i = 0; i < 7; i++) {
      const dateStr = currentDate.toISOString().split('T')[0];
      const oneYearAgo = new Date(today);
      oneYearAgo.setDate(oneYearAgo.getDate() - 364);

      if (currentDate >= oneYearAgo && currentDate <= today) {
        week.days.push({ date: dateStr, index: i });
      } else {
        week.days.push({ date: null, index: i });
      }

      currentDate.setDate(currentDate.getDate() + 1);
    }

    weeks.push(week);
  }

  return weeks;
};

// Helper to get week offset for a habit
const getWeekOffset = (habitId) => {
  if (!weekOffsets.value[habitId]) {
    weekOffsets.value[habitId] = 0;
  }
  return weekOffsets.value[habitId];
};

// Generate 7 days for the current week view based on offset
const getCurrentWeekDays = (habitId) => {
  const dates = [];
  const today = new Date();
  const offset = getWeekOffset(habitId);

  // Calculate start of the week we want to show (offset weeks ago)
  const startDate = new Date(today);
  startDate.setDate(startDate.getDate() + (offset * 7));

  // Start from Monday of that week
  const dayOfWeek = startDate.getDay();
  const daysFromMonday = dayOfWeek === 0 ? 6 : dayOfWeek - 1;
  startDate.setDate(startDate.getDate() - daysFromMonday);

  // Get 7 days (Monday-Sunday)
  for (let i = 0; i < 7; i++) {
    const date = new Date(startDate);
    date.setDate(date.getDate() + i);
    dates.push(date.toISOString().split('T')[0]);
  }

  return dates;
};

// Get the date range for the current week view
const getCurrentWeekRange = (habitId) => {
  const dates = getCurrentWeekDays(habitId);
  if (dates.length === 0) return '';

  const start = new Date(dates[0] + 'T00:00:00');
  const end = new Date(dates[dates.length - 1] + 'T00:00:00');

  const startMonth = start.toLocaleDateString('en-US', { month: 'short' });
  const endMonth = end.toLocaleDateString('en-US', { month: 'short' });
  const startDay = start.getDate();
  const endDay = end.getDate();
  const year = end.getFullYear();

  if (startMonth === endMonth) {
    return `${startMonth} ${startDay}-${endDay}, ${year}`;
  } else {
    return `${startMonth} ${startDay} - ${endMonth} ${endDay}, ${year}`;
  }
};

// Navigation functions
const goToPreviousWeek = (habitId) => {
  if (!weekOffsets.value[habitId]) {
    weekOffsets.value[habitId] = 0;
  }
  weekOffsets.value[habitId]--;
};

const goToNextWeek = (habitId) => {
  if (!weekOffsets.value[habitId]) {
    weekOffsets.value[habitId] = 0;
  }
  if (weekOffsets.value[habitId] < 0) {
    weekOffsets.value[habitId]++;
  }
};

const goToThisWeek = (habitId) => {
  weekOffsets.value[habitId] = 0;
};

// Tooltip functions
const showTooltip = async (event, habitId, date) => {
  const rect = event.target.getBoundingClientRect();
  const habit = habits.value.find(h => h.id === habitId);

  tooltip.value = {
    show: true,
    habitId,
    date,
    x: rect.left + rect.width / 2,
    y: rect.top,
    notes: [],
    loading: false,
  };

  // For multi-check habits, fetch all notes
  if (habit?.allow_multiple_checks && isChecked(habitId, date)) {
    tooltip.value.loading = true;
    try {
      const response = await axios.get(`/api/habits/${habitId}/checks/${date}`);
      tooltip.value.notes = response.data
        .filter(check => check.note)
        .map(check => check.note);
      tooltip.value.loading = false;
    } catch (error) {
      console.error('Error fetching tooltip notes:', error);
      tooltip.value.loading = false;
    }
  } else {
    // For single-check habits, use the existing note
    const note = getNote(habitId, date);
    if (note) {
      tooltip.value.notes = [note];
    }
  }
};

const hideTooltip = () => {
  tooltip.value = { show: false, habitId: null, date: null, x: 0, y: 0, notes: [], loading: false };
};

// Generate month labels for the grid
const getMonthLabels = () => {
  const weeks = getFullYearGrid();
  const labels = [];
  let currentMonth = null;
  let currentWidth = 0;

  weeks.forEach((week, index) => {
    const firstDay = week.days.find(d => d.date);
    if (firstDay) {
      const date = new Date(firstDay.date + 'T00:00:00');
      const month = date.toLocaleDateString('en-US', { month: 'short' });

      if (month !== currentMonth) {
        if (currentMonth !== null && currentWidth > 0) {
          labels.push({
            name: currentMonth,
            width: `${currentWidth * 13}px`, // 12px square + 2px gap
          });
        }
        currentMonth = month;
        currentWidth = 1;
      } else {
        currentWidth++;
      }
    }
  });

  // Add the last month
  if (currentMonth !== null && currentWidth > 0) {
    labels.push({
      name: currentMonth,
      width: `${currentWidth * 13}px`,
    });
  }

  return labels;
};

const isChecked = (habitId, date) => {
  const key = `${habitId}-${date}`;
  return checks.value[key] === true;
};

const hasNote = (habitId, date) => {
  const key = `${habitId}-${date}`;
  return !!checkNotes.value[key];
};

const getNote = (habitId, date) => {
  const key = `${habitId}-${date}`;
  return checkNotes.value[key] || '';
};

const getCheckCount = (habitId, date) => {
  // For multi-check habits, get the stored count
  const key = `${habitId}-${date}`;
  const countKey = `${key}-count`;
  return checks.value[countKey] || (checks.value[key] ? 1 : 0);
};

const getTooltipHabit = (habitId) => {
  return habits.value.find(h => h.id === habitId);
};

const openMultiCheckModal = async (habit, date) => {
  try {
    // Fetch all checks for this habit and date
    const response = await axios.get(`/api/habits/${habit.id}/checks/${date}`);

    multiCheckModal.value = {
      show: true,
      habitId: habit.id,
      habit: habit,
      date: date,
      checks: response.data,
      newCheckTime: new Date().toTimeString().slice(0, 5), // Current time HH:MM
    };
  } catch (error) {
    console.error('Error fetching checks:', error);
  }
};

const closeMultiCheckModal = () => {
  multiCheckModal.value = {
    show: false,
    habitId: null,
    habit: null,
    date: null,
    checks: [],
    newCheckTime: '',
    newCheckNote: '',
  };
};

const addMultiCheck = async () => {
  if (!multiCheckModal.value.newCheckTime) return;

  try {
    await axios.post(`/api/habits/${multiCheckModal.value.habitId}/check`, {
      date: multiCheckModal.value.date,
      time: multiCheckModal.value.newCheckTime,
      note: multiCheckModal.value.newCheckNote || null,
    });

    // Refresh the checks for this modal
    const response = await axios.get(`/api/habits/${multiCheckModal.value.habitId}/checks/${multiCheckModal.value.date}`);
    multiCheckModal.value.checks = response.data;

    // Clear the input fields
    multiCheckModal.value.newCheckTime = new Date().toTimeString().slice(0, 5);
    multiCheckModal.value.newCheckNote = '';

    // Update the checks state to reflect the new check
    await fetchHabits();
  } catch (error) {
    console.error('Error adding check:', error);
  }
};

const deleteMultiCheck = async (checkId) => {
  if (!confirm('Delete this check?')) return;

  try {
    await axios.delete(`/api/habit-checks/${checkId}`);

    // Remove from modal
    multiCheckModal.value.checks = multiCheckModal.value.checks.filter(c => c.id !== checkId);

    // Update the checks state
    await fetchHabits();
  } catch (error) {
    console.error('Error deleting check:', error);
  }
};

const getSquareClass = (habit, day) => {
  if (!day.date) return 'opacity-0';
  return 'hover:ring-1 hover:ring-gray-600 hover:ring-offset-1';
};

const getSquareStyle = (habit, day) => {
  if (!day.date) return { backgroundColor: 'transparent' };

  const color = habit.color || '#10B981';
  const checked = isChecked(habit.id, day.date);

  // For multi-check habits, show intensity based on count
  if (habit.allow_multiple_checks && checked) {
    const count = getCheckCount(habit.id, day.date);
    // Scale opacity from 0.4 to 1.0 based on count (1-5+ checks)
    const intensity = Math.min(0.4 + (count * 0.15), 1.0);
    return { backgroundColor: color, opacity: intensity };
  }

  if (checked) {
    return { backgroundColor: color };
  }

  return { backgroundColor: color + '20' };
};

const getSquareTitle = (habit, date) => {
  const checked = isChecked(habit.id, date);
  const note = hasNote(habit.id, date);
  const key = `${habit.id}-${date}`;
  const noteText = checkNotes.value[key];

  let tooltip = `${formatDateFull(date)}\n`;
  tooltip += checked ? '✓ Done' : '○ Not done';

  if (note && noteText) {
    tooltip += `\n📝 Note: ${noteText}`;
  }

  return tooltip;
};

const toggleCheck = async (habitId, date) => {
  try {
    const key = `${habitId}-${date}`;
    const wasChecked = checks.value[key];

    // Trigger animation
    animatingChecks.value[key] = true;
    setTimeout(() => {
      animatingChecks.value[key] = false;
    }, 400);

    const response = await axios.post(`/api/habits/${habitId}/check`, { date });
    checks.value[key] = response.data.checked;

    // If newly checked, trigger success animation
    if (!wasChecked && response.data.checked) {
      // Animate streak update
      const oldStats = habitStats.value[habitId];
      await fetchHabitStats(habitId);
      const newStats = habitStats.value[habitId];

      if (newStats && oldStats && newStats.current_streak > oldStats.current_streak) {
        animatingStreaks.value[habitId] = true;
        setTimeout(() => {
          animatingStreaks.value[habitId] = false;
        }, 500);
      }
    } else {
      // Just refresh stats
      await fetchHabitStats(habitId);
    }
  } catch (error) {
    console.error('Error toggling check:', error);
  }
};

const openNoteModal = (habitId, date) => {
  const key = `${habitId}-${date}`;
  noteModal.value = {
    show: true,
    habitId,
    date,
    note: checkNotes.value[key] || '',
  };
};

const closeNoteModal = () => {
  noteModal.value = { show: false, habitId: null, date: null, note: '' };
};

const saveNote = async () => {
  try {
    if (!noteModal.value.note.trim()) {
      closeNoteModal();
      return;
    }

    await axios.put(
      `/api/habits/${noteModal.value.habitId}/checks/${noteModal.value.date}/note`,
      { note: noteModal.value.note }
    );

    const key = `${noteModal.value.habitId}-${noteModal.value.date}`;
    checkNotes.value[key] = noteModal.value.note;

    closeNoteModal();
  } catch (error) {
    console.error('Error saving note:', error);
  }
};

const openEditModal = (habit) => {
  editingHabit.value = habit;
  habitForm.value = {
    name: habit.name,
    emoji: habit.emoji || '✅',
    color: habit.color || '#10B981',
    description: habit.description || '',
    allow_multiple_checks: habit.allow_multiple_checks || false,
  };
};

const closeModal = () => {
  showCreateModal.value = false;
  editingHabit.value = null;
  habitForm.value = {
    name: '',
    emoji: '✅',
    color: '#10B981',
    description: '',
    allow_multiple_checks: false,
  };
};

// Emoji Picker Functions
const openEmojiPicker = (event) => {
  const button = event.currentTarget;
  const rect = button.getBoundingClientRect();

  // Emoji picker dimensions
  const pickerWidth = 352;  // approximate width
  const pickerHeight = 435; // approximate height
  const margin = 10;        // minimum margin from screen edge

  // Calculate vertical position (prefer below, but show above if not enough space)
  let top;
  const spaceBelow = window.innerHeight - rect.bottom;
  const spaceAbove = rect.top;

  if (spaceBelow >= pickerHeight + margin) {
    // Enough space below - position below the button
    top = rect.bottom + window.scrollY + 5;
  } else if (spaceAbove >= pickerHeight + margin) {
    // Not enough space below but enough above - position above the button
    top = rect.top + window.scrollY - pickerHeight - 5;
  } else {
    // Not enough space either way - position at bottom with margin, or top with margin
    if (spaceBelow > spaceAbove) {
      top = window.innerHeight + window.scrollY - pickerHeight - margin;
    } else {
      top = window.scrollY + margin;
    }
  }

  // Calculate horizontal position
  let left = rect.left + window.scrollX;

  // Adjust if too close to right edge
  if (left + pickerWidth + margin > window.innerWidth) {
    left = window.innerWidth - pickerWidth - margin;
  }

  // Adjust if too close to left edge
  if (left < margin) {
    left = margin;
  }

  emojiPickerPosition.value = { top, left };
  showEmojiPicker.value = true;
};

const insertEmoji = (emojiData) => {
  const emoji = emojiData.unicode || emojiData.emoji || emojiData;
  habitForm.value.emoji = emoji;
  showEmojiPicker.value = false;
};

// Watch for emoji picker visibility to mount/unmount the picker element
watch(showEmojiPicker, (isVisible) => {
  if (isVisible) {
    nextTick(() => {
      if (emojiPickerRef.value && !pickerInstance) {
        pickerInstance = document.createElement('emoji-picker');
        pickerInstance.addEventListener('emoji-click', (event) => {
          insertEmoji(event.detail);
        });
        emojiPickerRef.value.appendChild(pickerInstance);
      }
    });
  } else {
    if (pickerInstance && emojiPickerRef.value) {
      emojiPickerRef.value.removeChild(pickerInstance);
      pickerInstance = null;
    }
  }
});

const saveHabit = async () => {
  if (!habitForm.value.name) return;

  try {
    if (editingHabit.value) {
      await axios.put(`/api/habits/${editingHabit.value.id}`, {
        ...habitForm.value,
        frequency: 'daily', // Default frequency for backend
      });
    } else {
      await axios.post('/api/habits', {
        ...habitForm.value,
        frequency: 'daily', // Default frequency for backend
        start_date: new Date().toISOString().split('T')[0],
      });
    }

    await fetchHabits();
    closeModal();
  } catch (error) {
    console.error('Error saving habit:', error);
  }
};

const confirmArchive = async (habitId) => {
  if (!confirm('Archive this habit? You can reactivate it later.')) return;

  try {
    await axios.patch(`/api/habits/${habitId}/archive`);
    await fetchHabits();
  } catch (error) {
    console.error('Error archiving habit:', error);
  }
};

const reactivateHabit = async (habitId) => {
  try {
    await axios.patch(`/api/habits/${habitId}/reactivate`);
    await fetchHabits();
  } catch (error) {
    console.error('Error reactivating habit:', error);
  }
};

const confirmDelete = async (habitId) => {
  if (!confirm('Permanently delete this habit and all its data? This cannot be undone.')) return;

  try {
    await axios.delete(`/api/habits/${habitId}`);
    await fetchHabits();
  } catch (error) {
    console.error('Error deleting habit:', error);
  }
};

// Local formatting function - has custom logic for habitId
const formatDateWithMonth = (dateStr, habitId = null) => {
  const date = new Date(dateStr + 'T00:00:00');
  const day = date.getDate();
  const month = date.toLocaleDateString('en-US', { month: 'short' });

  // Show month on the 1st of the month or if it's the first day in the week view
  if (habitId) {
    const weekDays = getCurrentWeekDays(habitId);
    const isFirstInWeek = weekDays[0] === dateStr;

    if (day === 1 || isFirstInWeek) {
      return `${month} ${day}`;
    }
  } else if (day === 1) {
    return `${month} ${day}`;
  }

  return day.toString();
};

onMounted(() => {
  fetchHabits();
});
</script>

<style scoped>
/* Check/Uncheck Animations */
@keyframes checkBounce {
  0%, 100% { transform: scale(1); }
  50% { transform: scale(1.2); }
}

@keyframes checkSuccess {
  0% { transform: scale(0.8); opacity: 0; }
  50% { transform: scale(1.1); }
  100% { transform: scale(1); opacity: 1; }
}

.check-animate {
  animation: checkBounce 0.3s ease;
}

.check-success {
  animation: checkSuccess 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
}

/* Modal Animations */
.modal-enter-active {
  transition: all 0.3s ease;
}

.modal-leave-active {
  transition: all 0.2s ease;
}

.modal-enter-from {
  opacity: 0;
}

.modal-leave-to {
  opacity: 0;
}

.modal-content-enter-active {
  transition: all 0.3s ease;
}

.modal-content-leave-active {
  transition: all 0.2s ease;
}

.modal-content-enter-from {
  transform: translateY(50px) scale(0.95);
  opacity: 0;
}

.modal-content-leave-to {
  transform: translateY(30px) scale(0.98);
  opacity: 0;
}

/* Week Slide Transitions */
.week-slide-enter-active,
.week-slide-leave-active {
  transition: all 0.3s ease;
}

.week-slide-enter-from {
  transform: translateX(100%);
  opacity: 0;
}

.week-slide-leave-to {
  transform: translateX(-100%);
  opacity: 0;
}

/* Habit Card Load Animation */
@keyframes fadeSlideUp {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.habit-card-enter {
  animation: fadeSlideUp 0.5s ease forwards;
}

/* GitHub Grid Animations */
.github-square {
  transition: transform 0.15s ease, box-shadow 0.15s ease;
}

.github-square:hover {
  transform: scale(1.15);
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
}

@keyframes pulse {
  0%, 100% { transform: scale(1); }
  50% { transform: scale(1.2); }
}

.pulse-animate {
  animation: pulse 0.5s ease;
}

/* Streak Counter Animation */
@keyframes countUp {
  0% { opacity: 0; transform: translateY(-10px); }
  100% { opacity: 1; transform: translateY(0); }
}

@keyframes flameWiggle {
  0%, 100% { transform: rotate(0deg); }
  25% { transform: rotate(-10deg); }
  75% { transform: rotate(10deg); }
}

.count-up {
  animation: countUp 0.4s ease;
}

.flame-wiggle {
  animation: flameWiggle 0.5s ease;
}

/* Number Morph Animation */
@keyframes numberMorph {
  0% { transform: scale(0.5) rotate(-10deg); opacity: 0; }
  60% { transform: scale(1.1) rotate(5deg); }
  100% { transform: scale(1) rotate(0deg); opacity: 1; }
}

.number-morph {
  animation: numberMorph 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
}

/* Tooltip Animation */
.tooltip-enter-active,
.tooltip-leave-active {
  transition: all 0.2s ease;
}

.tooltip-enter-from,
.tooltip-leave-to {
  opacity: 0;
  transform: translateY(5px) translateX(-50%);
}

/* List Animations */
.list-enter-active,
.list-leave-active {
  transition: all 0.3s ease;
}

.list-enter-from {
  opacity: 0;
  transform: translateX(-30px);
}

.list-leave-to {
  opacity: 0;
  transform: translateX(30px);
}

/* Smooth transitions */
.smooth-transition {
  transition: all 0.2s ease;
}
</style>
