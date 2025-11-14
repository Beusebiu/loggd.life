<template>
  <div class="fixed inset-0 bg-gray-900/20 backdrop-blur-sm flex items-end sm:items-center justify-center z-50 sm:p-4" @click.self="$emit('close')">
    <div class="bg-white rounded-t-2xl sm:rounded-xl max-w-3xl w-full max-h-[90vh] sm:max-h-[85vh] flex flex-col">
      <!-- Header -->
      <div class="flex-shrink-0 bg-white border-b border-gray-200 px-4 sm:px-6 py-3 sm:py-4 flex justify-between items-start gap-2">
        <div class="flex-1 min-w-0">
          <input
            v-if="editing"
            v-model="form.title"
            class="text-lg sm:text-2xl font-bold text-gray-900 w-full border-b-2 border-green-500 focus:outline-none"
            placeholder="Goal title..."
          />
          <h2 v-else class="text-lg sm:text-2xl font-bold text-gray-900 truncate">{{ goal.title }}</h2>

          <div class="flex items-center flex-wrap gap-x-2 gap-y-1 mt-1.5 sm:mt-2 text-xs sm:text-sm">
            <span :class="statusBadgeClass">{{ goal.status }}</span>
            <span class="text-gray-500">•</span>
            <span>{{ trackingTypeLabel }}</span>
            <span class="text-gray-500">•</span>
            <span>{{ lifeAreaIcon }} {{ goal.life_area }}</span>
            <span class="text-gray-500">•</span>
            <span
              :class="goal.is_public ? 'text-green-600' : 'text-gray-500'"
              :title="goal.is_public ? 'Public - visible on your profile' : 'Private - only you can see'"
            >
              {{ goal.is_public ? '🌍 Public' : '🔒 Private' }}
            </span>
            <span v-if="goal.target_date" class="text-gray-500">• {{ formatDate(goal.target_date) }}</span>
          </div>
        </div>

        <div class="flex items-center gap-1.5 sm:gap-2 flex-shrink-0">
          <button
            v-if="!editing"
            @click="editing = true"
            class="px-2.5 sm:px-3 py-1.5 text-xs sm:text-sm border border-gray-300 rounded-lg hover:bg-gray-50"
          >
            Edit
          </button>
          <button
            v-else
            @click="saveGoal"
            :disabled="saving"
            class="px-3 sm:px-4 py-1.5 text-xs sm:text-sm bg-green-600 text-white rounded-lg hover:bg-green-700 disabled:opacity-50"
          >
            {{ saving ? 'Saving...' : 'Save' }}
          </button>
          <button
            @click="$emit('close')"
            class="text-gray-400 hover:text-gray-600"
          >
            <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
            </svg>
          </button>
        </div>
      </div>

      <!-- Content -->
      <div class="overflow-y-auto flex-1 p-4 sm:p-6 space-y-4 sm:space-y-6">
        <!-- Description -->
        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-2">Why this matters</label>
          <textarea
            v-if="editing"
            v-model="form.description"
            rows="2"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 text-sm"
            placeholder="What makes this goal important?"
          ></textarea>
          <p v-else class="text-gray-700">{{ goal.description || 'No description' }}</p>
        </div>

        <!-- Goal Relationships -->
        <div v-if="goal.parent || (goal.children && goal.children.length > 0)" class="bg-gray-50 rounded-lg p-4 space-y-2">
          <div v-if="goal.parent" class="flex items-center gap-2 text-sm">
            <span class="text-gray-500">Part of:</span>
            <span class="font-medium text-gray-900">{{ goal.parent.title }}</span>
            <span class="text-xs text-gray-400">({{ timeHorizonLabel(goal.parent.time_horizon) }})</span>
          </div>

          <div v-if="goal.children && goal.children.length > 0">
            <div class="text-sm text-gray-500 mb-1">Sub-goals:</div>
            <div class="space-y-1">
              <div
                v-for="child in goal.children"
                :key="child.id"
                class="flex items-center gap-2 text-sm pl-3"
              >
                <span class="text-gray-400">↳</span>
                <span class="font-medium text-gray-900">{{ child.title }}</span>
                <span class="text-xs text-gray-400">({{ timeHorizonLabel(child.time_horizon) }})</span>
                <span v-if="child.status === 'completed'" class="text-xs text-green-600">✓</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Metric Goal Content -->
        <div v-if="goal.tracking_type === 'metric'" class="space-y-4">
          <!-- Current Stats -->
          <div class="bg-gray-50 rounded-lg p-4">
            <div class="grid grid-cols-4 gap-4 text-center">
              <div>
                <div class="text-xs text-gray-500 mb-1">Start</div>
                <div class="font-bold text-lg">{{ formatMetric(goal.metric_start_value) }}</div>
              </div>
              <div>
                <div class="text-xs text-gray-500 mb-1">Current</div>
                <div class="font-bold text-lg text-green-600">{{ formatMetric(goal.metric_current_value) }}</div>
              </div>
              <div>
                <div class="text-xs text-gray-500 mb-1">Target</div>
                <div class="font-bold text-lg">{{ formatMetric(goal.metric_target_value) }}</div>
              </div>
              <div>
                <div class="text-xs text-gray-500 mb-1">Progress</div>
                <div class="font-bold text-lg text-green-600">{{ goal.metric_progress_percentage }}%</div>
              </div>
            </div>

            <!-- Progress Bar -->
            <div class="mt-4 w-full bg-gray-200 rounded-full h-2">
              <div
                class="bg-green-600 h-2 rounded-full transition-all"
                :style="{ width: goal.metric_progress_percentage + '%' }"
              ></div>
            </div>
          </div>

          <!-- Update History -->
          <div>
            <div class="flex justify-between items-center mb-3">
              <h3 class="font-semibold text-gray-900">Update History</h3>
              <button
                @click="$emit('update-metric', goal)"
                class="text-sm text-green-600 hover:text-green-700 font-medium"
              >
                + Add Update
              </button>
            </div>

            <div v-if="goal.updates && goal.updates.length > 0" class="space-y-2">
              <div
                v-for="update in goal.updates"
                :key="update.id"
                class="border border-gray-200 rounded-lg p-3 text-sm"
              >
                <div class="flex justify-between items-start mb-1">
                  <span class="font-semibold">{{ formatMetric(update.metric_value) }}</span>
                  <span class="text-xs text-gray-500">{{ formatDate(update.update_date) }}</span>
                </div>
                <p v-if="update.note" class="text-gray-600 text-xs">{{ update.note }}</p>
              </div>
            </div>
            <div v-else class="text-sm text-gray-500 italic">No updates yet</div>
          </div>
        </div>

        <!-- Milestone Goal Content -->
        <div v-else-if="goal.tracking_type === 'milestone'" class="space-y-4">
          <!-- Progress Stats -->
          <div class="bg-gray-50 rounded-lg p-4">
            <div class="text-center mb-4">
              <div class="text-xs text-gray-500 mb-1">Milestones</div>
              <div class="font-bold text-lg">{{ completedMilestones }}/{{ totalMilestones }}</div>
            </div>

            <div class="w-full bg-gray-200 rounded-full h-2">
              <div
                class="bg-green-600 h-2 rounded-full transition-all"
                :style="{ width: currentProgress + '%' }"
              ></div>
            </div>
          </div>

          <!-- Milestones -->
          <div>
            <div class="flex justify-between items-center mb-3">
              <h3 class="font-semibold text-gray-900">Milestones</h3>
              <button
                v-if="editing"
                @click="addMilestone"
                class="text-sm text-green-600 hover:text-green-700 font-medium"
              >
                + Add Milestone
              </button>
            </div>

            <div v-if="milestones.length > 0" class="space-y-2">
              <div
                v-for="(milestone, index) in milestones"
                :key="milestone.id || `new-${index}`"
                class="flex items-center gap-2 p-2 border border-gray-200 rounded-lg"
              >
                <input
                  v-if="!editing"
                  type="checkbox"
                  :checked="milestone.completed"
                  @change="toggleMilestone(milestone)"
                  class="w-5 h-5 text-green-600 border-gray-300 rounded flex-shrink-0"
                />

                <input
                  v-if="editing"
                  v-model="milestone.title"
                  type="text"
                  placeholder="Milestone title..."
                  class="flex-1 px-2 py-1 text-sm border border-gray-300 rounded focus:ring-1 focus:ring-green-500"
                />
                <span
                  v-else
                  class="flex-1 text-sm"
                  :class="milestone.completed ? 'line-through text-gray-500' : 'text-gray-900'"
                >
                  {{ milestone.title }}
                </span>

                <input
                  v-if="editing"
                  v-model="milestone.target_date"
                  type="date"
                  class="px-2 py-1 text-xs border border-gray-300 rounded focus:ring-1 focus:ring-green-500"
                />
                <span v-else-if="milestone.target_date" class="text-xs text-gray-500 flex-shrink-0">
                  {{ formatDate(milestone.target_date) }}
                </span>

                <button
                  v-if="editing"
                  @click="removeMilestone(index)"
                  class="text-red-600 hover:text-red-700 flex-shrink-0"
                >
                  <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                  </svg>
                </button>
              </div>
            </div>
            <div v-else class="text-sm text-gray-500 italic">No milestones yet</div>
          </div>
        </div>

        <!-- Evolution Goal Content -->
        <div v-else-if="goal.tracking_type === 'evolution'" class="space-y-4">
          <div class="flex justify-between items-center">
            <h3 class="font-semibold text-gray-900">Journal Updates</h3>
            <button
              @click="$emit('add-update', goal)"
              class="text-sm text-green-600 hover:text-green-700 font-medium"
            >
              + Add Update
            </button>
          </div>

          <div v-if="goal.updates && goal.updates.length > 0" class="space-y-3">
            <div
              v-for="update in goal.updates"
              :key="update.id"
              class="border border-gray-200 rounded-lg p-4"
            >
              <div class="flex justify-between items-start mb-2">
                <span class="text-xs font-semibold text-gray-500">{{ formatDate(update.update_date) }}</span>
              </div>
              <p class="text-gray-700 text-sm whitespace-pre-wrap">{{ update.note }}</p>
            </div>
          </div>
          <div v-else class="text-sm text-gray-500 italic">No updates yet</div>
        </div>

        <!-- Active Goal Content -->
        <div v-else-if="goal.tracking_type === 'active'">
          <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 text-sm text-blue-800">
            <p class="font-medium mb-1">Ongoing Goal</p>
            <p>This is an active goal with no specific end date. Update as needed.</p>
          </div>

          <div v-if="goal.last_update_note" class="mt-4">
            <h3 class="font-semibold text-gray-900 mb-2">Latest Note</h3>
            <p class="text-gray-700 text-sm">{{ goal.last_update_note }}</p>
          </div>
        </div>

        <!-- Edit Fields (when editing) -->
        <div v-if="editing" class="space-y-4 pt-4 border-t">
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-semibold mb-2">Status</label>
              <select v-model="form.status" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                <option value="active">Active</option>
                <option value="completed">Completed</option>
                <option value="paused">Paused</option>
                <option value="abandoned">Abandoned</option>
              </select>
            </div>

            <div>
              <label class="block text-sm font-semibold mb-2">Target Date</label>
              <input
                v-model="form.target_date"
                type="date"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm"
              />
            </div>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-semibold mb-2">Time Horizon</label>
              <select v-model="form.time_horizon" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                <option value="3_year">🎯 3-Year</option>
                <option value="yearly">📆 Yearly</option>
                <option value="quarterly">📅 Quarterly</option>
                <option value="monthly">🗓️ Monthly</option>
              </select>
            </div>

            <div>
              <label class="block text-sm font-semibold mb-2">Life Area</label>
              <select v-model="form.life_area" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                <option value="career">💼 Career</option>
                <option value="health">🏃 Health</option>
                <option value="relationships">❤️ Relationships</option>
                <option value="financial">💰 Financial</option>
                <option value="growth">📚 Growth</option>
                <option value="impact">🌍 Impact</option>
                <option value="other">✨ Other</option>
              </select>
            </div>
          </div>

          <!-- Metric Goal Edit Fields -->
          <div v-if="goal.tracking_type === 'metric'" class="bg-blue-50 border border-blue-200 rounded-lg p-4 space-y-3">
            <h4 class="text-sm font-semibold text-gray-900">Metric Settings</h4>
            <div class="grid grid-cols-3 gap-3">
              <div>
                <label class="block text-xs font-semibold mb-1">Unit</label>
                <input
                  v-model="form.metric_unit"
                  type="text"
                  class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg"
                  placeholder="€, kg, #"
                  maxlength="10"
                />
              </div>
              <div>
                <label class="block text-xs font-semibold mb-1">Start Value</label>
                <input
                  v-model.number="form.metric_start_value"
                  type="number"
                  step="0.01"
                  class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg"
                />
              </div>
              <div>
                <label class="block text-xs font-semibold mb-1">Target Value</label>
                <input
                  v-model.number="form.metric_target_value"
                  type="number"
                  step="0.01"
                  class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg"
                />
              </div>
            </div>
            <div class="flex items-center gap-2">
              <input
                v-model="form.metric_decrease"
                type="checkbox"
                id="edit_metric_decrease"
                class="w-4 h-4 text-green-600 border-gray-300 rounded"
              />
              <label for="edit_metric_decrease" class="text-xs">Lower is better (mortgage, weight loss)</label>
            </div>
          </div>

          <!-- Period Identifier (for quarterly/yearly) -->
          <div v-if="form.time_horizon === 'quarterly' || form.time_horizon === 'yearly'">
            <label class="block text-sm font-semibold mb-2">Period (Optional)</label>
            <input
              v-model="form.period_identifier"
              type="text"
              class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm"
              :placeholder="form.time_horizon === 'quarterly' ? 'Q1_2025' : '2025'"
            />
          </div>

          <!-- Parent Goal -->
          <div v-if="availableParentGoals.length > 0">
            <label class="block text-sm font-semibold mb-2">Parent Goal (Optional)</label>
            <select
              v-model="form.parent_goal_id"
              class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm"
            >
              <option :value="null">None - standalone goal</option>
              <option v-for="goal in availableParentGoals" :key="goal.id" :value="goal.id">
                {{ goal.title }}
              </option>
            </select>
            <p class="text-xs text-gray-500 mt-1">Link this to a broader goal</p>
          </div>

          <!-- Public Toggle -->
          <div class="flex items-center gap-2 pt-2">
            <input
              v-model="form.is_public"
              type="checkbox"
              id="edit_is_public"
              class="w-4 h-4 text-green-600 border-gray-300 rounded"
            />
            <label for="edit_is_public" class="text-sm">Make this goal public (visible on your profile)</label>
          </div>
        </div>

        <!-- Actions -->
        <div class="flex justify-between items-center pt-4 border-t">
          <button
            @click="deleteGoal"
            class="text-sm text-red-600 hover:text-red-700 font-medium"
          >
            Delete Goal
          </button>

          <button
            v-if="goal.status !== 'completed'"
            @click="markComplete"
            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-semibold"
          >
            Mark as Complete
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue';

