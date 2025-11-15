<template>
  <div class="fixed inset-0 bg-gray-900/20 dark:bg-gray-900/60 backdrop-blur-sm flex items-end sm:items-center justify-center z-50 sm:p-4" @click.self="$emit('close')">
    <div class="bg-white dark:bg-gray-800 rounded-t-2xl sm:rounded-xl max-w-2xl w-full max-h-[90vh] sm:max-h-[85vh] flex flex-col">
      <div class="flex-shrink-0 p-4 sm:p-6 pb-3 sm:pb-4 border-b border-gray-200 dark:border-gray-700">
        <h2 class="text-lg sm:text-2xl font-bold mb-1 sm:mb-2 text-gray-900 dark:text-white">Create New Goal</h2>
        <p class="text-xs sm:text-sm text-gray-600 dark:text-gray-400">Choose how you'll track progress toward what matters</p>
      </div>

      <form @submit.prevent="createGoal" class="overflow-y-auto flex-1 p-4 sm:p-6 space-y-3 sm:space-y-4">
        <!-- Goal Title -->
        <div>
          <label class="block text-xs sm:text-sm font-semibold mb-1.5 sm:mb-2 text-gray-900 dark:text-white">Goal Title</label>
          <input
            v-model="form.title"
            type="text"
            required
            class="w-full px-3 sm:px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 dark:focus:ring-green-600 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder:text-gray-400 dark:placeholder:text-gray-500"
            placeholder="Pay off mortgage, Launch MVP..."
          />
        </div>

        <!-- Tracking Type -->
        <div>
          <label class="block text-xs sm:text-sm font-semibold mb-1.5 sm:mb-2 text-gray-900 dark:text-white">How will you track progress?</label>
          <div class="grid grid-cols-2 gap-2">
            <button
              type="button"
              @click="form.tracking_type = 'metric'"
              :class="form.tracking_type === 'metric' ? 'border-green-500 dark:border-green-600 bg-green-50 dark:bg-green-950/30' : 'border-gray-300 dark:border-gray-600'"
              class="p-2 sm:p-3 border-2 rounded-lg text-left hover:border-green-400 dark:hover:border-green-500 transition"
            >
              <div class="font-semibold text-xs sm:text-sm text-gray-900 dark:text-white">📊 Metric</div>
              <div class="text-[10px] sm:text-xs text-gray-600 dark:text-gray-400">Track a number</div>
            </button>

            <button
              type="button"
              @click="form.tracking_type = 'milestone'"
              :class="form.tracking_type === 'milestone' ? 'border-green-500 dark:border-green-600 bg-green-50 dark:bg-green-950/30' : 'border-gray-300 dark:border-gray-600'"
              class="p-2 sm:p-3 border-2 rounded-lg text-left hover:border-green-400 dark:hover:border-green-500 transition"
            >
              <div class="font-semibold text-xs sm:text-sm text-gray-900 dark:text-white">🎯 Milestones</div>
              <div class="text-[10px] sm:text-xs text-gray-600 dark:text-gray-400">Key checkpoints</div>
            </button>

            <button
              type="button"
              @click="form.tracking_type = 'evolution'"
              :class="form.tracking_type === 'evolution' ? 'border-green-500 dark:border-green-600 bg-green-50 dark:bg-green-950/30' : 'border-gray-300 dark:border-gray-600'"
              class="p-2 sm:p-3 border-2 rounded-lg text-left hover:border-green-400 dark:hover:border-green-500 transition"
            >
              <div class="font-semibold text-xs sm:text-sm text-gray-900 dark:text-white">📝 Journal</div>
              <div class="text-[10px] sm:text-xs text-gray-600 dark:text-gray-400">Notes over time</div>
            </button>

            <button
              type="button"
              @click="form.tracking_type = 'active'"
              :class="form.tracking_type === 'active' ? 'border-green-500 dark:border-green-600 bg-green-50 dark:bg-green-950/30' : 'border-gray-300 dark:border-gray-600'"
              class="p-2 sm:p-3 border-2 rounded-lg text-left hover:border-green-400 dark:hover:border-green-500 transition"
            >
              <div class="font-semibold text-xs sm:text-sm text-gray-900 dark:text-white">♾️ Active</div>
              <div class="text-[10px] sm:text-xs text-gray-600 dark:text-gray-400">Ongoing</div>
            </button>
          </div>
        </div>

        <!-- Metric Fields (if metric selected) -->
        <div v-if="form.tracking_type === 'metric'" class="bg-blue-50 dark:bg-blue-950/30 border border-blue-200 dark:border-blue-900 rounded-lg p-4 space-y-3">
          <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div>
              <label class="block text-xs font-semibold mb-1 text-gray-900 dark:text-white">Unit</label>
              <input
                v-model="form.metric_unit"
                type="text"
                class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder:text-gray-400 dark:placeholder:text-gray-500"
                placeholder="€, kg, #"
                maxlength="10"
              />
            </div>
            <div>
              <label class="block text-xs font-semibold mb-1 text-gray-900 dark:text-white">Start Value</label>
              <input
                v-model.number="form.metric_start_value"
                type="number"
                step="0.01"
                class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder:text-gray-400 dark:placeholder:text-gray-500"
                placeholder="69000"
              />
            </div>
            <div>
              <label class="block text-xs font-semibold mb-1 text-gray-900 dark:text-white">Target Value</label>
              <input
                v-model.number="form.metric_target_value"
                type="number"
                step="0.01"
                class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder:text-gray-400 dark:placeholder:text-gray-500"
                placeholder="0"
              />
            </div>
          </div>

          <div class="flex items-center gap-2">
            <input
              v-model="form.metric_decrease"
              type="checkbox"
              id="metric_decrease"
              class="w-4 h-4 text-green-600 dark:text-green-500 border-gray-300 dark:border-gray-600 rounded focus:ring-green-500 dark:focus:ring-green-600 bg-white dark:bg-gray-700"
            />
            <label for="metric_decrease" class="text-xs text-gray-900 dark:text-gray-300">Lower is better (mortgage, weight loss)</label>
          </div>
        </div>

        <!-- Description -->
        <div>
          <label class="block text-xs sm:text-sm font-semibold mb-1.5 sm:mb-2 text-gray-900 dark:text-white">Why does this matter?</label>
          <textarea
            v-model="form.description"
            rows="2"
            class="w-full px-3 sm:px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 dark:focus:ring-green-600 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder:text-gray-400 dark:placeholder:text-gray-500"
            placeholder="This matters because..."
          ></textarea>
        </div>

        <!-- Time Horizon & Life Area -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
          <div>
            <label class="block text-xs sm:text-sm font-semibold mb-1.5 sm:mb-2 text-gray-900 dark:text-white">Time Horizon</label>
            <select
              v-model="form.time_horizon"
              required
              class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 dark:focus:ring-green-600 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
            >
              <option value="3_year">🎯 3-Year</option>
              <option value="yearly">📆 Yearly</option>
              <option value="quarterly">📅 Quarterly</option>
              <option value="monthly">🗓️ Monthly</option>
            </select>
          </div>

          <div>
            <label class="block text-xs sm:text-sm font-semibold mb-1.5 sm:mb-2 text-gray-900 dark:text-white">Life Area</label>
            <select
              v-model="form.life_area"
              required
              class="w-full px-3 sm:px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 dark:focus:ring-green-600 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
            >
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

        <!-- Vision Reminder (if available for this life area) -->
        <div v-if="visionForSelectedArea" class="bg-purple-50 dark:bg-purple-950/30 border border-purple-200 dark:border-purple-900 rounded-lg p-4">
          <div class="flex items-start justify-between gap-3 mb-2">
            <div class="flex items-center gap-2">
              <span class="text-lg">💭</span>
              <h4 class="text-sm font-semibold text-purple-900 dark:text-purple-300">Your Vision for {{ lifeAreaLabel }}</h4>
            </div>
            <button
              type="button"
              @click="showVisionReminder = !showVisionReminder"
              class="text-purple-600 dark:text-purple-400 hover:text-purple-800 dark:hover:text-purple-300 text-xs font-medium"
            >
              {{ showVisionReminder ? 'Hide' : 'Show' }}
            </button>
          </div>
          <div v-if="showVisionReminder">
            <p class="text-sm text-purple-800 dark:text-purple-300 whitespace-pre-wrap leading-relaxed">{{ visionForSelectedArea }}</p>
            <p class="text-xs text-purple-600 dark:text-purple-400 mt-2 italic">
              💡 Does this goal support your vision for this area of life?
            </p>
          </div>
        </div>

        <!-- Parent Goal (Optional) -->
        <div v-if="availableParentGoals.length > 0">
          <label class="block text-sm font-semibold mb-2 text-gray-900 dark:text-white">Parent Goal (Optional)</label>
          <select
            v-model="form.parent_goal_id"
            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 dark:focus:ring-green-600 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
          >
            <option :value="null">None - standalone goal</option>
            <option v-for="goal in availableParentGoals" :key="goal.id" :value="goal.id">
              {{ goal.title }}
            </option>
          </select>
          <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Link this to a broader goal</p>
        </div>

        <!-- Target Date -->
        <div>
          <label class="block text-sm font-semibold mb-2 text-gray-900 dark:text-white">Target Date (Optional)</label>
          <input
            v-model="form.target_date"
            type="date"
            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 dark:focus:ring-green-600 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
          />
        </div>

        <!-- Period Identifier (optional) -->
        <div v-if="form.time_horizon === 'quarterly' || form.time_horizon === 'yearly'">
          <label class="block text-sm font-semibold mb-2 text-gray-900 dark:text-white">Period (Optional)</label>
          <input
            v-model="form.period_identifier"
            type="text"
            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 dark:focus:ring-green-600 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder:text-gray-400 dark:placeholder:text-gray-500"
            :placeholder="form.time_horizon === 'quarterly' ? 'Q1_2025' : '2025'"
          />
        </div>

        <!-- Public Toggle -->
        <div class="flex items-center gap-2">
          <input
            v-model="form.is_public"
            type="checkbox"
            id="is_public"
            class="w-4 h-4 text-green-600 dark:text-green-500 border-gray-300 dark:border-gray-600 rounded focus:ring-green-500 dark:focus:ring-green-600 bg-white dark:bg-gray-700"
          />
          <label for="is_public" class="text-sm text-gray-900 dark:text-gray-300">Make this goal public</label>
        </div>

      </form>

        <!-- Actions -->
        <div class="flex-shrink-0 flex justify-end gap-2 sm:gap-3 p-4 sm:p-6 pt-3 sm:pt-4 border-t border-gray-200 dark:border-gray-700">
          <button
            type="button"
            @click="$emit('close')"
            class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 text-xs sm:text-sm text-gray-900 dark:text-white"
          >
            Cancel
          </button>
          <button
            type="submit"
            @click="createGoal"
            :disabled="saving"
            class="px-5 sm:px-6 py-2 bg-green-600 dark:bg-green-700 text-white rounded-lg hover:bg-green-700 dark:hover:bg-green-600 disabled:opacity-50 text-xs sm:text-sm font-semibold"
          >
            {{ saving ? 'Creating...' : 'Create Goal' }}
          </button>
        </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';

