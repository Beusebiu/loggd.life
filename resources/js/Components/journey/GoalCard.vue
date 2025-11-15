<template>
  <div
    class="bg-white dark:bg-gray-800 border-2 border-gray-200 dark:border-gray-700 rounded-xl p-4 hover:border-green-300 dark:hover:border-green-700 transition cursor-pointer"
    @click="$emit('click', goal)"
  >
    <!-- Header -->
    <div class="flex justify-between items-start mb-3">
      <div class="flex-1">
        <h3 class="text-base font-bold text-gray-900 dark:text-white">{{ goal.title }}</h3>
        <p class="text-xs text-gray-600 dark:text-gray-400 mt-0.5" v-if="goal.description">{{ truncate(goal.description, 60) }}</p>
      </div>
      <div class="flex items-center gap-2 ml-2">
        <span v-if="goal.is_on_track" class="text-green-600 text-sm" title="On track">✓</span>
        <span v-else class="text-amber-600 text-sm" title="Needs attention">⚠</span>
        <span v-if="trendIcon" :class="trendClass" class="text-lg">{{ trendIcon }}</span>
      </div>
    </div>

    <!-- Metric Goal Display -->
    <div v-if="goal.tracking_type === 'metric'" class="space-y-2">
      <div class="grid grid-cols-3 gap-2 text-xs">
        <div>
          <div class="text-gray-500 dark:text-gray-400">Current</div>
          <div class="font-bold text-sm text-gray-900 dark:text-white">{{ formatMetric(goal.metric_current_value ?? goal.metric_start_value) }}</div>
        </div>
        <div>
          <div class="text-gray-500 dark:text-gray-400">Target</div>
          <div class="font-bold text-sm text-gray-900 dark:text-white">{{ formatMetric(goal.metric_target_value) }}</div>
        </div>
        <div>
          <div class="text-gray-500 dark:text-gray-400">Progress</div>
          <div class="font-bold text-sm text-green-600 dark:text-green-400">{{ goal.metric_progress_percentage }}%</div>
        </div>
      </div>

      <!-- Mini progress bar -->
      <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-1.5">
        <div
          class="bg-green-600 dark:bg-green-500 h-1.5 rounded-full transition-all"
          :style="{ width: goal.metric_progress_percentage + '%' }"
        ></div>
      </div>

      <!-- Quick update button -->
      <button
        @click.stop="$emit('update-metric', goal)"
        class="text-xs text-green-600 dark:text-green-400 hover:text-green-700 dark:hover:text-green-300 font-medium"
      >
        Update Value
      </button>
    </div>

    <!-- Milestone Goal Display -->
    <div v-else-if="goal.tracking_type === 'milestone'" class="space-y-2">
      <div class="text-xs">
        <div class="text-gray-500 dark:text-gray-400">Milestones</div>
        <div class="font-bold text-gray-900 dark:text-white">{{ completedMilestones }}/{{ totalMilestones }}</div>
      </div>

      <!-- Progress bar -->
      <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-1.5">
        <div
          class="bg-green-600 dark:bg-green-500 h-1.5 rounded-full transition-all"
          :style="{ width: goal.progress_percentage + '%' }"
        ></div>
      </div>

      <!-- Recent milestones preview -->
      <div v-if="recentMilestones.length > 0" class="space-y-1">
        <div
          v-for="milestone in recentMilestones"
          :key="milestone.id"
          class="flex items-center gap-1.5 text-xs"
        >
          <span v-if="milestone.completed" class="text-green-600 dark:text-green-400">✓</span>
          <span v-else class="text-gray-400 dark:text-gray-600">○</span>
          <span class="truncate" :class="milestone.completed ? 'text-gray-500 dark:text-gray-400 line-through' : 'text-gray-700 dark:text-gray-300'">
            {{ milestone.title }}
          </span>
        </div>
      </div>
    </div>

    <!-- Evolution/Journal Goal Display -->
    <div v-else-if="goal.tracking_type === 'evolution'" class="space-y-2">
      <div v-if="goal.last_update_note" class="bg-gray-50 dark:bg-gray-700/50 rounded p-2 text-xs text-gray-700 dark:text-gray-300">
        <div class="text-gray-500 dark:text-gray-400 mb-1">Latest update:</div>
        <p class="italic">"{{ truncate(goal.last_update_note, 80) }}"</p>
      </div>
      <div v-else class="text-xs text-gray-500 dark:text-gray-400 italic">
        No updates yet
      </div>

      <button
        @click.stop="$emit('add-update', goal)"
        class="text-xs text-green-600 dark:text-green-400 hover:text-green-700 dark:hover:text-green-300 font-medium"
      >
        Add Update
      </button>
    </div>

    <!-- Active/Ongoing Goal Display -->
    <div v-else-if="goal.tracking_type === 'active'" class="space-y-2">
      <div class="flex items-center gap-2 text-xs text-gray-600 dark:text-gray-400">
        <span class="inline-block w-2 h-2 bg-green-500 dark:bg-green-400 rounded-full animate-pulse"></span>
        <span>Ongoing goal - no end date</span>
      </div>

      <div v-if="goal.last_update_note" class="bg-gray-50 dark:bg-gray-700/50 rounded p-2 text-xs text-gray-700 dark:text-gray-300">
        {{ truncate(goal.last_update_note, 80) }}
      </div>
    </div>

    <!-- Footer -->
    <div class="flex justify-between items-center mt-3 pt-3 border-t border-gray-100 dark:border-gray-700">
      <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400">
        <span>{{ lifeAreaIcon(goal.life_area) }}</span>
        <span v-if="goal.target_date">{{ formatDate(goal.target_date) }}</span>
        <span
          :class="goal.is_public ? 'text-green-600 dark:text-green-400' : 'text-gray-400 dark:text-gray-600'"
          :title="goal.is_public ? 'Public - visible on your profile' : 'Private - only you can see'"
          class="text-sm"
        >
          {{ goal.is_public ? '🌍' : '🔒' }}
        </span>
        <span v-if="goal.parent" class="text-xs text-blue-600 dark:text-blue-400" title="Part of larger goal">
          ↳ {{ truncate(goal.parent.title, 20) }}
        </span>
        <span v-if="goal.children && goal.children.length > 0" class="text-xs text-purple-600 dark:text-purple-400" :title="`Has ${goal.children.length} sub-goal${goal.children.length > 1 ? 's' : ''}`">
          ⚬ {{ goal.children.length }}
        </span>
      </div>

      <span
        :class="statusColor"
        class="px-2 py-0.5 text-xs font-semibold rounded-full"
      >
        {{ goal.status }}
      </span>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { formatMetricValue, truncate } from '../../utils/formatters';
