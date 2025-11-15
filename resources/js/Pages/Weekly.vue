<template>
  <AppLayout>
    <div class="max-w-7xl mx-auto px-3 sm:px-4 md:px-6 lg:px-8 py-4 sm:py-8">
      <!-- Journey Navigation -->
      <JourneyNav />

      <div class="mb-4 sm:mb-8">
        <div class="flex justify-between items-start gap-2">
          <div class="flex-1 min-w-0">
            <h1 class="text-xl sm:text-3xl font-bold text-gray-900 dark:text-white mb-1 sm:mb-2">Weekly Review</h1>
            <p class="text-xs sm:text-base text-gray-600 dark:text-gray-400">Take 30 minutes to reflect, celebrate, and plan your week ahead</p>
          </div>
          <!-- Completion Progress -->
          <div class="bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-lg px-2 py-1.5 sm:px-3 sm:py-2 flex-shrink-0 flex items-center gap-1.5 sm:gap-2">
            <div class="text-lg sm:text-xl font-bold text-green-600 dark:text-green-500">{{ completionPercentage }}%</div>
            <div class="text-[9px] sm:text-xs text-gray-500 dark:text-gray-400">{{ completedSections }}/5</div>
          </div>
        </div>
      </div>

      <!-- Mobile History Button -->
      <button
        @click="showMobileHistory = true"
        class="lg:hidden mb-3 w-full px-3 py-2 bg-white dark:bg-gray-800 border-2 border-gray-200 dark:border-gray-700 rounded-lg hover:border-green-300 dark:hover:border-green-600 transition text-left flex items-center justify-between"
      >
        <span class="text-xs sm:text-sm font-medium text-gray-900 dark:text-white">📊 Review History</span>
        <span class="text-xs sm:text-sm text-gray-600 dark:text-gray-400">{{ recentReviews.length }} reviews</span>
      </button>

      <div class="flex gap-4 sm:gap-6">
        <!-- History Sidebar (Desktop) -->
        <div class="w-64 hidden lg:block">
          <div class="sticky top-8">
            <div class="flex items-center justify-between mb-3">
              <h3 class="text-sm font-bold text-gray-900 dark:text-white">📊 Review History</h3>
              <span v-if="recentReviews.length > 0" class="text-xs text-gray-500 dark:text-gray-400">{{ recentReviews.length }}</span>
            </div>
            <div class="relative">
              <div class="space-y-3 max-h-[calc(100vh-12rem)] overflow-y-auto pr-1">
                <template v-for="(entry, index) in recentReviews" :key="entry.week_start_date">
                  <!-- Year separator -->
                  <div v-if="index === 0 || getYear(entry.week_start_date) !== getYear(recentReviews[index - 1].week_start_date)" class="sticky top-0 bg-white dark:bg-gray-900 pt-3 pb-2 z-10 -mx-1 px-1">
                    <div class="flex items-center gap-2">
                      <div class="flex-1 h-px bg-gradient-to-r from-transparent via-gray-300 dark:via-gray-700 to-transparent"></div>
                      <div class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wide px-2">{{ getYear(entry.week_start_date) }}</div>
                      <div class="flex-1 h-px bg-gradient-to-r from-transparent via-gray-300 dark:via-gray-700 to-transparent"></div>
                    </div>
                  </div>
                  <button
                    @click="selectWeek(entry.week_start_date)"
                    :class="[
                      'w-full text-left px-3 py-2 rounded-lg border-2 transition',
                      weekStart === entry.week_start_date
                        ? 'border-green-500 dark:border-green-600 bg-green-50 dark:bg-green-950/30'
                        : 'border-gray-200 dark:border-gray-700 hover:border-green-300 dark:hover:border-green-600 bg-white dark:bg-gray-800'
                    ]"
                  >
                    <div class="flex items-center justify-between">
                      <div>
                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                          Week of {{ formatDateShort(entry.week_start_date) }}
                        </div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">
                          {{ entry.total_check_ins || 0 }}/7 check-ins
                        </div>
                      </div>
                      <div class="text-lg">
                        {{ entry.biggest_win ? '✅' : '📝' }}
                      </div>
                    </div>
                  </button>
                </template>
                <div v-if="recentReviews.length === 0" class="text-sm text-gray-500 dark:text-gray-400 text-center py-4">
                  No reviews yet
                </div>
              </div>
              <!-- Scroll fade indicator -->
              <div v-if="recentReviews.length > 5" class="absolute bottom-0 left-0 right-0 h-8 bg-gradient-to-t from-white dark:from-gray-900 to-transparent pointer-events-none"></div>
            </div>
          </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1 max-w-4xl">
          <!-- Week Navigation -->
          <div class="mb-4 sm:mb-6 flex flex-col xl:flex-row xl:items-center gap-2 sm:gap-3">
            <div class="flex gap-2 sm:gap-3">
              <button
                @click="navigateWeek(-1)"
                class="flex-1 xl:flex-none px-2 sm:px-3 py-1.5 sm:py-2 border border-gray-300 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 text-gray-700 dark:text-gray-300 text-xs sm:text-sm"
              >
                <span class="hidden sm:inline">← Previous Week</span>
                <span class="sm:hidden">← Prev</span>
              </button>
              <button
                @click="navigateWeek(1)"
                :disabled="isCurrentWeek"
                :class="[
                  'flex-1 xl:flex-none px-2 sm:px-3 py-1.5 sm:py-2 border border-gray-300 dark:border-gray-700 rounded-lg text-gray-700 dark:text-gray-300 text-xs sm:text-sm',
                  isCurrentWeek ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-50 dark:hover:bg-gray-800'
                ]"
              >
                <span class="hidden sm:inline">Next Week →</span>
                <span class="sm:hidden">Next →</span>
              </button>
            </div>
            <div class="flex items-center gap-1.5 sm:gap-2 w-full xl:w-auto">
              <label class="text-xs sm:text-sm font-semibold text-gray-700 dark:text-gray-300 whitespace-nowrap">Week:</label>
              <input
                v-model="weekStart"
                @change="loadReview"
                type="date"
                class="flex-1 px-2 sm:px-4 py-1.5 sm:py-2 border border-gray-300 dark:border-gray-700 rounded-lg focus:ring-2 focus:ring-green-500 dark:focus:ring-green-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white text-xs sm:text-sm"
              />
              <span class="hidden xl:inline text-sm text-gray-600 dark:text-gray-400">→ {{ weekEnd }}</span>
            </div>
          </div>

      <!-- Memory Jogger -->
      <div class="mb-4 sm:mb-6 bg-gradient-to-r from-blue-50 to-green-50 dark:from-blue-950/30 dark:to-green-950/30 border-2 border-blue-200 dark:border-blue-800 rounded-xl p-3 sm:p-5">
        <div class="flex items-start justify-between mb-2 sm:mb-3 gap-2">
          <div class="flex-1 min-w-0">
            <h3 class="text-base sm:text-lg font-bold text-gray-900 dark:text-white mb-0.5 sm:mb-1">🔍 Your Week at a Glance</h3>
            <p class="text-xs sm:text-sm text-gray-600 dark:text-gray-400">Quick recap to jog your memory before reflecting</p>
          </div>
          <button
            @click="showMemoryJogger = !showMemoryJogger"
            class="text-xs sm:text-sm text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-medium flex-shrink-0"
          >
            {{ showMemoryJogger ? 'Hide' : 'Show' }}
          </button>
        </div>

        <div v-if="showMemoryJogger" class="space-y-2 sm:space-y-3 text-xs sm:text-sm">
          <div class="bg-white dark:bg-gray-800 rounded-lg p-2 sm:p-3 border border-blue-200 dark:border-blue-800">
            <div class="font-semibold text-gray-900 dark:text-white mb-1">📊 Activity This Week</div>
            <div class="text-gray-600 dark:text-gray-400">
              <p>• {{ review.total_check_ins || 0 }} daily check-ins completed</p>
              <p v-if="review.avg_mood">• Average mood: {{ review.avg_mood.toFixed(1) }}/10</p>
              <p v-if="review.avg_energy">• Average energy: {{ review.avg_energy.toFixed(1) }}/10</p>
            </div>
          </div>

          <div class="bg-white dark:bg-gray-800 rounded-lg p-2 sm:p-3 border border-blue-200 dark:border-blue-800">
            <div class="font-semibold text-gray-900 dark:text-white mb-1 sm:mb-2">💡 Reflection Prompts</div>
            <div class="text-gray-600 dark:text-gray-400 space-y-1">
              <p>→ What did you work on most this week?</p>
              <p>→ What conversations or interactions stood out?</p>
              <p>→ What surprised you (good or bad)?</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Goal Progress This Week -->
      <div v-if="goalsWorkedOn.length > 0" class="mb-4 sm:mb-6 bg-gradient-to-r from-purple-50 to-blue-50 dark:from-purple-950/30 dark:to-blue-950/30 rounded-xl border-2 border-purple-200 dark:border-purple-800 p-3 sm:p-5">
        <h3 class="text-base sm:text-lg font-bold text-gray-900 dark:text-white mb-2 sm:mb-3">🎯 Goal Progress This Week</h3>
        <p class="text-xs sm:text-sm text-gray-600 dark:text-gray-400 mb-3 sm:mb-4">Goals you worked on this week based on your daily check-ins</p>

        <div class="space-y-2 sm:space-y-3">
          <div
            v-for="goal in goalsWorkedOn"
            :key="goal.id"
            class="bg-white dark:bg-gray-800 rounded-lg border border-purple-200 dark:border-purple-800 p-2.5 sm:p-4"
          >
            <div class="flex items-start justify-between mb-1.5 sm:mb-2 gap-2">
              <div class="flex-1 min-w-0">
                <div class="font-semibold text-gray-900 dark:text-white text-xs sm:text-base">{{ goal.title }}</div>
                <div class="text-[10px] sm:text-xs text-gray-500 dark:text-gray-400 mt-0.5 sm:mt-1">
                  {{ formatTimeHorizon(goal.time_horizon) }} • {{ goal.life_area }}
                </div>
              </div>
              <div class="text-right flex-shrink-0">
                <div class="text-xs sm:text-sm font-bold text-purple-600 dark:text-purple-400">{{ goal.days_worked }} days</div>
                <div class="text-[10px] sm:text-xs text-gray-500 dark:text-gray-400">this week</div>
              </div>
            </div>

            <div v-if="goal.tracking_type === 'metric'" class="mt-2 sm:mt-3">
              <div class="flex justify-between text-[10px] sm:text-xs text-gray-600 dark:text-gray-400 mb-1">
                <span>Progress</span>
                <span class="font-semibold">{{ goal.metric_progress_percentage }}%</span>
              </div>
              <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-1.5 sm:h-2">
                <div
                  class="bg-purple-600 dark:bg-purple-500 h-1.5 sm:h-2 rounded-full transition-all"
                  :style="{ width: goal.metric_progress_percentage + '%' }"
                ></div>
              </div>
            </div>

            <div v-else-if="goal.tracking_type === 'milestone' && goal.milestones" class="mt-2 sm:mt-3">
              <div class="text-[10px] sm:text-xs text-gray-600 dark:text-gray-400">
                {{ goal.milestones.filter(m => m.completed).length }}/{{ goal.milestones.length }} milestones completed
              </div>
            </div>
          </div>
        </div>

        <div class="mt-3 sm:mt-4 p-2 sm:p-3 bg-white dark:bg-gray-800 rounded-lg border border-purple-200 dark:border-purple-800 text-xs sm:text-sm">
          <div class="font-semibold text-gray-900 dark:text-white mb-1">📊 Weekly Goal Summary</div>
          <div class="text-gray-600 dark:text-gray-400">
            You worked on <span class="font-bold text-purple-600 dark:text-purple-400">{{ goalsWorkedOn.length }} goal{{ goalsWorkedOn.length > 1 ? 's' : '' }}</span> across
            <span class="font-bold text-purple-600 dark:text-purple-400">{{ totalDaysWorkedOnGoals }} day{{ totalDaysWorkedOnGoals > 1 ? 's' : '' }}</span> this week.
          </div>
        </div>
      </div>

      <div class="space-y-4 sm:space-y-6">
        <!-- Biggest Win -->
        <div class="bg-white dark:bg-gray-800 rounded-xl border-2 border-gray-200 dark:border-gray-700 p-3 sm:p-6 hover:border-green-300 dark:hover:border-green-600 transition">
          <div class="flex items-start justify-between mb-2 sm:mb-3 gap-2">
            <div class="flex-1 min-w-0">
              <div class="flex items-center gap-1.5 sm:gap-2 mb-1">
                <span class="text-[10px] sm:text-xs font-bold text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-gray-700 px-1.5 py-0.5 sm:px-2 sm:py-1 rounded">1/5</span>
                <h3 class="text-base sm:text-lg font-bold text-gray-900 dark:text-white">🏆 Biggest Win</h3>
              </div>
              <p class="text-xs sm:text-sm text-gray-600 dark:text-gray-400 mb-1 sm:mb-2">What are you most proud of accomplishing this week?</p>
              <p class="text-[10px] sm:text-xs text-blue-600 dark:text-blue-400 italic">💡 Why: Celebrating wins builds momentum and reinforces positive patterns</p>
            </div>
            <button
              @click="showExamples.biggestWin = !showExamples.biggestWin"
              class="text-[10px] sm:text-xs text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 underline flex-shrink-0"
            >
              {{ showExamples.biggestWin ? 'Hide' : 'Examples' }}
            </button>
          </div>

          <div v-if="showExamples.biggestWin" class="mb-2 sm:mb-3 text-[10px] sm:text-xs bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg p-2 sm:p-3">
            <p class="font-semibold text-gray-900 dark:text-white mb-1.5 sm:mb-2">Examples of good wins:</p>
            <div class="text-gray-600 dark:text-gray-400 space-y-1">
              <p>✓ "Launched the new checkout flow - 3 months of work finally shipped!"</p>
              <p>✓ "Had a tough conversation with my manager about workload. Feel heard."</p>
              <p>✓ "Ran 4 times this week even when I didn't feel like it"</p>
            </div>
          </div>

          <div class="mb-1.5 sm:mb-2 text-[10px] sm:text-xs text-gray-500 dark:text-gray-400 space-y-0.5">
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
        <div class="bg-white dark:bg-gray-800 rounded-xl border-2 border-gray-200 dark:border-gray-700 p-3 sm:p-6 hover:border-green-300 dark:hover:border-green-600 transition">
          <div class="flex items-start justify-between mb-2 sm:mb-3 gap-2">
            <div class="flex-1 min-w-0">
              <div class="flex items-center gap-1.5 sm:gap-2 mb-1">
                <span class="text-[10px] sm:text-xs font-bold text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-gray-700 px-1.5 py-0.5 sm:px-2 sm:py-1 rounded">2/5</span>
                <h3 class="text-base sm:text-lg font-bold text-gray-900 dark:text-white">🤔 Biggest Challenge</h3>
              </div>
              <p class="text-xs sm:text-sm text-gray-600 dark:text-gray-400 mb-1 sm:mb-2">What was difficult this week? What did it teach you?</p>
              <p class="text-[10px] sm:text-xs text-blue-600 dark:text-blue-400 italic">💡 Why: Reflecting on challenges helps you learn and avoid repeating mistakes</p>
            </div>
            <button
              @click="showExamples.biggestChallenge = !showExamples.biggestChallenge"
              class="text-[10px] sm:text-xs text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 underline flex-shrink-0"
            >
              {{ showExamples.biggestChallenge ? 'Hide' : 'Examples' }}
            </button>
          </div>

          <div v-if="showExamples.biggestChallenge" class="mb-2 sm:mb-3 text-[10px] sm:text-xs bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg p-2 sm:p-3">
            <p class="font-semibold text-gray-900 dark:text-white mb-1.5 sm:mb-2">Examples of good reflections:</p>
            <div class="text-gray-600 dark:text-gray-400 space-y-1">
              <p>✓ "Overcommitted and missed my workout goals. Need to be more realistic in planning."</p>
              <p>✓ "Client changed requirements mid-sprint. Learned to document scope earlier."</p>
              <p>✓ "Struggled with procrastination on the proposal. Breaking it into smaller pieces helped."</p>
            </div>
          </div>

          <div class="mb-1.5 sm:mb-2 text-[10px] sm:text-xs text-gray-500 dark:text-gray-400 space-y-0.5">
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
        <div class="bg-white dark:bg-gray-800 rounded-xl border-2 border-gray-200 dark:border-gray-700 p-3 sm:p-6 hover:border-green-300 dark:hover:border-green-600 transition">
          <div class="flex items-start justify-between mb-2 sm:mb-3 gap-2">
            <div class="flex-1 min-w-0">
              <div class="flex items-center gap-1.5 sm:gap-2 mb-1">
                <span class="text-[10px] sm:text-xs font-bold text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-gray-700 px-1.5 py-0.5 sm:px-2 sm:py-1 rounded">3/5</span>
                <h3 class="text-base sm:text-lg font-bold text-gray-900 dark:text-white">💡 What I Learned</h3>
              </div>
              <p class="text-xs sm:text-sm text-gray-600 dark:text-gray-400 mb-1 sm:mb-2">What insights, patterns, or lessons emerged this week?</p>
              <p class="text-[10px] sm:text-xs text-blue-600 dark:text-blue-400 italic">💡 Why: Capturing lessons helps you build self-awareness and improve faster</p>
            </div>
            <button
              @click="showExamples.whatILearned = !showExamples.whatILearned"
              class="text-[10px] sm:text-xs text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 underline flex-shrink-0"
            >
              {{ showExamples.whatILearned ? 'Hide' : 'Examples' }}
            </button>
          </div>

          <div v-if="showExamples.whatILearned" class="mb-2 sm:mb-3 text-[10px] sm:text-xs bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg p-2 sm:p-3">
            <p class="font-semibold text-gray-900 dark:text-white mb-1.5 sm:mb-2">Examples of insights:</p>
            <div class="text-gray-600 dark:text-gray-400 space-y-1">
              <p>✓ "Deep work sessions before 10am produce my best code. Afternoons for meetings."</p>
              <p>✓ "Saying 'no' to small requests freed up time for the big project"</p>
              <p>✓ "Walking before writing helps me think more clearly"</p>
            </div>
          </div>

          <div class="mb-1.5 sm:mb-2 text-[10px] sm:text-xs text-gray-500 dark:text-gray-400 space-y-0.5">
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
        <div class="bg-white dark:bg-gray-800 rounded-xl border-2 border-gray-200 dark:border-gray-700 p-3 sm:p-6 hover:border-green-300 dark:hover:border-green-600 transition">
          <div class="flex items-start justify-between mb-2 sm:mb-3 gap-2">
            <div class="flex-1 min-w-0">
              <div class="flex items-center gap-1.5 sm:gap-2 mb-1">
                <span class="text-[10px] sm:text-xs font-bold text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-gray-700 px-1.5 py-0.5 sm:px-2 sm:py-1 rounded">4/5</span>
                <h3 class="text-base sm:text-lg font-bold text-gray-900 dark:text-white">🎯 Vision Alignment</h3>
              </div>
              <p class="text-xs sm:text-sm text-gray-600 dark:text-gray-400 mb-1 sm:mb-2">Are your daily actions moving you toward your long-term vision?</p>
              <p class="text-[10px] sm:text-xs text-blue-600 dark:text-blue-400 italic">💡 Why: Regular alignment checks prevent drifting off course</p>
            </div>
            <button
              @click="showExamples.visionAlignment = !showExamples.visionAlignment"
              class="text-[10px] sm:text-xs text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 underline flex-shrink-0"
            >
              {{ showExamples.visionAlignment ? 'Hide' : 'Help' }}
            </button>
          </div>

          <div v-if="showExamples.visionAlignment" class="mb-2 sm:mb-3 text-[10px] sm:text-xs bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg p-2 sm:p-3">
            <p class="font-semibold text-gray-900 dark:text-white mb-1.5 sm:mb-2">How to rate alignment:</p>
            <div class="text-gray-600 dark:text-gray-400 space-y-1">
              <p>• 8-10: Week directly supported major goals, felt purposeful</p>
              <p>• 5-7: Some progress but also distractions or busy work</p>
              <p>• 1-4: Mostly reactive, not working toward what matters</p>
              <p class="mt-1 sm:mt-2 text-blue-600 dark:text-blue-400">→ If consistently below 7, something needs to change</p>
            </div>
          </div>

          <div class="flex justify-between items-center mb-1.5 sm:mb-2 gap-1 sm:gap-2">
            <span class="text-[10px] sm:text-sm text-gray-600 dark:text-gray-400 flex-shrink-0 text-left">Off track</span>
            <span class="text-base sm:text-lg md:text-2xl font-bold text-green-600 dark:text-green-500 flex-shrink-0">{{ review.vision_alignment || 5 }}/10</span>
            <span class="text-[10px] sm:text-sm text-gray-600 dark:text-gray-400 flex-shrink-0 text-right">Fully aligned</span>
          </div>
          <input
            v-model.number="review.vision_alignment"
            @change="saveReview"
            type="range"
            min="1"
            max="10"
            class="w-full h-1.5 sm:h-2 bg-gray-200 dark:bg-gray-700 rounded-lg appearance-none cursor-pointer accent-green-600"
          />
          <div class="mt-1.5 sm:mt-2 text-[10px] sm:text-xs text-gray-500 dark:text-gray-400">
            <p>Consider: Did this week move you closer to your 3-year vision? Which goals did you progress?</p>
          </div>
        </div>

        <!-- Next Week Focus -->
        <div class="bg-white dark:bg-gray-800 rounded-xl border-2 border-gray-200 dark:border-gray-700 p-3 sm:p-6 hover:border-green-300 dark:hover:border-green-600 transition">
          <div class="flex items-start justify-between mb-2 sm:mb-3 gap-2">
            <div class="flex-1 min-w-0">
              <div class="flex items-center gap-1.5 sm:gap-2 mb-1">
                <span class="text-[10px] sm:text-xs font-bold text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-gray-700 px-1.5 py-0.5 sm:px-2 sm:py-1 rounded">5/5</span>
                <h3 class="text-base sm:text-lg font-bold text-gray-900 dark:text-white">📅 Next Week's Top Priorities</h3>
              </div>
              <p class="text-xs sm:text-sm text-gray-600 dark:text-gray-400 mb-1 sm:mb-2">What are the 3 most important outcomes to achieve next week?</p>
              <p class="text-[10px] sm:text-xs text-blue-600 dark:text-blue-400 italic">💡 Why: Choosing just 3 priorities forces focus on what truly matters</p>
            </div>
            <button
              @click="showExamples.nextWeekFocus = !showExamples.nextWeekFocus"
              class="text-[10px] sm:text-xs text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 underline flex-shrink-0"
            >
              {{ showExamples.nextWeekFocus ? 'Hide' : 'Examples' }}
            </button>
          </div>

          <div v-if="showExamples.nextWeekFocus" class="mb-2 sm:mb-3 text-[10px] sm:text-xs bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg p-2 sm:p-3">
            <p class="font-semibold text-gray-900 dark:text-white mb-1.5 sm:mb-2">Examples of clear priorities:</p>
            <div class="text-gray-600 dark:text-gray-400 space-y-1">
              <p>✓ "1. Ship v2 dashboard to staging and get team feedback"</p>
              <p>✓ "2. Run 4x this week (Mon/Wed/Fri/Sun morning)"</p>
              <p>✓ "3. Have budget conversation with partner and agree on plan"</p>
              <p class="mt-1 sm:mt-2 text-amber-600 dark:text-amber-500">✗ "Work on project" (too vague)</p>
              <p class="text-amber-600 dark:text-amber-500">✗ "Be healthier" (not measurable)</p>
            </div>
          </div>

          <div class="mb-1.5 sm:mb-2 text-[10px] sm:text-xs text-gray-500 dark:text-gray-400 space-y-0.5">
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
          <div v-if="saving" class="fixed bottom-3 sm:bottom-4 right-3 sm:right-4 bg-green-600 text-white px-3 py-1.5 sm:px-4 sm:py-2 rounded-lg shadow-lg text-xs sm:text-sm">
            Saving...
          </div>
        </div>
      </div>

      <!-- Mobile History Modal -->
      <div v-if="showMobileHistory" class="fixed inset-0 bg-gray-900/20 dark:bg-black/50 backdrop-blur-sm flex items-end sm:items-center justify-center z-50 sm:p-4 lg:hidden" @click.self="showMobileHistory = false">
        <div class="bg-white dark:bg-gray-800 rounded-t-2xl sm:rounded-xl max-w-md w-full h-[85vh] sm:max-h-[85vh] flex flex-col">
          <div class="flex-shrink-0 p-4 sm:p-6 pb-3 sm:pb-4 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-2">
                <h3 class="text-lg sm:text-xl font-bold text-gray-900 dark:text-white">📊 Review History</h3>
                <span v-if="recentReviews.length > 0" class="text-xs sm:text-sm text-gray-500 dark:text-gray-400">({{ recentReviews.length }})</span>
              </div>
              <button
                @click="showMobileHistory = false"
                class="text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300"
              >
                <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                </svg>
              </button>
            </div>
          </div>

          <div class="relative flex-1 min-h-0 overflow-hidden">
            <div class="overflow-y-scroll h-full p-4 sm:p-6 space-y-3" style="-webkit-overflow-scrolling: touch; overscroll-behavior: contain;">
              <template v-for="(entry, index) in recentReviews" :key="entry.week_start_date">
                <!-- Year separator -->
                <div v-if="index === 0 || getYear(entry.week_start_date) !== getYear(recentReviews[index - 1].week_start_date)" class="sticky top-0 bg-white dark:bg-gray-800 pt-3 pb-2 z-10">
                  <div class="flex items-center gap-2">
                    <div class="flex-1 h-px bg-gradient-to-r from-transparent via-gray-300 dark:via-gray-700 to-transparent"></div>
                    <div class="text-xs font-bold text-gray-600 dark:text-gray-400 uppercase tracking-wide px-2 bg-white dark:bg-gray-800">{{ getYear(entry.week_start_date) }}</div>
                    <div class="flex-1 h-px bg-gradient-to-r from-transparent via-gray-300 dark:via-gray-700 to-transparent"></div>
                  </div>
                </div>
                <button
                  @click="selectWeek(entry.week_start_date); showMobileHistory = false"
                  :class="[
                    'w-full text-left px-3 py-2 rounded-lg border-2 transition',
                    weekStart === entry.week_start_date
                      ? 'border-green-500 dark:border-green-600 bg-green-50 dark:bg-green-950/30'
                      : 'border-gray-200 dark:border-gray-700 hover:border-green-300 dark:hover:border-green-600 bg-white dark:bg-gray-800'
                  ]"
                >
                  <div class="flex items-center justify-between">
                    <div>
                      <div class="text-sm font-medium text-gray-900 dark:text-white">
                        Week of {{ formatDateShort(entry.week_start_date) }}
                      </div>
                      <div class="text-xs text-gray-500 dark:text-gray-400">
                        {{ entry.total_check_ins || 0 }}/7 check-ins
                      </div>
                    </div>
                    <div class="text-lg">
                      {{ entry.biggest_win ? '✅' : '📝' }}
                    </div>
                  </div>
                </button>
              </template>
              <div v-if="recentReviews.length === 0" class="text-sm text-gray-500 dark:text-gray-400 text-center py-8">
                No reviews yet
              </div>
            </div>
            <!-- Scroll fade indicator -->
            <div v-if="recentReviews.length > 8" class="absolute bottom-0 left-0 right-0 h-12 bg-gradient-to-t from-white dark:from-gray-800 to-transparent pointer-events-none"></div>
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
import { formatDateShort, formatTimeHorizon } from '../utils/dateFormatters';

