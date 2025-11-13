<template>
  <AppLayout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Journey Navigation -->
      <JourneyNav />

      <div class="mb-8">
        <div class="flex justify-between items-start">
          <div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Weekly Review</h1>
            <p class="text-gray-600">Take 30 minutes to reflect, celebrate, and plan your week ahead</p>
          </div>
          <!-- Completion Progress -->
          <div class="bg-white border-2 border-gray-200 rounded-lg px-4 py-3">
            <div class="text-sm text-gray-600 mb-1">Completion</div>
            <div class="text-2xl font-bold text-green-600">{{ completionPercentage }}%</div>
            <div class="text-xs text-gray-500">{{ completedSections }}/5 sections</div>
          </div>
        </div>
      </div>

      <div class="flex gap-6">
        <!-- History Sidebar -->
        <div class="w-64 hidden lg:block">
          <div class="sticky top-8">
            <h3 class="text-sm font-bold text-gray-900 mb-3">📊 Review History</h3>
            <div class="space-y-2 max-h-[calc(100vh-12rem)] overflow-y-auto">
              <button
                v-for="entry in recentReviews"
                :key="entry.week_start_date"
                @click="selectWeek(entry.week_start_date)"
                :class="[
                  'w-full text-left px-3 py-2 rounded-lg border-2 transition',
                  weekStart === entry.week_start_date
                    ? 'border-green-500 bg-green-50'
                    : 'border-gray-200 hover:border-green-300 bg-white'
                ]"
              >
                <div class="flex items-center justify-between">
                  <div>
                    <div class="text-sm font-medium text-gray-900">
                      Week of {{ formatDateShort(entry.week_start_date) }}
                    </div>
                    <div class="text-xs text-gray-500">
                      {{ entry.total_check_ins || 0 }}/7 check-ins
                    </div>
                  </div>
                  <div class="text-lg">
                    {{ entry.biggest_win ? '✅' : '📝' }}
                  </div>
                </div>
              </button>
              <div v-if="recentReviews.length === 0" class="text-sm text-gray-500 text-center py-4">
                No reviews yet
              </div>
            </div>
          </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1 max-w-4xl">
          <!-- Week Navigation -->
          <div class="mb-6 flex items-center gap-3">
            <button
              @click="navigateWeek(-1)"
              class="px-3 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 text-gray-700"
            >
              ← Previous Week
            </button>
            <div class="flex items-center gap-2">
              <label class="text-sm font-semibold text-gray-700">Week:</label>
              <input
                v-model="weekStart"
                @change="loadReview"
                type="date"
                class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500"
              />
              <span class="text-sm text-gray-600">→ {{ weekEnd }}</span>
            </div>
            <button
              @click="navigateWeek(1)"
              :disabled="isCurrentWeek"
              :class="[
                'px-3 py-2 border border-gray-300 rounded-lg text-gray-700',
                isCurrentWeek ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-50'
              ]"
            >
              Next Week →
            </button>
          </div>

      <!-- Memory Jogger -->
      <div class="mb-6 bg-gradient-to-r from-blue-50 to-green-50 border-2 border-blue-200 rounded-xl p-5">
        <div class="flex items-start justify-between mb-3">
          <div>
            <h3 class="text-lg font-bold text-gray-900 mb-1">🔍 Your Week at a Glance</h3>
            <p class="text-sm text-gray-600">Quick recap to jog your memory before reflecting</p>
          </div>
          <button
            @click="showMemoryJogger = !showMemoryJogger"
            class="text-sm text-blue-600 hover:text-blue-700 font-medium"
          >
            {{ showMemoryJogger ? 'Hide' : 'Show' }}
          </button>
        </div>

        <div v-if="showMemoryJogger" class="space-y-3 text-sm">
          <div class="bg-white rounded-lg p-3 border border-blue-200">
            <div class="font-semibold text-gray-900 mb-1">📊 Activity This Week</div>
            <div class="text-gray-600">
              <p>• {{ review.total_check_ins || 0 }} daily check-ins completed</p>
              <p v-if="review.avg_mood">• Average mood: {{ review.avg_mood.toFixed(1) }}/10</p>
              <p v-if="review.avg_energy">• Average energy: {{ review.avg_energy.toFixed(1) }}/10</p>
            </div>
          </div>

          <div class="bg-white rounded-lg p-3 border border-blue-200">
            <div class="font-semibold text-gray-900 mb-2">💡 Reflection Prompts</div>
            <div class="text-gray-600 space-y-1">
              <p>→ What did you work on most this week?</p>
              <p>→ What conversations or interactions stood out?</p>
              <p>→ What surprised you (good or bad)?</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Goal Progress This Week -->
      <div v-if="goalsWorkedOn.length > 0" class="mb-6 bg-gradient-to-r from-purple-50 to-blue-50 rounded-xl border-2 border-purple-200 p-5">
        <h3 class="text-lg font-bold text-gray-900 mb-3">🎯 Goal Progress This Week</h3>
        <p class="text-sm text-gray-600 mb-4">Goals you worked on this week based on your daily check-ins</p>

        <div class="space-y-3">
          <div
            v-for="goal in goalsWorkedOn"
            :key="goal.id"
            class="bg-white rounded-lg border border-purple-200 p-4"
          >
            <div class="flex items-start justify-between mb-2">
              <div class="flex-1">
                <div class="font-semibold text-gray-900">{{ goal.title }}</div>
                <div class="text-xs text-gray-500 mt-1">
                  {{ formatTimeHorizon(goal.time_horizon) }} • {{ goal.life_area }}
                </div>
              </div>
              <div class="text-right">
                <div class="text-sm font-bold text-purple-600">{{ goal.days_worked }} days</div>
                <div class="text-xs text-gray-500">this week</div>
              </div>
            </div>

            <div v-if="goal.tracking_type === 'metric'" class="mt-3">
              <div class="flex justify-between text-xs text-gray-600 mb-1">
                <span>Progress</span>
                <span class="font-semibold">{{ goal.metric_progress_percentage }}%</span>
              </div>
              <div class="w-full bg-gray-200 rounded-full h-2">
                <div
                  class="bg-purple-600 h-2 rounded-full transition-all"
                  :style="{ width: goal.metric_progress_percentage + '%' }"
                ></div>
              </div>
            </div>

            <div v-else-if="goal.tracking_type === 'milestone' && goal.milestones" class="mt-3">
              <div class="text-xs text-gray-600">
                {{ goal.milestones.filter(m => m.completed).length }}/{{ goal.milestones.length }} milestones completed
              </div>
            </div>
          </div>
        </div>

        <div class="mt-4 p-3 bg-white rounded-lg border border-purple-200 text-sm">
          <div class="font-semibold text-gray-900 mb-1">📊 Weekly Goal Summary</div>
          <div class="text-gray-600">
            You worked on <span class="font-bold text-purple-600">{{ goalsWorkedOn.length }} goal{{ goalsWorkedOn.length > 1 ? 's' : '' }}</span> across
            <span class="font-bold text-purple-600">{{ totalDaysWorkedOnGoals }} day{{ totalDaysWorkedOnGoals > 1 ? 's' : '' }}</span> this week.
          </div>
        </div>
      </div>

      <div class="space-y-6">
        <!-- Biggest Win -->
        <div class="bg-white rounded-xl border-2 border-gray-200 p-6 hover:border-green-300 transition">
          <div class="flex items-start justify-between mb-3">
            <div class="flex-1">
              <div class="flex items-center gap-2 mb-1">
                <span class="text-xs font-bold text-gray-500 bg-gray-100 px-2 py-1 rounded">1/5</span>
                <h3 class="text-lg font-bold text-gray-900">🏆 Biggest Win</h3>
              </div>
              <p class="text-sm text-gray-600 mb-2">What are you most proud of accomplishing this week?</p>
              <p class="text-xs text-blue-600 italic">💡 Why: Celebrating wins builds momentum and reinforces positive patterns</p>
            </div>
            <button
              @click="showExamples.biggestWin = !showExamples.biggestWin"
              class="text-xs text-gray-500 hover:text-gray-700 underline ml-4"
            >
              {{ showExamples.biggestWin ? 'Hide' : 'Examples' }}
            </button>
          </div>

          <div v-if="showExamples.biggestWin" class="mb-3 text-xs bg-gray-50 border border-gray-200 rounded-lg p-3">
            <p class="font-semibold text-gray-900 mb-2">Examples of good wins:</p>
            <div class="text-gray-600 space-y-1">
              <p>✓ "Launched the new checkout flow - 3 months of work finally shipped!"</p>
              <p>✓ "Had a tough conversation with my manager about workload. Feel heard."</p>
              <p>✓ "Ran 4 times this week even when I didn't feel like it"</p>
            </div>
          </div>

          <div class="mb-2 text-xs text-gray-500 space-y-0.5">
            <p>Consider: What went better than expected? What feedback did you get? What progress happened?</p>
          </div>

          <TextareaWithEmoji
            v-model="review.biggest_win"
            @blur="saveReview"
            rows="3"
            placeholder="Be specific about what you accomplished and why it matters..."
          />
        </div>

        <!-- Biggest Challenge -->
        <div class="bg-white rounded-xl border-2 border-gray-200 p-6 hover:border-green-300 transition">
          <div class="flex items-start justify-between mb-3">
            <div class="flex-1">
              <div class="flex items-center gap-2 mb-1">
                <span class="text-xs font-bold text-gray-500 bg-gray-100 px-2 py-1 rounded">2/5</span>
                <h3 class="text-lg font-bold text-gray-900">🤔 Biggest Challenge</h3>
              </div>
              <p class="text-sm text-gray-600 mb-2">What was difficult this week? What did it teach you?</p>
              <p class="text-xs text-blue-600 italic">💡 Why: Reflecting on challenges helps you learn and avoid repeating mistakes</p>
            </div>
            <button
              @click="showExamples.biggestChallenge = !showExamples.biggestChallenge"
              class="text-xs text-gray-500 hover:text-gray-700 underline ml-4"
            >
              {{ showExamples.biggestChallenge ? 'Hide' : 'Examples' }}
            </button>
          </div>

          <div v-if="showExamples.biggestChallenge" class="mb-3 text-xs bg-gray-50 border border-gray-200 rounded-lg p-3">
            <p class="font-semibold text-gray-900 mb-2">Examples of good reflections:</p>
            <div class="text-gray-600 space-y-1">
              <p>✓ "Overcommitted and missed my workout goals. Need to be more realistic in planning."</p>
              <p>✓ "Client changed requirements mid-sprint. Learned to document scope earlier."</p>
              <p>✓ "Struggled with procrastination on the proposal. Breaking it into smaller pieces helped."</p>
            </div>
          </div>

          <div class="mb-2 text-xs text-gray-500 space-y-0.5">
            <p>Consider: What frustrated you? What would you do differently? What help do you need?</p>
          </div>

          <TextareaWithEmoji
            v-model="review.biggest_challenge"
            @blur="saveReview"
            rows="3"
            placeholder="Describe the challenge and what you learned or plan to change..."
          />
        </div>

        <!-- What I Learned -->
        <div class="bg-white rounded-xl border-2 border-gray-200 p-6 hover:border-green-300 transition">
          <div class="flex items-start justify-between mb-3">
            <div class="flex-1">
              <div class="flex items-center gap-2 mb-1">
                <span class="text-xs font-bold text-gray-500 bg-gray-100 px-2 py-1 rounded">3/5</span>
                <h3 class="text-lg font-bold text-gray-900">💡 What I Learned</h3>
              </div>
              <p class="text-sm text-gray-600 mb-2">What insights, patterns, or lessons emerged this week?</p>
              <p class="text-xs text-blue-600 italic">💡 Why: Capturing lessons helps you build self-awareness and improve faster</p>
            </div>
            <button
              @click="showExamples.whatILearned = !showExamples.whatILearned"
              class="text-xs text-gray-500 hover:text-gray-700 underline ml-4"
            >
              {{ showExamples.whatILearned ? 'Hide' : 'Examples' }}
            </button>
          </div>

          <div v-if="showExamples.whatILearned" class="mb-3 text-xs bg-gray-50 border border-gray-200 rounded-lg p-3">
            <p class="font-semibold text-gray-900 mb-2">Examples of insights:</p>
            <div class="text-gray-600 space-y-1">
              <p>✓ "Deep work sessions before 10am produce my best code. Afternoons for meetings."</p>
              <p>✓ "Saying 'no' to small requests freed up time for the big project"</p>
              <p>✓ "Walking before writing helps me think more clearly"</p>
            </div>
          </div>

          <div class="mb-2 text-xs text-gray-500 space-y-0.5">
            <p>Consider: What patterns did you notice? What worked well? What didn't? What surprised you?</p>
          </div>

          <TextareaWithEmoji
            v-model="review.what_i_learned"
            @blur="saveReview"
            rows="3"
            placeholder="Share insights about yourself, your work style, or what's working..."
          />
        </div>

        <!-- Vision Alignment -->
        <div class="bg-white rounded-xl border-2 border-gray-200 p-6 hover:border-green-300 transition">
          <div class="flex items-start justify-between mb-3">
            <div class="flex-1">
              <div class="flex items-center gap-2 mb-1">
                <span class="text-xs font-bold text-gray-500 bg-gray-100 px-2 py-1 rounded">4/5</span>
                <h3 class="text-lg font-bold text-gray-900">🎯 Vision Alignment</h3>
              </div>
              <p class="text-sm text-gray-600 mb-2">Are your daily actions moving you toward your long-term vision?</p>
              <p class="text-xs text-blue-600 italic">💡 Why: Regular alignment checks prevent drifting off course</p>
            </div>
            <button
              @click="showExamples.visionAlignment = !showExamples.visionAlignment"
              class="text-xs text-gray-500 hover:text-gray-700 underline ml-4"
            >
              {{ showExamples.visionAlignment ? 'Hide' : 'Help' }}
            </button>
          </div>

          <div v-if="showExamples.visionAlignment" class="mb-3 text-xs bg-gray-50 border border-gray-200 rounded-lg p-3">
            <p class="font-semibold text-gray-900 mb-2">How to rate alignment:</p>
            <div class="text-gray-600 space-y-1">
              <p>• 8-10: Week directly supported major goals, felt purposeful</p>
              <p>• 5-7: Some progress but also distractions or busy work</p>
              <p>• 1-4: Mostly reactive, not working toward what matters</p>
              <p class="mt-2 text-blue-600">→ If consistently below 7, something needs to change</p>
            </div>
          </div>

          <div class="flex justify-between items-center mb-2">
            <span class="text-sm text-gray-600">Off track</span>
            <span class="text-2xl font-bold text-green-600">{{ review.vision_alignment || 5 }}/10</span>
            <span class="text-sm text-gray-600">Fully aligned</span>
          </div>
          <input
            v-model.number="review.vision_alignment"
            @change="saveReview"
            type="range"
            min="1"
            max="10"
            class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-green-600"
          />
          <div class="mt-2 text-xs text-gray-500">
            <p>Consider: Did this week move you closer to your 3-year vision? Which goals did you progress?</p>
          </div>
        </div>

        <!-- Next Week Focus -->
        <div class="bg-white rounded-xl border-2 border-gray-200 p-6 hover:border-green-300 transition">
          <div class="flex items-start justify-between mb-3">
            <div class="flex-1">
              <div class="flex items-center gap-2 mb-1">
                <span class="text-xs font-bold text-gray-500 bg-gray-100 px-2 py-1 rounded">5/5</span>
                <h3 class="text-lg font-bold text-gray-900">📅 Next Week's Top Priorities</h3>
              </div>
              <p class="text-sm text-gray-600 mb-2">What are the 3 most important outcomes to achieve next week?</p>
              <p class="text-xs text-blue-600 italic">💡 Why: Choosing just 3 priorities forces focus on what truly matters</p>
            </div>
            <button
              @click="showExamples.nextWeekFocus = !showExamples.nextWeekFocus"
              class="text-xs text-gray-500 hover:text-gray-700 underline ml-4"
            >
              {{ showExamples.nextWeekFocus ? 'Hide' : 'Examples' }}
            </button>
          </div>

          <div v-if="showExamples.nextWeekFocus" class="mb-3 text-xs bg-gray-50 border border-gray-200 rounded-lg p-3">
            <p class="font-semibold text-gray-900 mb-2">Examples of clear priorities:</p>
            <div class="text-gray-600 space-y-1">
              <p>✓ "1. Ship v2 dashboard to staging and get team feedback"</p>
              <p>✓ "2. Run 4x this week (Mon/Wed/Fri/Sun morning)"</p>
              <p>✓ "3. Have budget conversation with partner and agree on plan"</p>
              <p class="mt-2 text-amber-600">✗ "Work on project" (too vague)</p>
              <p class="text-amber-600">✗ "Be healthier" (not measurable)</p>
            </div>
          </div>

          <div class="mb-2 text-xs text-gray-500 space-y-0.5">
            <p>Make it specific: What done looks like? When will you do it? Focus on outcomes, not just tasks.</p>
          </div>

          <TextareaWithEmoji
            v-model="review.next_week_focus"
            @blur="saveReview"
            rows="4"
            placeholder="1. [First priority - be specific about the outcome]