const props = defineProps({
  goal: {
    type: Object,
    required: true,
  },
});

const emit = defineEmits(['close', 'updated', 'deleted', 'update-metric', 'add-update']);

const editing = ref(false);
const saving = ref(false);
const allGoals = ref([]);

const form = ref({
  title: props.goal.title,
  description: props.goal.description,
  status: props.goal.status,
  target_date: props.goal.target_date,
  time_horizon: props.goal.time_horizon,
  life_area: props.goal.life_area,
  parent_goal_id: props.goal.parent_goal_id,
  period_identifier: props.goal.period_identifier,
  is_public: props.goal.is_public ?? true,
  // Metric fields
  metric_unit: props.goal.metric_unit,
  metric_start_value: props.goal.metric_start_value,
  metric_target_value: props.goal.metric_target_value,
  metric_decrease: props.goal.metric_decrease || false,
});

// Milestones management (create a copy for editing)
const milestones = ref([...props.goal.milestones || []]);

// Watch for changes to props.goal and update local data
watch(() => props.goal, (newGoal) => {
  // Update milestones when goal changes (unless we're in edit mode)
  if (!editing.value) {
    milestones.value = [...newGoal.milestones || []];
  }

  // Update form data
  form.value = {
    title: newGoal.title,
    description: newGoal.description,
    status: newGoal.status,
    target_date: newGoal.target_date,
    time_horizon: newGoal.time_horizon,
    life_area: newGoal.life_area,
    parent_goal_id: newGoal.parent_goal_id,
    period_identifier: newGoal.period_identifier,
    is_public: newGoal.is_public ?? true,
    metric_unit: newGoal.metric_unit,
    metric_start_value: newGoal.metric_start_value,
    metric_target_value: newGoal.metric_target_value,
    metric_decrease: newGoal.metric_decrease || false,
  };
}, { deep: true });