const emit = defineEmits(['close', 'created']);

const saving = ref(false);
const allGoals = ref([]);
const vision = ref(null);
const showVisionReminder = ref(true);

const form = ref({
  title: '',
  description: '',
  time_horizon: '3_year',
  life_area: 'other',
  tracking_type: 'metric',
  metric_unit: '',
  metric_start_value: null,
  metric_target_value: null,
  metric_current_value: null,
  metric_decrease: false,
  period_identifier: '',
  parent_goal_id: null,
  target_date: null,
  is_public: true,
});

const lifeAreaLabels = {
  career: 'Career/Business',
  health: 'Health/Wellness',
  relationships: 'Relationships/Family',
  financial: 'Financial Freedom',
  growth: 'Personal Growth',
  impact: 'Contribution/Impact',
  other: 'Other',
};

// Fetch all goals for parent selection
const fetchGoals = async () => {
  try {
    const response = await window.axios.get('/api/journey/goals');
    // Flatten the grouped goals into a single array
    allGoals.value = Object.values(response.data).flat();
  } catch (error) {
    console.error('Error fetching goals:', error);
  }
};

// Fetch vision data
const fetchVision = async () => {
  try {
    const response = await window.axios.get('/api/journey/vision');
    vision.value = response.data;
  } catch (error) {
    console.error('Error fetching vision:', error);
  }
};