2. [Second priority]
3. [Third priority]

Keep it realistic - what can you actually accomplish?"
          />
        </div>
      </div>

          <!-- Save Status -->
          <div v-if="saving" class="fixed bottom-4 right-4 bg-green-600 text-white px-4 py-2 rounded-lg shadow-lg">
            Saving...
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import AppLayout from '../Layouts/AppLayout.vue';
import JourneyNav from '../Components/journey/JourneyNav.vue';
import TextareaWithEmoji from '../Components/TextareaWithEmoji.vue';
import { ref, computed, onMounted } from 'vue';

const getMonday = (date) => {
  const d = new Date(date);
  const day = d.getDay();
  const diff = d.getDate() - day + (day === 0 ? -6 : 1);
  return new Date(d.setDate(diff));
};

const weekStart = ref(getMonday(new Date()).toISOString().split('T')[0]);
const saving = ref(false);
const recentReviews = ref([]);
const showMemoryJogger = ref(false);
const goalsWorkedOn = ref([]);

const showExamples = ref({
  biggestWin: false,
  biggestChallenge: false,
  whatILearned: false,
  visionAlignment: false,
  nextWeekFocus: false,
});

const weekEnd = computed(() => {
  const end = new Date(weekStart.value);
  end.setDate(end.getDate() + 6);
  return end.toISOString().split('T')[0];
});