import { formatDate } from '../../utils/dateFormatters';

const props = defineProps({
  goal: {
    type: Object,
    required: true,
  },
});

defineEmits(['click', 'update-metric', 'add-update']);

const statusColor = computed(() => {
  const colors = {
    active: 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-400',
    completed: 'bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-400',
    paused: 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-400',
    abandoned: 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-400',
  };
  return colors[props.goal.status] || colors.active;
});

const trendIcon = computed(() => {
  if (props.goal.tracking_type !== 'metric') return null;

  const trend = props.goal.trend_indicator;
  if (trend === 'up') return '↗';
  if (trend === 'down') return '↘';
  return null;
});

const trendClass = computed(() => {
  const trend = props.goal.trend_indicator;
  if (trend === 'up') return 'text-green-600';
  if (trend === 'down') return 'text-red-600';
  return 'text-gray-400';
});

const completedMilestones = computed(() => {
  return props.goal.milestones?.filter(m => m.completed).length || 0;
});

const totalMilestones = computed(() => {
  return props.goal.milestones?.length || 0;
});

const recentMilestones = computed(() => {
  return (props.goal.milestones || []).slice(0, 3);
});

// Wrapper for formatMetric to include unit from goal
const formatMetric = (value) => {
  if (value === null || value === undefined) return '-';
  const unit = props.goal.metric_unit || '';
  return formatMetricValue(value, unit);
};

const lifeAreaIcon = (area) => {
  const icons = {
    career: '💼',
    health: '🏃',
    relationships: '❤️',
    financial: '💰',
    growth: '📚',
    impact: '🌍',
    other: '✨',
  };
  return icons[area] || icons.other;
};
</script>