// Fetch all goals for parent selection
const fetchGoals = async () => {
  try {
    const response = await window.axios.get('/api/journey/goals');
    allGoals.value = Object.values(response.data).flat();
  } catch (error) {
    console.error('Error fetching goals:', error);
  }
};

// Filter parent goals based on time horizon (exclude self)
const availableParentGoals = computed(() => {
  const timeHorizonMap = {
    '3_year': [],
    'yearly': ['3_year'],
    'quarterly': ['3_year', 'yearly'],
    'monthly': ['3_year', 'yearly', 'quarterly'],
  };

  const allowedParentHorizons = timeHorizonMap[form.value.time_horizon] || [];

  return allGoals.value.filter(goal =>
    allowedParentHorizons.includes(goal.time_horizon) && goal.id !== props.goal.id
  );
});

onMounted(() => {
  fetchGoals();
});

const trackingTypeLabel = computed(() => {
  const labels = {
    metric: '📊 Metric',
    milestone: '🎯 Milestones',
    evolution: '📝 Journal',
    active: '♾️ Active',
  };
  return labels[props.goal.tracking_type] || props.goal.tracking_type;
});

const statusBadgeClass = computed(() => {
  const colors = {
    active: 'px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800',
    completed: 'px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800',
    paused: 'px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800',
    abandoned: 'px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800',
  };
  return colors[props.goal.status] || colors.active;
});

