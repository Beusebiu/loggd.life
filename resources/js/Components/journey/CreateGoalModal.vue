<template>
  <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4" @click.self="$emit('close')">
    <div class="bg-white rounded-xl max-w-2xl w-full p-6 max-h-[90vh] overflow-y-auto">
      <h2 class="text-2xl font-bold mb-2">Create New Goal</h2>
      <p class="text-sm text-gray-600 mb-4">Choose how you'll track progress toward what matters</p>

      <form @submit.prevent="createGoal" class="space-y-4">
        <!-- Goal Title -->
        <div>
          <label class="block text-sm font-semibold mb-2">Goal Title</label>
          <input
            v-model="form.title"
            type="text"
            required
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500"
            placeholder="Pay off mortgage, Launch MVP, Get to 70kg..."
          />
        </div>

        <!-- Tracking Type -->
        <div>
          <label class="block text-sm font-semibold mb-2">How will you track progress?</label>
          <div class="grid grid-cols-2 gap-2">
            <button
              type="button"
              @click="form.tracking_type = 'metric'"
              :class="form.tracking_type === 'metric' ? 'border-green-500 bg-green-50' : 'border-gray-300'"
              class="p-3 border-2 rounded-lg text-left hover:border-green-400 transition"
            >
              <div class="font-semibold text-sm">📊 Metric</div>
              <div class="text-xs text-gray-600">Track a number (€, kg, customers)</div>
            </button>

            <button
              type="button"
              @click="form.tracking_type = 'milestone'"
              :class="form.tracking_type === 'milestone' ? 'border-green-500 bg-green-50' : 'border-gray-300'"
              class="p-3 border-2 rounded-lg text-left hover:border-green-400 transition"
            >
              <div class="font-semibold text-sm">🎯 Milestones</div>
              <div class="text-xs text-gray-600">Key checkpoints to complete</div>
            </button>

            <button
              type="button"
              @click="form.tracking_type = 'evolution'"
              :class="form.tracking_type === 'evolution' ? 'border-green-500 bg-green-50' : 'border-gray-300'"
              class="p-3 border-2 rounded-lg text-left hover:border-green-400 transition"
            >
              <div class="font-semibold text-sm">📝 Journal</div>
              <div class="text-xs text-gray-600">Update with notes over time</div>
            </button>

            <button
              type="button"
              @click="form.tracking_type = 'active'"
              :class="form.tracking_type === 'active' ? 'border-green-500 bg-green-50' : 'border-gray-300'"
              class="p-3 border-2 rounded-lg text-left hover:border-green-400 transition"
            >
              <div class="font-semibold text-sm">♾️ Active</div>
              <div class="text-xs text-gray-600">Ongoing, no end date</div>
            </button>
          </div>
        </div>

        <!-- Metric Fields (if metric selected) -->
        <div v-if="form.tracking_type === 'metric'" class="bg-blue-50 border border-blue-200 rounded-lg p-4 space-y-3">
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
                placeholder="69000"
              />
            </div>
            <div>
              <label class="block text-xs font-semibold mb-1">Target Value</label>
              <input
                v-model.number="form.metric_target_value"
                type="number"
                step="0.01"
                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg"
                placeholder="0"
              />
            </div>
          </div>

          <div class="flex items-center gap-2">
            <input
              v-model="form.metric_decrease"
              type="checkbox"
              id="metric_decrease"
              class="w-4 h-4 text-green-600 border-gray-300 rounded"
            />
            <label for="metric_decrease" class="text-xs">Lower is better (mortgage, weight loss)</label>
          </div>
        </div>

        <!-- Description -->
        <div>
          <label class="block text-sm font-semibold mb-2">Why does this matter?</label>
          <textarea
            v-model="form.description"
            rows="2"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 text-sm"
            placeholder="This matters because..."
          ></textarea>
        </div>

        <!-- Time Horizon & Life Area -->
        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-semibold mb-2">Time Horizon</label>
            <select
              v-model="form.time_horizon"
              required
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 text-sm"
            >
              <option value="3_year">🎯 3-Year</option>
              <option value="yearly">📆 Yearly</option>
              <option value="quarterly">📅 Quarterly</option>
              <option value="monthly">🗓️ Monthly</option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-semibold mb-2">Life Area</label>
            <select
              v-model="form.life_area"
              required
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 text-sm"
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
        <div v-if="visionForSelectedArea" class="bg-purple-50 border border-purple-200 rounded-lg p-4">
          <div class="flex items-start justify-between gap-3 mb-2">
            <div class="flex items-center gap-2">
              <span class="text-lg">💭</span>
              <h4 class="text-sm font-semibold text-purple-900">Your Vision for {{ lifeAreaLabel }}</h4>
            </div>
            <button
              type="button"
              @click="showVisionReminder = !showVisionReminder"
              class="text-purple-600 hover:text-purple-800 text-xs font-medium"
            >
              {{ showVisionReminder ? 'Hide' : 'Show' }}
            </button>
          </div>
          <div v-if="showVisionReminder">
            <p class="text-sm text-purple-800 whitespace-pre-wrap leading-relaxed">{{ visionForSelectedArea }}</p>
            <p class="text-xs text-purple-600 mt-2 italic">
              💡 Does this goal support your vision for this area of life?
            </p>
          </div>
        </div>

        <!-- Parent Goal (Optional) -->
        <div v-if="availableParentGoals.length > 0">
          <label class="block text-sm font-semibold mb-2">Parent Goal (Optional)</label>
          <select
            v-model="form.parent_goal_id"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 text-sm"
          >
            <option :value="null">None - standalone goal</option>
            <option v-for="goal in availableParentGoals" :key="goal.id" :value="goal.id">
              {{ goal.title }}
            </option>
          </select>
          <p class="text-xs text-gray-500 mt-1">Link this to a broader goal</p>
        </div>

        <!-- Target Date -->
        <div>
          <label class="block text-sm font-semibold mb-2">Target Date (Optional)</label>
          <input
            v-model="form.target_date"
            type="date"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 text-sm"
          />
        </div>

        <!-- Period Identifier (optional) -->
        <div v-if="form.time_horizon === 'quarterly' || form.time_horizon === 'yearly'">
          <label class="block text-sm font-semibold mb-2">Period (Optional)</label>
          <input
            v-model="form.period_identifier"
            type="text"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 text-sm"
            :placeholder="form.time_horizon === 'quarterly' ? 'Q1_2025' : '2025'"
          />
        </div>

        <!-- Public Toggle -->
        <div class="flex items-center gap-2">
          <input
            v-model="form.is_public"
            type="checkbox"
            id="is_public"
            class="w-4 h-4 text-green-600 border-gray-300 rounded"
          />
          <label for="is_public" class="text-sm">Make this goal public</label>
        </div>

        <!-- Actions -->
        <div class="flex justify-end gap-3 pt-4 border-t">
          <button
            type="button"
            @click="$emit('close')"
            class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 text-sm"
          >
            Cancel
          </button>
          <button
            type="submit"
            :disabled="saving"
            class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 disabled:opacity-50 text-sm font-semibold"
          >
            {{ saving ? 'Creating...' : 'Create Goal' }}
          </button>
        </div>
      </form>
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
