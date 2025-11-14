<template>
  <div
    class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow habit-card-enter"
    :style="{ animationDelay: `${index * 0.05}s` }"
  >
    <!-- Habit Header -->
    <div class="px-3 sm:px-4 py-3 sm:py-2.5 border-b border-gray-100">
      <div class="flex items-center justify-between gap-2">
        <div class="flex items-center gap-2 sm:gap-2.5 min-w-0 flex-1">
          <span class="text-xl flex-shrink-0">{{ habit.emoji || '📌' }}</span>
          <div class="min-w-0 flex-1">
            <h3 class="text-sm font-semibold text-gray-900">{{ habit.name }}</h3>
            <p v-if="habit.description" class="text-[11px] text-gray-600 mt-0.5">{{ habit.description }}</p>
            <div class="flex items-center flex-wrap gap-x-2 sm:gap-x-3 gap-y-1 mt-1 sm:mt-0.5 text-[11px] text-gray-500">
              <span class="whitespace-nowrap">Started {{ formatStartDate(habit.start_date) }}</span>
              <span v-if="stats" class="flex items-center gap-0.5 whitespace-nowrap">
                <span class="text-orange-500" :class="animatingStreaks[habit.id] ? 'flame-wiggle' : ''">🔥</span>
                <span class="font-semibold text-gray-900" :class="animatingStreaks[habit.id] ? 'count-up' : ''">{{ stats.current_streak }}</span>
                <span>day streak</span>
              </span>
              <span v-if="stats" class="font-medium text-gray-700 whitespace-nowrap">
                {{ stats.total_completions }} days completed
              </span>
            </div>
          </div>
        </div>
        <div class="flex items-center gap-1 sm:gap-1.5 flex-shrink-0">
          <button
            @click="$emit('edit', habit)"
            class="p-1.5 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors"
            title="Edit"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
            </svg>
          </button>
          <button
            @click="$emit('archive', habit.id)"
            class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors"
            title="Archive"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
            </svg>
          </button>
        </div>
      </div>
    </div>

    <!-- GitHub-style Progress Grid (Read-only) -->
    <HabitYearGrid
      :habit="habit"
      :show-tooltip="true"
      @show-tooltip="(event, day) => $emit('show-tooltip', { event, habitId: habit.id, date: day.date })"
      @hide-tooltip="$emit('hide-tooltip')"
      @change-year="(year) => $emit('change-year', year)"
    />

    <!-- Calendar Checkboxes (Navigable Week) -->
    <div class="px-2 sm:px-4 py-3 border-t border-gray-100">
      <!-- Navigation Header -->
      <div class="flex items-center justify-between mb-3 gap-2">
        <button
          @click="$emit('previous-week', habit.id)"
          class="px-2 sm:px-3 py-1.5 text-xs font-medium text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors flex items-center gap-1"
        >
          <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
          </svg>
          <span class="hidden sm:inline">Previous</span>
        </button>

        <div class="text-xs font-semibold text-gray-900 text-center flex-1">
          {{ weekRange }}
        </div>

        <div class="flex items-center gap-1 sm:gap-2">
          <button
            v-if="weekOffset !== 0"
            @click="$emit('this-week', habit.id)"
            class="px-2 sm:px-3 py-1.5 text-xs font-medium text-blue-600 hover:text-blue-700 hover:bg-blue-50 rounded-lg transition-colors hidden sm:block"
          >
            This Week
          </button>
          <button
            @click="$emit('next-week', habit.id)"
            :disabled="weekOffset >= 0"
            class="px-2 sm:px-3 py-1.5 text-xs font-medium text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors flex items-center gap-1 disabled:opacity-30 disabled:cursor-not-allowed"
          >
            <span class="hidden sm:inline">Next</span>
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
          </button>
        </div>
      </div>

      <!-- Week Calendar -->
      <div class="grid grid-cols-7 gap-1 sm:gap-2">
        <div
          v-for="date in weekDays"
          :key="date"
          class="flex flex-col items-center"
        >
          <!-- Day Name -->
          <div class="text-[10px] font-medium text-gray-500 mb-1">
            {{ formatDayName(date) }}
          </div>

          <!-- Checkbox (single check) or Counter (multiple checks) -->
          <button
            v-if="!habit.allow_multiple_checks"
            @click="!isFutureDate(date) && $emit('toggle-check', { habitId: habit.id, date })"
            @contextmenu.prevent="!isFutureDate(date) && $emit('open-note', { habitId: habit.id, date })"
            @touchstart="handleTouchStart(habit.id, date, $event)"
            @touchend="handleTouchEnd"
            @touchmove="handleTouchMove"
            @mouseenter="!isFutureDate(date) && $emit('show-tooltip', { event: $event, habitId: habit.id, date })"
            @mouseleave="$emit('hide-tooltip')"
            :disabled="isFutureDate(date)"
            class="relative w-8 h-8 sm:w-10 sm:h-10 rounded-lg border-2 transition-all"
            :class="[
              isFutureDate(date)
                ? 'bg-gray-100 border-gray-200 cursor-not-allowed opacity-50'
                : isChecked(habit.id, date)
                ? 'bg-green-500 border-green-500 shadow-sm hover:scale-105'
                : isToday(date)
                ? 'bg-yellow-50 border-yellow-400 hover:border-green-400 ring-2 ring-yellow-200 hover:scale-105'
                : 'bg-white border-gray-200 hover:border-gray-400 hover:scale-105',
              animatingChecks[`${habit.id}-${date}`] && isChecked(habit.id, date) ? 'check-success' : '',
              animatingChecks[`${habit.id}-${date}`] && !isChecked(habit.id, date) ? 'check-animate' : ''
            ]"
            :title="isFutureDate(date) ? 'Cannot check future dates' : formatDateFull(date)"
          >
            <span
              v-if="isChecked(habit.id, date)"
              class="text-white text-sm font-bold"
            >✓</span>
            <div
              v-if="hasNote(habit.id, date)"
              class="absolute -top-1 -right-1 w-2 h-2 bg-blue-500 rounded-full ring-2 ring-white"
            ></div>
          </button>

          <!-- Multi-check button -->
          <button
            v-else
            @click="!isFutureDate(date) && $emit('open-multi-check', { habit, date })"
            @contextmenu.prevent="!isFutureDate(date) && $emit('open-note', { habitId: habit.id, date })"
            @touchstart="handleTouchStart(habit.id, date, $event)"
            @touchend="handleTouchEnd"
            @touchmove="handleTouchMove"
            @mouseenter="!isFutureDate(date) && $emit('show-tooltip', { event: $event, habitId: habit.id, date })"
            @mouseleave="$emit('hide-tooltip')"
            :disabled="isFutureDate(date)"
            class="relative w-8 h-8 sm:w-10 sm:h-10 rounded-lg border-2 transition-all flex items-center justify-center"
            :class="[
              isFutureDate(date)
                ? 'bg-gray-100 border-gray-200 cursor-not-allowed opacity-50'
                : getCheckCount(habit.id, date) > 0
                ? 'bg-green-500 border-green-500 shadow-sm text-white hover:scale-105'
                : isToday(date)
                ? 'bg-yellow-50 border-yellow-400 hover:border-green-400 ring-2 ring-yellow-200 hover:scale-105'
                : 'bg-white border-gray-200 hover:border-gray-400 hover:scale-105'
            ]"
            :title="isFutureDate(date) ? 'Cannot check future dates' : `${getCheckCount(habit.id, date)} check(s) on ${formatDateFull(date)}`"
          >
            <span class="text-sm font-bold" :class="getCheckCount(habit.id, date) > 0 ? 'number-morph' : ''">
              {{ getCheckCount(habit.id, date) || '+' }}
            </span>
            <div
              v-if="hasNote(habit.id, date)"
              class="absolute -top-1 -right-1 w-2 h-2 bg-blue-500 rounded-full ring-2 ring-white"
            ></div>
          </button>

          <!-- Date with Month -->
          <div class="text-[11px] font-medium mt-1.5" :class="isToday(date) ? 'text-yellow-600' : 'text-gray-700'">
            {{ formatDateWithMonth(date, habit.id) }}
          </div>
        </div>
      </div>

      <p class="mt-3 text-[10px] text-gray-500 text-center hidden sm:block">
        <span v-if="!habit.allow_multiple_checks">💡 Click to check/uncheck • Right-click to add note • Blue dot = has note</span>
        <span v-else>💡 Click to view/add checks • Right-click to add note • Blue dot = has note • Numbers show check count</span>
      </p>
      <p class="mt-3 text-[10px] text-gray-500 text-center sm:hidden">
        <span v-if="!habit.allow_multiple_checks">💡 Tap to check/uncheck • Hold to add note • Blue dot = has note</span>
        <span v-else>💡 Tap to view/add checks • Hold to add note • Blue dot = has note</span>
      </p>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import HabitYearGrid from './HabitYearGrid.vue';