const lifeAreaIcon = computed(() => {
  const icons = {
    career: '💼',
    health: '🏃',
    relationships: '❤️',
    financial: '💰',
    growth: '📚',
    impact: '🌍',
    other: '✨',
  };
  return icons[props.goal.life_area] || icons.other;
});

const timeHorizonLabel = (horizon) => {
  const labels = {
    '3_year': '3-Year',
    'yearly': 'Yearly',
    'quarterly': 'Quarterly',
    'monthly': 'Monthly',
  };
  return labels[horizon] || horizon;
};

const completedMilestones = computed(() => {
  return milestones.value.filter(m => m.completed).length || 0;
});

const totalMilestones = computed(() => {
  return milestones.value.length || 0;
});

// Calculate current progress percentage for milestone goals
const currentProgress = computed(() => {
  if (props.goal.tracking_type === 'milestone') {
    const total = totalMilestones.value;
    if (total === 0) return 0;
    return Math.round((completedMilestones.value / total) * 100);
  }
  return props.goal.progress_percentage || 0;
});

const formatMetric = (value) => {
  if (value === null || value === undefined) return '-';
  const unit = props.goal.metric_unit || '';
  const formatted = parseFloat(value).toLocaleString('en-US', {
    minimumFractionDigits: 0,
    maximumFractionDigits: 2,
  });
  return `${unit}${formatted}`;
};