const isCurrentWeek = computed(() => {
  const currentWeekStart = getMonday(new Date()).toISOString().split('T')[0];
  return weekStart.value === currentWeekStart;
});

const completedSections = computed(() => {
  let count = 0;
  if (review.value.biggest_win && review.value.biggest_win.trim()) count++;
  if (review.value.biggest_challenge && review.value.biggest_challenge.trim()) count++;
  if (review.value.what_i_learned && review.value.what_i_learned.trim()) count++;
  if (review.value.vision_alignment && review.value.vision_alignment > 0) count++;
  if (review.value.next_week_focus && review.value.next_week_focus.trim()) count++;
  return count;
});

const completionPercentage = computed(() => {
  return Math.round((completedSections.value / 5) * 100);
});

const totalDaysWorkedOnGoals = computed(() => {
  return goalsWorkedOn.value.reduce((total, goal) => total + goal.days_worked, 0);
});

const review = ref({
  week_start_date: weekStart.value,
  week_end_date: weekEnd.value,
  biggest_win: '',
  biggest_challenge: '',
  what_i_learned: '',
  vision_alignment: 5,
  next_week_focus: '',
});

const loadGoalsWorkedOn = async () => {
  try {
    // Get daily check-ins for this week
    const weekEndDate = weekEnd.value;
    const response = await window.axios.get('/api/journey/daily', {
      params: {
        from: weekStart.value,
        to: weekEndDate
      }
    });

    const dailyCheckIns = response.data;

    // Extract all unique goal IDs and count days worked
    const goalDays = {};
    dailyCheckIns.forEach(checkIn => {
      if (checkIn.goals_worked_on && Array.isArray(checkIn.goals_worked_on)) {
        checkIn.goals_worked_on.forEach(goalId => {
          if (!goalDays[goalId]) {
            goalDays[goalId] = 0;
          }
          goalDays[goalId]++;
        });
      }
    });

    // Fetch the actual goal data
    if (Object.keys(goalDays).length > 0) {
      const goalsResponse = await window.axios.get('/api/journey/goals');
      const allGoals = Object.values(goalsResponse.data).flat();

      goalsWorkedOn.value = Object.keys(goalDays).map(goalId => {
        const goal = allGoals.find(g => g.id === parseInt(goalId));
        if (goal) {
          return {
            ...goal,
            days_worked: goalDays[goalId]
          };
        }
        return null;
      }).filter(g => g !== null);
    } else {
      goalsWorkedOn.value = [];
    }
  } catch (error) {
    console.error('Error loading goals worked on:', error);
  }
};