// Get vision content for selected life area
const visionForSelectedArea = computed(() => {
  if (!vision.value || !vision.value.definition_of_success || form.value.life_area === 'other') {
    return null;
  }

  const visionText = vision.value.definition_of_success[form.value.life_area];
  return visionText && visionText.trim().length > 0 ? visionText : null;
});

// Get label for selected life area
const lifeAreaLabel = computed(() => {
  return lifeAreaLabels[form.value.life_area] || 'Other';
});

// Filter parent goals based on time horizon
// 3_year -> no parents
// yearly -> 3_year parents
// quarterly -> yearly or 3_year parents
// monthly -> quarterly, yearly, or 3_year parents
const availableParentGoals = computed(() => {
  const timeHorizonMap = {
    '3_year': [],
    'yearly': ['3_year'],
    'quarterly': ['3_year', 'yearly'],
    'monthly': ['3_year', 'yearly', 'quarterly'],
  };

  const allowedParentHorizons = timeHorizonMap[form.value.time_horizon] || [];

  return allGoals.value.filter(goal =>
    allowedParentHorizons.includes(goal.time_horizon)
  );
});

onMounted(() => {
  fetchGoals();
  fetchVision();
});

const createGoal = async () => {
  saving.value = true;

  try {
    // Clean up form data based on tracking type
    const data = { ...form.value };

    // Set current value to start value for metric goals
    if (data.tracking_type === 'metric') {
      data.metric_current_value = data.metric_start_value;
    } else {
      // Clear metric fields for non-metric goals
      delete data.metric_unit;
      delete data.metric_start_value;
      delete data.metric_target_value;
      delete data.metric_current_value;
      delete data.metric_decrease;
    }

    await window.axios.post('/api/journey/goals', data);
    emit('created');
  } catch (error) {
    console.error('Error creating goal:', error);
    alert('Error creating goal. Please try again.');
  } finally {
    saving.value = false;
  }
};
</script>
