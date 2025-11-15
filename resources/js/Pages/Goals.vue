<template>
  <AppLayout>
    <div class="max-w-6xl mx-auto px-3 sm:px-4 md:px-6 lg:px-8 py-4 sm:py-8">
      <!-- Journey Navigation -->
      <JourneyNav />

      <!-- Header -->
      <div class="flex justify-between items-start mb-4 sm:mb-8">
        <div>
          <h1 class="text-xl sm:text-3xl font-bold text-gray-900 dark:text-white mb-1 sm:mb-2">Goals</h1>
          <p class="text-xs sm:text-base text-gray-600 dark:text-gray-400">Track progress toward what matters most</p>
        </div>
        <button
          @click="showCreateModal = true"
          class="px-2 sm:px-4 py-2 text-xs sm:text-sm font-medium text-green-700 dark:text-green-400 bg-white dark:bg-gray-800 border border-green-300 dark:border-green-700 rounded-lg hover:bg-green-50 dark:hover:bg-gray-700 transition flex items-center gap-1 sm:gap-2 flex-shrink-0"
        >
          <span>➕</span>
          <span class="hidden sm:inline">New Goal</span>
        </button>
      </div>

      <!-- Goals Grid -->
      <div class="space-y-8">
        <!-- 3-Year Goals -->
        <div v-if="groupedGoals['3_year'] && groupedGoals['3_year'].length > 0">
          <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-3 flex items-center gap-2">
            <span>🎯 Long-term Vision</span>
            <span class="text-sm font-normal text-gray-500 dark:text-gray-400">(3+ years)</span>
          </h2>
          <div class="grid md:grid-cols-2 gap-4">
            <GoalCard
              v-for="goal in groupedGoals['3_year']"
              :key="goal.id"
              :goal="goal"
              @click="viewGoal"
              @update-metric="openMetricUpdate"
              @add-update="openJournalUpdate"
            />
          </div>
        </div>

        <!-- Yearly Goals -->
        <div v-if="groupedGoals['yearly'] && groupedGoals['yearly'].length > 0">
          <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-3 flex items-center gap-2">
            <span>📅 This Year ({{ currentYear }})</span>
          </h2>
          <div class="grid md:grid-cols-2 gap-4">
            <GoalCard
              v-for="goal in groupedGoals['yearly']"
              :key="goal.id"
              :goal="goal"
              @click="viewGoal"
              @update-metric="openMetricUpdate"
              @add-update="openJournalUpdate"
            />
          </div>
        </div>

        <!-- Quarterly Goals -->
        <div v-if="groupedGoals['quarterly'] && groupedGoals['quarterly'].length > 0">
          <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-3 flex items-center gap-2">
            <span>📆 This Quarter</span>
          </h2>
          <div class="grid md:grid-cols-2 gap-4">
            <GoalCard
              v-for="goal in groupedGoals['quarterly']"
              :key="goal.id"
              :goal="goal"
              @click="viewGoal"
              @update-metric="openMetricUpdate"
              @add-update="openJournalUpdate"
            />
          </div>
        </div>

        <!-- Monthly Goals -->
        <div v-if="groupedGoals['monthly'] && groupedGoals['monthly'].length > 0">
          <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-3 flex items-center gap-2">
            <span>🗓️ This Month</span>
          </h2>
          <div class="grid md:grid-cols-2 gap-4">
            <GoalCard
              v-for="goal in groupedGoals['monthly']"
              :key="goal.id"
              :goal="goal"
              @click="viewGoal"
              @update-metric="openMetricUpdate"
              @add-update="openJournalUpdate"
            />
          </div>
        </div>

        <!-- Empty State -->
        <div v-if="!hasAnyGoals" class="text-center py-16">
          <div class="text-6xl mb-4">🎯</div>
          <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Ready to set your first goal?</h3>
          <p class="text-gray-600 dark:text-gray-400 mb-6 max-w-md mx-auto">
            Start with one meaningful goal. Track it with numbers, milestones, or journal updates.
          </p>
          <button
            @click="showCreateModal = true"
            class="px-6 py-3 bg-green-600 dark:bg-green-700 text-white rounded-lg hover:bg-green-700 dark:hover:bg-green-600 font-semibold"
          >
            Create First Goal
          </button>
        </div>
      </div>

      <!-- Create Goal Modal -->
      <CreateGoalModal
        v-if="showCreateModal"
        @close="showCreateModal = false"
        @created="handleGoalCreated"
      />

      <!-- Goal Detail Modal -->
      <GoalDetailModal
        v-if="selectedGoal"
        :goal="selectedGoal"
        @close="selectedGoal = null"
        @updated="handleGoalUpdated"
        @deleted="handleGoalDeleted"
        @update-metric="openMetricUpdate"
        @add-update="openJournalUpdate"
      />

      <!-- Update Metric Modal -->
      <div v-if="metricUpdateGoal" class="fixed inset-0 bg-gray-900/20 dark:bg-gray-900/60 backdrop-blur-sm flex items-end sm:items-center justify-center z-50 sm:p-4" @click.self="metricUpdateGoal = null">
        <div class="bg-white dark:bg-gray-800 rounded-t-2xl sm:rounded-xl max-w-md w-full max-h-[90vh] sm:max-h-[85vh] flex flex-col">
          <div class="flex-shrink-0 p-4 sm:p-6 pb-3 sm:pb-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg sm:text-xl font-bold text-gray-900 dark:text-white mb-1 sm:mb-2">Update: {{ metricUpdateGoal.title }}</h3>
            <p class="text-xs sm:text-sm text-gray-600 dark:text-gray-400">Record your current progress</p>
          </div>

          <form @submit.prevent="submitMetricUpdate" class="overflow-y-auto flex-1 p-4 sm:p-6 space-y-3 sm:space-y-4">
            <div>
              <label class="block text-sm font-semibold mb-2 dark:text-gray-300">Current Value</label>
              <div class="flex items-center gap-2">
                <span class="text-gray-600 dark:text-gray-400">{{ metricUpdateGoal.metric_unit }}</span>
                <input
                  v-model.number="metricUpdateForm.value"
                  type="number"
                  step="0.01"
                  required
                  class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 dark:focus:ring-green-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                  :placeholder="formatMetricValue(metricUpdateGoal.metric_current_value)"
                />
              </div>
              <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                Previous: {{ metricUpdateGoal.metric_unit }}{{ formatMetricValue(metricUpdateGoal.metric_current_value) }}
              </p>
            </div>

            <div>
              <label class="block text-sm font-semibold mb-2 dark:text-gray-300">Note (Optional)</label>
              <textarea
                v-model="metricUpdateForm.note"
                rows="3"
                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 dark:focus:ring-green-600 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder:text-gray-400 dark:placeholder:text-gray-500"
                placeholder="What changed? Any insights?"
              ></textarea>
            </div>

          </form>

          <div class="flex justify-end gap-2 sm:gap-3 flex-shrink-0 p-4 sm:p-6 pt-3 sm:pt-4 border-t border-gray-200 dark:border-gray-700">
            <button
              type="button"
              @click="metricUpdateGoal = null"
              class="px-4 py-2 text-xs sm:text-sm border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-900 dark:text-white"
            >
              Cancel
            </button>
            <button
              type="submit"
              @click="submitMetricUpdate"
              :disabled="updatingMetric"
              class="px-6 py-2 text-xs sm:text-sm bg-green-600 dark:bg-green-700 text-white rounded-lg hover:bg-green-700 dark:hover:bg-green-600 disabled:opacity-50"
            >
              {{ updatingMetric ? 'Saving...' : 'Update' }}
            </button>
          </div>
        </div>
      </div>

      <!-- Journal Update Modal -->
      <div v-if="journalUpdateGoal" class="fixed inset-0 bg-gray-900/20 dark:bg-gray-900/60 backdrop-blur-sm flex items-end sm:items-center justify-center z-50 sm:p-4" @click.self="journalUpdateGoal = null">
        <div class="bg-white dark:bg-gray-800 rounded-t-2xl sm:rounded-xl max-w-md w-full max-h-[90vh] sm:max-h-[85vh] flex flex-col">
          <div class="flex-shrink-0 p-4 sm:p-6 pb-3 sm:pb-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg sm:text-xl font-bold text-gray-900 dark:text-white mb-1 sm:mb-2">Update: {{ journalUpdateGoal.title }}</h3>
            <p class="text-xs sm:text-sm text-gray-600 dark:text-gray-400">Add progress notes or reflections</p>
          </div>

          <form @submit.prevent="submitJournalUpdate" class="overflow-y-auto flex-1 p-4 sm:p-6 space-y-3 sm:space-y-4">
            <div>
              <label class="block text-sm font-semibold mb-2 dark:text-gray-300">Update</label>
              <textarea
                v-model="journalUpdateForm.note"
                rows="6"
                required
                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 dark:focus:ring-green-600 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder:text-gray-400 dark:placeholder:text-gray-500"
                placeholder="What's happening with this goal? Any progress, challenges, or insights?"
              ></textarea>
            </div>

          </form>

          <div class="flex justify-end gap-2 sm:gap-3 flex-shrink-0 p-4 sm:p-6 pt-3 sm:pt-4 border-t border-gray-200 dark:border-gray-700">
            <button
              type="button"
              @click="journalUpdateGoal = null"
              class="px-4 py-2 text-xs sm:text-sm border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-900 dark:text-white"
            >
              Cancel
            </button>
            <button
              type="submit"
              @click="submitJournalUpdate"
              :disabled="updatingJournal"
              class="px-6 py-2 text-xs sm:text-sm bg-green-600 dark:bg-green-700 text-white rounded-lg hover:bg-green-700 dark:hover:bg-green-600 disabled:opacity-50"
            >
              {{ updatingJournal ? 'Saving...' : 'Add Update' }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import AppLayout from '../Layouts/AppLayout.vue';
import JourneyNav from '../Components/journey/JourneyNav.vue';
import GoalCard from '../Components/journey/GoalCard.vue';
import CreateGoalModal from '../Components/journey/CreateGoalModal.vue';
import GoalDetailModal from '../Components/journey/GoalDetailModal.vue';
import { ref, computed, onMounted } from 'vue';
import { formatMetricValue } from '../utils/formatters';

const goals = ref({});
const showCreateModal = ref(false);
const selectedGoal = ref(null);
const currentYear = new Date().getFullYear();

// Metric update
const metricUpdateGoal = ref(null);
const metricUpdateForm = ref({ value: null, note: '' });
const updatingMetric = ref(false);

// Journal update
const journalUpdateGoal = ref(null);
const journalUpdateForm = ref({ note: '' });
const updatingJournal = ref(false);

const groupedGoals = computed(() => goals.value);

const hasAnyGoals = computed(() => {
  return Object.values(groupedGoals.value).some(arr => arr && arr.length > 0);
});

const fetchGoals = async () => {
  try {
    const response = await window.axios.get('/api/journey/goals');
    goals.value = response.data;
  } catch (error) {
    console.error('Error fetching goals:', error);
  }
};

const findGoalById = (goalId) => {
  // Search through all time horizons to find the goal
  for (const horizon in goals.value) {
    const goal = goals.value[horizon]?.find(g => g.id === goalId);
    if (goal) return goal;
  }
  return null;
};

const handleGoalCreated = () => {
  showCreateModal.value = false;
  fetchGoals();
};

const handleGoalUpdated = async () => {
  await fetchGoals();

  // Refresh the selected goal with updated data
  if (selectedGoal.value) {
    const updatedGoal = findGoalById(selectedGoal.value.id);
    if (updatedGoal) {
      selectedGoal.value = updatedGoal;
    }
  }
};

const handleGoalDeleted = () => {
  selectedGoal.value = null;
  fetchGoals();
};

const viewGoal = (goal) => {
  selectedGoal.value = goal;
};

const openMetricUpdate = (goal) => {
  metricUpdateGoal.value = goal;
  metricUpdateForm.value = {
    value: goal.metric_current_value || goal.metric_start_value,
    note: '',
  };
};

const openJournalUpdate = (goal) => {
  journalUpdateGoal.value = goal;
  journalUpdateForm.value = { note: '' };
};

const submitMetricUpdate = async () => {
  updatingMetric.value = true;

  try {
    await window.axios.post(`/api/journey/goals/${metricUpdateGoal.value.id}/updates`, {
      metric_value: metricUpdateForm.value.value,
      note: metricUpdateForm.value.note || null,
    });

    metricUpdateGoal.value = null;
    await fetchGoals();

    // Refresh the selected goal if it's open
    if (selectedGoal.value) {
      const updatedGoal = findGoalById(selectedGoal.value.id);
      if (updatedGoal) {
        selectedGoal.value = updatedGoal;
      }
    }
  } catch (error) {
    console.error('Error updating metric:', error);
    alert('Failed to update. Please try again.');
  } finally {
    updatingMetric.value = false;
  }
};

const submitJournalUpdate = async () => {
  updatingJournal.value = true;

  try {
    await window.axios.post(`/api/journey/goals/${journalUpdateGoal.value.id}/updates`, {
      note: journalUpdateForm.value.note,
    });

    journalUpdateGoal.value = null;
    await fetchGoals();

    // Refresh the selected goal if it's open
    if (selectedGoal.value) {
      const updatedGoal = findGoalById(selectedGoal.value.id);
      if (updatedGoal) {
        selectedGoal.value = updatedGoal;
      }
    }
  } catch (error) {
    console.error('Error adding update:', error);
    alert('Failed to add update. Please try again.');
  } finally {
    updatingJournal.value = false;
  }
};

onMounted(() => {
  fetchGoals();
});
</script>