const loadReview = async () => {
  try {
    const response = await window.axios.get(`/api/journey/weekly/${weekStart.value}`);
    review.value = response.data;
    await loadGoalsWorkedOn();
  } catch (error) {
    console.error('Error loading review:', error);
  }
};

const loadRecentReviews = async () => {
  try {
    const response = await window.axios.get('/api/journey/weekly');
    recentReviews.value = response.data;
  } catch (error) {
    console.error('Error loading recent reviews:', error);
  }
};

const saveReview = async () => {
  saving.value = true;

  try {
    review.value.week_start_date = weekStart.value;
    review.value.week_end_date = weekEnd.value;
    await window.axios.post('/api/journey/weekly', review.value);
    await loadRecentReviews(); // Refresh the history
    setTimeout(() => { saving.value = false; }, 500);
  } catch (error) {
    console.error('Error saving review:', error);
    saving.value = false;
  }
};

const selectWeek = (date) => {
  weekStart.value = date;
  loadReview();
};

const navigateWeek = (weeks) => {
  const current = new Date(weekStart.value);
  current.setDate(current.getDate() + (weeks * 7));
  weekStart.value = getMonday(current).toISOString().split('T')[0];
  loadReview();
};

const formatDateShort = (dateString) => {
  const date = new Date(dateString);
  return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
};

const formatTimeHorizon = (horizon) => {
  const labels = {
    '3_year': '3-Year',
    'yearly': 'Yearly',
    'quarterly': 'Quarterly',
    'monthly': 'Monthly',
  };
  return labels[horizon] || horizon;
};

onMounted(() => {
  loadReview();
  loadRecentReviews();
});
</script>