const props = defineProps({
  habit: {
    type: Object,
    required: true,
  },
  index: {
    type: Number,
    default: 0,
  },
  stats: {
    type: Object,
    default: null,
  },
  animatingStreaks: {
    type: Object,
    default: () => ({}),
  },
  animatingChecks: {
    type: Object,
    default: () => ({}),
  },
  weekRange: {
    type: String,
    required: true,
  },
  weekOffset: {
    type: Number,
    required: true,
  },
  weekDays: {
    type: Array,
    required: true,
  },
  // Helper functions passed as props
  formatStartDate: {
    type: Function,
    required: true,
  },
  formatDayName: {
    type: Function,
    required: true,
  },
  isFutureDate: {
    type: Function,
    required: true,
  },
  isChecked: {
    type: Function,
    required: true,
  },
  isToday: {
    type: Function,
    required: true,
  },
  formatDateFull: {
    type: Function,
    required: true,
  },
  hasNote: {
    type: Function,
    required: true,
  },
  getCheckCount: {
    type: Function,
    required: true,
  },
  formatDateWithMonth: {
    type: Function,
    required: true,
  },
});

const emit = defineEmits([
  'edit',
  'archive',
  'show-tooltip',
  'hide-tooltip',
  'previous-week',
  'this-week',
  'next-week',
  'toggle-check',
  'open-note',
  'open-multi-check',
]);

// Long-press support for mobile
const longPressTimer = ref(null);
const longPressActive = ref(false);

const handleTouchStart = (habitId, date, event) => {
  if (props.isFutureDate(date)) return;

  longPressActive.value = false;
  longPressTimer.value = setTimeout(() => {
    longPressActive.value = true;
    // Trigger haptic feedback if available
    if (navigator.vibrate) {
      navigator.vibrate(50);
    }
    emit('open-note', { habitId, date });
  }, 500); // 500ms hold to trigger note
};

const handleTouchEnd = (event) => {
  if (longPressTimer.value) {
    clearTimeout(longPressTimer.value);
    longPressTimer.value = null;
  }

  // If it was a long press, prevent the click event
  if (longPressActive.value) {
    event.preventDefault();
    longPressActive.value = false;
  }
};

const handleTouchMove = () => {
  // Cancel long press if finger moves
  if (longPressTimer.value) {
    clearTimeout(longPressTimer.value);
    longPressTimer.value = null;
  }
};
</script>