const getMonday = (date) => {
  const d = new Date(date);
  const day = d.getDay();
  const diff = d.getDate() - day + (day === 0 ? -6 : 1);
  return new Date(d.setDate(diff));
};

const weekStart = ref(getMonday(new Date()).toISOString().split('T')[0]);
const saving = ref(false);
const recentReviews = ref([]);
const showMobileHistory = ref(false);
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
    const data = response.data;

    // Merge with defaults to ensure all fields exist and null values become empty strings
    review.value = {
      week_start_date: data.week_start_date || weekStart.value,
      week_end_date: data.week_end_date || weekEnd.value,
      biggest_win: data.biggest_win || '',
      biggest_challenge: data.biggest_challenge || '',
      what_i_learned: data.what_i_learned || '',
      vision_alignment: data.vision_alignment || 5,
      next_week_focus: data.next_week_focus || '',
      total_check_ins: data.total_check_ins || 0,
      total_wins: data.total_wins || 0,
      avg_energy: data.avg_energy || 0,
      avg_productivity: data.avg_productivity || 0,
      avg_mood: data.avg_mood || 0,
      goal_progress: data.goal_progress || [],
      next_week_goals: data.next_week_goals || [],
      notes: data.notes || '',
      is_public: data.is_public || false,
    };

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

const getYear = (dateString) => {
  return new Date(dateString).getFullYear();
};

onMounted(() => {
  loadReview();
  loadRecentReviews();
});
</script>