const formatDate = (date) => {
  if (!date) return '';
  return new Date(date).toLocaleDateString('en-US', {
    month: 'short',
    day: 'numeric',
    year: 'numeric',
  });
};

const addMilestone = () => {
  milestones.value.push({
    title: '',
    target_date: null,
    completed: false,
    order: milestones.value.length,
  });
};

const removeMilestone = (index) => {
  milestones.value.splice(index, 1);
};

const saveGoal = async () => {
  saving.value = true;

  try {
    // Save basic goal fields
    await window.axios.put(`/api/journey/goals/${props.goal.id}`, form.value);

    // Save milestones (delete removed ones, update existing, create new)
    if (props.goal.tracking_type === 'milestone') {
      // Get IDs of milestones we're keeping
      const keepIds = milestones.value.filter(m => m.id).map(m => m.id);

      // Delete removed milestones
      const originalIds = (props.goal.milestones || []).map(m => m.id);
      const toDelete = originalIds.filter(id => !keepIds.includes(id));

      for (const id of toDelete) {
        await window.axios.delete(`/api/journey/goals/${props.goal.id}/milestones/${id}`);
      }

      // Update or create milestones
      for (const milestone of milestones.value) {
        if (milestone.id) {
          // Update existing
          await window.axios.put(`/api/journey/goals/${props.goal.id}/milestones/${milestone.id}`, {
            title: milestone.title,
            target_date: milestone.target_date,
          });
        } else if (milestone.title.trim()) {
          // Create new (only if title is not empty)
          await window.axios.post(`/api/journey/goals/${props.goal.id}/milestones`, {
            title: milestone.title,
            target_date: milestone.target_date,
          });
        }
      }
    }

    editing.value = false;
    emit('updated');
  } catch (error) {
    console.error('Error saving goal:', error);
    alert('Failed to save. Please try again.');
  } finally {
    saving.value = false;
  }
};

const toggleMilestone = async (milestone) => {
  try {
    await window.axios.put(`/api/journey/goals/${props.goal.id}/milestones/${milestone.id}`, {
      completed: !milestone.completed,
    });

    // Update local milestone state immediately
    milestone.completed = !milestone.completed;

    emit('updated');
  } catch (error) {
    console.error('Error toggling milestone:', error);
  }
};

const markComplete = async () => {
  if (!confirm('Mark this goal as complete?')) return;

  try {
    await window.axios.post(`/api/journey/goals/${props.goal.id}/complete`);
    emit('updated');
  } catch (error) {
    console.error('Error marking complete:', error);
  }
};

const deleteGoal = async () => {
  if (!confirm('Delete this goal permanently?')) return;

  try {
    await window.axios.delete(`/api/journey/goals/${props.goal.id}`);
    emit('deleted');
    emit('close');
  } catch (error) {
    console.error('Error deleting goal:', error);
    alert('Failed to delete. Please try again.');
  }
};
</script>
