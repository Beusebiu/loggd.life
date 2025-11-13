<template>
  <AppLayout>
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Journey Navigation -->
      <JourneyNav />

      <!-- Page Header -->
      <div class="mb-8">
        <div class="flex items-start justify-between">
          <div class="flex-1">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Your Life Compass</h1>
            <p class="text-gray-600">Define your north star - the life you're building toward</p>
            <p class="text-sm text-gray-500 mt-2 italic">💡 Take what works for you, adapt what doesn't. This isn't about perfection - it's about clarity.</p>
          </div>
          <div class="flex gap-2 ml-4">
            <button
              @click="showHistoryModal = true"
              class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition flex items-center gap-2"
              title="View past snapshots of your vision"
            >
              <span>📜</span>
              <span class="hidden sm:inline">History</span>
            </button>
            <button
              @click="showSnapshotModal = true"
              class="px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700 transition flex items-center gap-2"
              title="Capture how you see your life right now"
            >
              <span>📸</span>
              <span class="hidden sm:inline">Save Snapshot</span>
            </button>
          </div>
        </div>
      </div>

      <!-- Snapshot Feature Info Banner -->
      <div v-if="!hasSeenSnapshotInfo" class="mb-6 bg-gradient-to-r from-purple-50 to-blue-50 border-2 border-purple-200 rounded-lg p-4">
        <div class="flex items-start gap-3">
          <span class="text-2xl">💡</span>
          <div class="flex-1">
            <h4 class="text-sm font-semibold text-purple-900 mb-1">New: Vision History</h4>
            <p class="text-sm text-purple-800 mb-2">
              Your vision auto-saves as you edit. But when life changes—save a <strong>snapshot</strong> to remember this moment.
            </p>
            <p class="text-xs text-purple-700">
              In 5 years, you'll love looking back at how your bucket list and priorities evolved.
            </p>
          </div>
          <button
            @click="dismissSnapshotInfo"
            class="text-purple-400 hover:text-purple-600"
            title="Dismiss"
          >
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
            </svg>
          </button>
        </div>
      </div>

      <!-- Privacy Notice -->
      <div v-if="vision.is_public" class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4">
        <div class="flex items-start gap-3">
          <svg class="w-5 h-5 text-green-600 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
          </svg>
          <div class="flex-1">
            <h4 class="text-sm font-semibold text-green-900">Your vision is PUBLIC by default</h4>
            <p class="text-sm text-green-700 mt-1">You can make individual sections private using the lock icon</p>
          </div>
        </div>
      </div>

      <!-- Vision Sections -->
      <div class="space-y-8">
        <!-- 1. The Eulogy Method -->
        <VisionSection
          title="📜 The Eulogy Method"
          subtitle="Imagine you've lived to 100. What would you want people to say about how you lived?"
          :is-private="isSectionPrivate('eulogy_method')"
          @toggle-privacy="toggleSectionPrivacy('eulogy_method')"
        >
          <div class="mb-3 text-sm text-gray-600 bg-blue-50 border border-blue-200 rounded-lg p-3">
            <p class="font-medium mb-1">💭 This isn't morbid - it's about clarity on how you want to <em>live</em>.</p>
            <p class="text-xs text-gray-500">Reflect on what your family, friends, colleagues, and community would say about you.</p>
          </div>
          <TextareaWithEmoji
            v-model="vision.eulogy_method"
            @blur="saveVision"
            rows="8"
            placeholder="What would your family say about how you showed up in their lives?
What would friends remember about your friendship and the memories you created?
What would colleagues say about your contributions?
What values and qualities would people remember about you?"
          />
        </VisionSection>

        <!-- 2. The Bucket List -->
        <VisionSection
          title="🪣 The Bucket List"
          subtitle="Dream without limits - what do you want to do, be, learn, create, and experience?"
          :is-private="isSectionPrivate('bucket_list')"
          @toggle-privacy="toggleSectionPrivacy('bucket_list')"
        >
          <div class="mb-3 text-sm text-gray-600 bg-blue-50 border border-blue-200 rounded-lg p-3">
            <p class="font-medium mb-1">✨ Think about what you'd like to:</p>
            <p class="text-xs text-gray-500">Do • Be • Learn • Contribute to • Create • See • Experience • Have • Overcome</p>
          </div>
          <div class="space-y-2">
            <div
              v-for="(item, index) in bucketListItems"
              :key="index"
              class="flex items-center gap-2"
            >
              <input
                type="checkbox"
                v-model="item.completed"
                @change="saveVision"
                class="w-5 h-5 text-green-600 border-gray-300 rounded focus:ring-green-500"
              />
              <input
                type="text"
                v-model="item.text"
                @blur="saveVision"
                :class="[
                  'flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500',
                  item.completed ? 'line-through text-gray-500' : ''
                ]"
                :placeholder="`${index + 1}. Start a business, learn piano, visit Japan, write a book...`"
              />
              <button
                @click="removeBucketListItem(index)"
                class="text-red-500 hover:text-red-700"
              >
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                </svg>
              </button>
            </div>
            <button
              @click="addBucketListItem"
              class="mt-3 px-4 py-2 text-sm font-medium text-green-700 bg-green-50 border border-green-300 rounded-lg hover:bg-green-100 transition"
            >
              + Add Item
            </button>
          </div>
        </VisionSection>

        <!-- 3. The Mission Prompt -->
        <VisionSection
          title="🎯 The Mission Prompt"
          subtitle="How would you serve others if money and fear weren't factors?"
          :is-private="isSectionPrivate('mission_prompt')"
          @toggle-privacy="toggleSectionPrivacy('mission_prompt')"
        >
          <div class="mb-3 text-sm text-gray-600 bg-blue-50 border border-blue-200 rounded-lg p-3">
            <p class="font-medium mb-1">🪄 Imagine a magical world:</p>
            <p class="text-xs text-gray-500">You have all the time and money you need. You're not afraid of failure or judgment. You must spend 40 hours a week using your talents to serve others. What would you do?</p>
          </div>
          <TextareaWithEmoji
            v-model="vision.mission_prompt"
            @blur="saveVision"
            rows="6"
            placeholder="What problems would you solve in the world?
Which of your unique abilities bring you the most joy?
If you could leave one lasting positive change, what would it be?

My mission is to..."
          />
          <p class="mt-2 text-xs text-gray-500 italic">
            💡 Example: "Help motivated people achieve more by building tools that bring balance between work and family life"
          </p>
        </VisionSection>

        <!-- 4. Definition of Success -->
        <VisionSection
          title="🏆 Definition of Success"
          subtitle="Imagine yourself in complete contentment. What does your life look like?"
          :is-private="isSectionPrivate('definition_of_success')"
          @toggle-privacy="toggleSectionPrivacy('definition_of_success')"
        >
          <div class="mb-3 text-sm text-gray-600 bg-blue-50 border border-blue-200 rounded-lg p-3">
            <p class="font-medium mb-1">🌟 Picture deep, lasting satisfaction:</p>
            <p class="text-xs text-gray-500">Not achievement or recognition, but genuine peace. What fills your days? Who surrounds you? What worries are absent?</p>
          </div>
          <div class="space-y-4">
            <div v-for="area in lifeAreas" :key="area.key" class="border border-gray-200 rounded-lg p-4 hover:border-green-300 transition">
              <label class="block text-sm font-semibold text-gray-900 mb-2">
                {{ area.icon }} {{ area.label }}
              </label>
              <TextareaWithEmoji
                :model-value="getDefinitionOfSuccess(area.key)"
                @update:model-value="updateDefinitionOfSuccess(area.key, $event)"
                @blur="saveVision"
                rows="2"
                class="text-sm"
                :placeholder="`For me, success in ${area.label.toLowerCase()} means...`"
              />

              <!-- Goals in this area -->
              <div v-if="getGoalsForArea(area.key).length > 0" class="mt-3 pt-3 border-t border-gray-200">
                <div class="flex items-center justify-between mb-2">
                  <p class="text-xs font-semibold text-gray-700">🎯 Active goals in this area:</p>
                  <a :href="`/@${$page.props.auth.user.username}/journey/goals`" class="text-xs text-green-600 hover:underline">View all</a>
                </div>
                <div class="space-y-2">
                  <div
                    v-for="goal in getGoalsForArea(area.key)"
                    :key="goal.id"
                    class="bg-green-50 border border-green-200 rounded-lg p-2 text-xs"
                  >
                    <div class="flex items-start justify-between gap-2">
                      <div class="flex-1">
                        <div class="font-medium text-gray-900">{{ goal.title }}</div>
                        <div class="text-gray-600 mt-0.5">
                          {{ formatTimeHorizon(goal.time_horizon) }}
                          <span v-if="goal.tracking_type === 'metric' && goal.metric_progress_percentage !== undefined">
                            • {{ goal.metric_progress_percentage }}% complete
                          </span>
                          <span v-if="goal.tracking_type === 'milestone' && goal.progress_percentage !== undefined">
                            • {{ goal.progress_percentage }}% complete
                          </span>
                        </div>
                      </div>
                      <span v-if="goal.status === 'completed'" class="text-green-600 font-bold">✓</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </VisionSection>

        <!-- 5. Odyssey Plan -->
        <VisionSection
          title="🗺️ Odyssey Plan"
          subtitle="3 possible life paths you could take over the next 5-10 years"
          :is-private="isSectionPrivate('odyssey_plan')"
          @toggle-privacy="toggleSectionPrivacy('odyssey_plan')"
        >
          <div class="mb-3 text-sm text-gray-600 bg-blue-50 border border-blue-200 rounded-lg p-3">
            <p class="font-medium mb-1">🧭 Explore multiple futures to avoid getting stuck:</p>
            <p class="text-xs text-gray-500">For each path, think about <strong>Self</strong> (your health, energy, mindset), <strong>Work</strong> (your career/projects), and <strong>Relationships</strong> (family, friends, community).</p>
          </div>
          <div class="space-y-6">
            <!-- Current Path -->
            <div class="border-2 border-gray-200 rounded-lg p-5 hover:border-green-300 transition">
              <div class="mb-3">
                <h4 class="text-lg font-bold text-gray-900 mb-1">🎯 Current Path</h4>
                <p class="text-sm text-gray-600">
                  What does your life look like if you continue on your current trajectory?
                </p>
                <p class="text-xs text-gray-500 mt-1 italic">
                  Use your current context - where will you be in 5-10 years if nothing changes?
                </p>
              </div>
              <TextareaWithEmoji
                :model-value="getOdysseyPath('current_path')"
                @update:model-value="updateOdysseyPath('current_path', $event)"
                @blur="saveVision"
                rows="5"
                class="text-sm"
                placeholder="Self: How's your health and energy?
Work: What does your career look like?
Relationships: How have your connections evolved?

Describe the full picture..."
              />
            </div>

            <!-- Alternative Path -->
            <div class="border-2 border-gray-200 rounded-lg p-5 hover:border-green-300 transition">
              <div class="mb-3">
                <h4 class="text-lg font-bold text-gray-900 mb-1">🐉 Alternative Path</h4>
                <p class="text-sm text-gray-600">
                  If your current path wasn't an option, what's another way your life could unfold that would be fulfilling?
                </p>
                <p class="text-xs text-gray-500 mt-1 italic">
                  Imagine your current career is completely unavailable. What would you pivot to?
                </p>
              </div>
              <TextareaWithEmoji
                :model-value="getOdysseyPath('alternative_path')"
                @update:model-value="updateOdysseyPath('alternative_path', $event)"
                @blur="saveVision"
                rows="5"
                class="text-sm"
                placeholder="Self: How would you grow differently?
Work: What new field or business would you explore?
Relationships: How would your connections change?

Paint the picture..."
              />
            </div>

            <!-- Radical Alternative Path -->
            <div class="border-2 border-gray-200 rounded-lg p-5 hover:border-green-300 transition">
              <div class="mb-3">
                <h4 class="text-lg font-bold text-gray-900 mb-1">🦄 Radical Path</h4>
                <p class="text-sm text-gray-600">
                  If money, fear, and societal expectations weren't limiting factors, what would you do with your life?
                </p>
                <p class="text-xs text-gray-500 mt-1 italic">
                  Dream big. No constraints. The California beach lifestyle? Go for it.
                </p>
              </div>
              <TextareaWithEmoji
                :model-value="getOdysseyPath('radical_path')"
                @update:model-value="updateOdysseyPath('radical_path', $event)"
                @blur="saveVision"
                rows="5"
                class="text-sm"
                placeholder="Self: Living your healthiest, most energized life?
Work: Creating on your own terms?
Relationships: Perfect work-life balance?

What's your wildest dream?"
              />
            </div>
          </div>
        </VisionSection>

        <!-- 6. Future Calendar -->
        <VisionSection
          title="📅 Future Calendar"
          subtitle="Your ideal days, 3 years from now"
          :is-private="isSectionPrivate('future_calendar')"
          @toggle-privacy="toggleSectionPrivacy('future_calendar')"
        >
          <div class="mb-3 text-sm text-gray-600 bg-blue-50 border border-blue-200 rounded-lg p-3">
            <p class="font-medium mb-1">⏰ Your life is made up of ordinary days:</p>
            <p class="text-xs text-gray-500">If you can design an amazing Tuesday and Sunday, you'll probably have an amazing life.</p>
          </div>
          <div class="space-y-6">
            <!-- Ideal Tuesday (Workday) -->
            <div class="border-2 border-gray-200 rounded-lg p-5 hover:border-green-300 transition">
              <div class="mb-3">
                <h4 class="text-lg font-bold text-gray-900 mb-1">🕴️ Your Ideal Tuesday</h4>
                <p class="text-sm text-gray-600">
                  3 years from now, what does a perfect workday look like?
                </p>
                <p class="text-xs text-gray-500 mt-1 italic">
                  Walk through the entire day - morning to evening.
                </p>
              </div>
              <TextareaWithEmoji
                :model-value="getFutureDay('ideal_tuesday')"
                @update:model-value="updateFutureDay('ideal_tuesday', $event)"
                @blur="saveVision"
                rows="6"
                class="text-sm"
                placeholder="What time do you wake up?
What's your morning routine?
What work are you doing? Where? With whom?
How do you spend your lunch and breaks?
When does work end? What fills your evening?
What time do you sleep?"
              />
            </div>

            <!-- Ideal Sunday (Rest Day) -->
            <div class="border-2 border-gray-200 rounded-lg p-5 hover:border-green-300 transition">
              <div class="mb-3">
                <h4 class="text-lg font-bold text-gray-900 mb-1">🏠 Your Ideal Sunday</h4>
                <p class="text-sm text-gray-600">
                  3 years from now, how do you spend a perfect day of rest?
                </p>
                <p class="text-xs text-gray-500 mt-1 italic">
                  This is about life, not work. How do you recharge?
                </p>
              </div>
              <TextareaWithEmoji
                :model-value="getFutureDay('ideal_sunday')"
                @update:model-value="updateFutureDay('ideal_sunday', $event)"
                @blur="saveVision"
                rows="6"
                class="text-sm"
                placeholder="What time do you wake up?
What's your morning like?
What activities bring you joy during the day?
Who are you spending time with?
Where are you living? What's your home like?
How does it feel to be you on a day of rest?"
              />
            </div>
          </div>
        </VisionSection>
      </div>

      <!-- Save Status -->
      <div v-if="saving" class="fixed bottom-4 right-4 bg-green-600 text-white px-4 py-2 rounded-lg shadow-lg">
        Saving...
      </div>
      <div v-else-if="saved" class="fixed bottom-4 right-4 bg-green-600 text-white px-4 py-2 rounded-lg shadow-lg">
        ✓ Saved
      </div>

      <!-- Create Snapshot Modal -->
      <div v-if="showSnapshotModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50" @click.self="showSnapshotModal = false">
        <div class="bg-white rounded-xl p-6 max-w-lg w-full mx-4 shadow-2xl">
          <h3 class="text-xl font-bold text-gray-900 mb-2">📸 Save a Snapshot of Your Vision</h3>
          <p class="text-sm text-gray-600 mb-4">Create a time capsule of how you see your life right now.</p>

          <!-- Why this matters -->
          <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
            <p class="text-sm font-semibold text-blue-900 mb-2">✨ Why save snapshots?</p>
            <ul class="text-xs text-blue-800 space-y-1">
              <li>• <strong>Track your evolution:</strong> See how your priorities shift as life changes</li>
              <li>• <strong>Celebrate growth:</strong> Look back at bucket list items you've completed</li>
              <li>• <strong>Gain perspective:</strong> Understand patterns in what matters to you</li>
              <li>• <strong>Remember key moments:</strong> Career changes, big moves, life milestones</li>
            </ul>
          </div>

          <!-- When to save -->
          <div class="bg-green-50 border border-green-200 rounded-lg p-3 mb-4">
            <p class="text-xs font-semibold text-green-900 mb-1">💡 Good times to save a snapshot:</p>
            <p class="text-xs text-green-700">New Year, birthday, career change, moving cities, getting married, having a child, or just feeling different about life</p>
          </div>

          <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">What's happening in your life? (optional)</label>
            <textarea
              v-model="snapshotNote"
              rows="3"
              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
              placeholder="e.g., 'Just turned 30 - refocusing on health and family' or 'Starting my own business after 10 years corporate'"
              maxlength="500"
            ></textarea>
            <p class="text-xs text-gray-500 mt-1">{{ snapshotNote.length }}/500 characters</p>
          </div>

          <div class="flex gap-3 justify-end">
            <button
              @click="showSnapshotModal = false"
              class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50"
            >
              Cancel
            </button>
            <button
              @click="createSnapshot"
              :disabled="creatingSnapshot"
              class="px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700 disabled:opacity-50"
            >
              {{ creatingSnapshot ? 'Saving...' : 'Save Snapshot' }}
            </button>
          </div>
        </div>
      </div>

      <!-- History Modal -->
      <div v-if="showHistoryModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50" @click.self="showHistoryModal = false">
        <div class="bg-white rounded-xl p-6 max-w-3xl w-full mx-4 shadow-2xl max-h-[90vh] overflow-y-auto">
          <div class="flex items-start justify-between mb-4">
            <div>
              <h3 class="text-xl font-bold text-gray-900 mb-1">📜 Your Vision History</h3>
              <p class="text-sm text-gray-600">See how your life vision has evolved over time</p>
            </div>
            <button
              @click="showHistoryModal = false"
              class="text-gray-400 hover:text-gray-600"
            >
              <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
              </svg>
            </button>
          </div>

          <div v-if="loadingSnapshots" class="text-center py-8 text-gray-500">
            Loading history...
          </div>

          <div v-else-if="snapshots.length === 0" class="text-center py-12">
            <div class="mb-6">
              <span class="text-6xl">🌱</span>
            </div>
            <h4 class="text-lg font-semibold text-gray-900 mb-2">Your Journey Starts Here</h4>
            <p class="text-gray-600 mb-4 max-w-md mx-auto">
              You haven't saved any snapshots yet. Your vision will naturally evolve as life happens.
            </p>

            <div class="bg-purple-50 border border-purple-200 rounded-lg p-4 max-w-md mx-auto mb-4">
              <p class="text-sm font-semibold text-purple-900 mb-2">💭 Imagine this in 5 years:</p>
              <ul class="text-xs text-purple-800 text-left space-y-1">
                <li>• Looking back at your 2025 bucket list vs your 2030 bucket list</li>
                <li>• Seeing how your definition of success changed after a career shift</li>
                <li>• Remembering what mattered most before you had kids</li>
                <li>• Noticing patterns in how you evolve during major life changes</li>
              </ul>
            </div>

            <button
              @click="showHistoryModal = false; showSnapshotModal = true"
              class="px-6 py-3 text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700 transition"
            >
              📸 Save Your First Snapshot
            </button>

            <p class="text-xs text-gray-500 mt-3">
              Your current vision auto-saves as you edit. Snapshots are for big moments.
            </p>
          </div>

          <div v-else class="space-y-4">
            <div
              v-for="snapshot in snapshots"
              :key="snapshot.id"
              class="border-2 border-gray-200 rounded-lg p-4 hover:border-green-300 transition cursor-pointer"
              @click="viewSnapshot(snapshot)"
            >
              <div class="flex items-start justify-between">
                <div class="flex-1">
                  <div class="flex items-center gap-2 mb-1">
                    <span class="text-sm font-semibold text-gray-900">
                      {{ formatDate(snapshot.snapshot_date) }}
                    </span>
                  </div>
                  <p v-if="snapshot.note" class="text-sm text-gray-600 mb-2">{{ snapshot.note }}</p>
                  <p class="text-xs text-gray-500">
                    Bucket list: {{ (snapshot.bucket_list || []).length }} items
                  </p>
                </div>
                <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                </svg>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- View Snapshot Modal -->
      <div v-if="viewingSnapshot" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50" @click.self="viewingSnapshot = null">
        <div class="bg-white rounded-xl p-6 max-w-3xl w-full mx-4 shadow-2xl max-h-[90vh] overflow-y-auto">
          <div class="flex items-start justify-between mb-4">
            <div>
              <h3 class="text-xl font-bold text-gray-900 mb-1">
                Vision Snapshot - {{ formatDate(viewingSnapshot.snapshot_date) }}
              </h3>
              <p v-if="viewingSnapshot.note" class="text-sm text-gray-600">{{ viewingSnapshot.note }}</p>
            </div>
            <button
              @click="viewingSnapshot = null"
              class="text-gray-400 hover:text-gray-600"
            >
              <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
              </svg>
            </button>
          </div>

          <div class="space-y-6 text-sm">
            <!-- Eulogy Method -->
            <div v-if="viewingSnapshot.eulogy_method">
              <h4 class="font-semibold text-gray-900 mb-2">📜 The Eulogy Method</h4>
              <p class="text-gray-700 whitespace-pre-wrap">{{ viewingSnapshot.eulogy_method }}</p>
            </div>

            <!-- Bucket List -->
            <div v-if="viewingSnapshot.bucket_list && viewingSnapshot.bucket_list.length > 0">
              <h4 class="font-semibold text-gray-900 mb-2">🪣 The Bucket List</h4>
              <ul class="space-y-1">
                <li v-for="(item, index) in viewingSnapshot.bucket_list" :key="index" class="flex items-start gap-2">
                  <span>{{ item.completed ? '✅' : '⬜' }}</span>
                  <span :class="item.completed ? 'line-through text-gray-500' : 'text-gray-700'">{{ item.text }}</span>
                </li>
              </ul>
            </div>

            <!-- Mission Prompt -->
            <div v-if="viewingSnapshot.mission_prompt">
              <h4 class="font-semibold text-gray-900 mb-2">🎯 The Mission Prompt</h4>
              <p class="text-gray-700 whitespace-pre-wrap">{{ viewingSnapshot.mission_prompt }}</p>
            </div>

            <!-- Definition of Success -->
            <div v-if="viewingSnapshot.definition_of_success && Object.keys(viewingSnapshot.definition_of_success).length > 0">
              <h4 class="font-semibold text-gray-900 mb-2">🏆 Definition of Success</h4>
              <div class="space-y-2">
                <div v-for="(value, key) in viewingSnapshot.definition_of_success" :key="key">
                  <p class="font-medium text-gray-800 capitalize">{{ key.replace('_', ' ') }}</p>
                  <p class="text-gray-700 whitespace-pre-wrap">{{ value }}</p>
                </div>
              </div>
            </div>

            <!-- Odyssey Plan -->
            <div v-if="viewingSnapshot.odyssey_plan && Object.keys(viewingSnapshot.odyssey_plan).length > 0">
              <h4 class="font-semibold text-gray-900 mb-2">🗺️ Odyssey Plan</h4>
              <div class="space-y-2">
                <div v-for="(value, key) in viewingSnapshot.odyssey_plan" :key="key">
                  <p class="font-medium text-gray-800 capitalize">{{ key.replace('_', ' ') }}</p>
                  <p class="text-gray-700 whitespace-pre-wrap">{{ value }}</p>
                </div>
              </div>
            </div>

            <!-- Future Calendar -->
            <div v-if="viewingSnapshot.future_calendar && Object.keys(viewingSnapshot.future_calendar).length > 0">
              <h4 class="font-semibold text-gray-900 mb-2">📅 Future Calendar</h4>
              <div class="space-y-2">
                <div v-for="(value, key) in viewingSnapshot.future_calendar" :key="key">
                  <p class="font-medium text-gray-800 capitalize">{{ key.replace('_', ' ') }}</p>
                  <p class="text-gray-700 whitespace-pre-wrap">{{ value }}</p>
                </div>
              </div>
            </div>
          </div>

          <div class="mt-6 flex justify-end">
            <button
              @click="viewingSnapshot = null"
              class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50"
            >
              Close
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
import VisionSection from '../Components/journey/VisionSection.vue';
import TextareaWithEmoji from '../Components/TextareaWithEmoji.vue';
import { ref, computed, onMounted, watch } from 'vue';
import { formatTimeHorizon, formatDateFull } from '../utils/dateFormatters';

const vision = ref({
  eulogy_method: null,
  bucket_list: [],
  mission_prompt: null,
  definition_of_success: {},
  odyssey_plan: {},
  future_calendar: {},
  is_public: true,
  private_sections: [],
});

const saving = ref(false);
const saved = ref(false);

// Snapshot state
const showSnapshotModal = ref(false);
const showHistoryModal = ref(false);
const snapshotNote = ref('');
const creatingSnapshot = ref(false);
const snapshots = ref([]);
const loadingSnapshots = ref(false);
const viewingSnapshot = ref(null);
const hasSeenSnapshotInfo = ref(localStorage.getItem('hasSeenSnapshotInfo') === 'true');

// Goals data
const allGoals = ref([]);

const lifeAreas = [
  { key: 'career', label: 'Career/Business', icon: '💼' },
  { key: 'health', label: 'Health/Wellness', icon: '🏃' },
  { key: 'relationships', label: 'Relationships/Family', icon: '❤️' },
  { key: 'financial', label: 'Financial Freedom', icon: '💰' },
  { key: 'growth', label: 'Personal Growth', icon: '📚' },
  { key: 'impact', label: 'Contribution/Impact', icon: '🌍' },
];

const bucketListItems = computed({
  get: () => vision.value.bucket_list || [],
  set: (value) => { vision.value.bucket_list = value; },
});

const definitionOfSuccess = computed({
  get: () => vision.value.definition_of_success || {},
  set: (value) => { vision.value.definition_of_success = value; },
});



const fetchVision = async () => {
  try {
    const response = await window.axios.get('/api/journey/vision');
    vision.value = response.data;

    // Ensure definition_of_success is an object, not an array
    if (Array.isArray(vision.value.definition_of_success)) {
      vision.value.definition_of_success = {};
    }

    // Ensure odyssey_plan is an object, not an array
    if (Array.isArray(vision.value.odyssey_plan)) {
      vision.value.odyssey_plan = {};
    }

    // Ensure future_calendar is an object, not an array
    if (Array.isArray(vision.value.future_calendar)) {
      vision.value.future_calendar = {};
    }
  } catch (error) {
    console.error('Error fetching vision:', error);
  }
};

const saveVision = async () => {
  saving.value = true;
  saved.value = false;

  try {
    const response = await window.axios.post('/api/journey/vision', vision.value);
    vision.value = response.data;

    // Ensure definition_of_success is an object, not an array
    if (Array.isArray(vision.value.definition_of_success)) {
      vision.value.definition_of_success = {};
    }

    // Ensure odyssey_plan is an object, not an array
    if (Array.isArray(vision.value.odyssey_plan)) {
      vision.value.odyssey_plan = {};
    }

    // Ensure future_calendar is an object, not an array
    if (Array.isArray(vision.value.future_calendar)) {
      vision.value.future_calendar = {};
    }

    saved.value = true;
    setTimeout(() => { saved.value = false; }, 2000);
  } catch (error) {
    console.error('Error saving vision:', error);
  } finally {
    saving.value = false;
  }
};

const isSectionPrivate = (section) => {
  return (vision.value.private_sections || []).includes(section);
};

const toggleSectionPrivacy = async (section) => {
  try {
    const response = await window.axios.post('/api/journey/vision/toggle-section-privacy', { section });
    vision.value.private_sections = response.data.private_sections;
  } catch (error) {
    console.error('Error toggling section privacy:', error);
  }
};

const getDefinitionOfSuccess = (key) => {
  return (vision.value.definition_of_success || {})[key] || '';
};

const updateDefinitionOfSuccess = (key, value) => {
  // Ensure definition_of_success is an object, not an array
  if (!vision.value.definition_of_success || Array.isArray(vision.value.definition_of_success)) {
    vision.value.definition_of_success = {};
  }
  vision.value.definition_of_success[key] = value;
};

const addBucketListItem = () => {
  bucketListItems.value.push({ text: '', completed: false });
};

const removeBucketListItem = (index) => {
  bucketListItems.value.splice(index, 1);
  saveVision();
};

const getOdysseyPath = (key) => {
  return (vision.value.odyssey_plan || {})[key] || '';
};

const updateOdysseyPath = (key, value) => {
  // Ensure odyssey_plan is an object, not an array
  if (!vision.value.odyssey_plan || Array.isArray(vision.value.odyssey_plan)) {
    vision.value.odyssey_plan = {};
  }
  vision.value.odyssey_plan[key] = value;
};

const getFutureDay = (key) => {
  return (vision.value.future_calendar || {})[key] || '';
};

const updateFutureDay = (key, value) => {
  // Ensure future_calendar is an object, not an array
  if (!vision.value.future_calendar || Array.isArray(vision.value.future_calendar)) {
    vision.value.future_calendar = {};
  }
  vision.value.future_calendar[key] = value;
};

// Snapshot functions
const createSnapshot = async () => {
  creatingSnapshot.value = true;

  try {
    await window.axios.post('/api/journey/vision/snapshots', {
      note: snapshotNote.value || null,
    });

    showSnapshotModal.value = false;
    snapshotNote.value = '';

    // Show success message
    saved.value = true;
    setTimeout(() => { saved.value = false; }, 2000);

    // Refresh snapshots if history modal is open
    if (showHistoryModal.value) {
      await fetchSnapshots();
    }
  } catch (error) {
    console.error('Error creating snapshot:', error);
    alert('Failed to create snapshot. Please try again.');
  } finally {
    creatingSnapshot.value = false;
  }
};

const fetchSnapshots = async () => {
  loadingSnapshots.value = true;

  try {
    const response = await window.axios.get('/api/journey/vision/snapshots');
    snapshots.value = response.data;
  } catch (error) {
    console.error('Error fetching snapshots:', error);
  } finally {
    loadingSnapshots.value = false;
  }
};

const viewSnapshot = (snapshot) => {
  viewingSnapshot.value = snapshot;
  showHistoryModal.value = false;
};

// Alias formatDateFull as formatDate for template usage
const formatDate = formatDateFull;

// Dismiss snapshot info banner
const dismissSnapshotInfo = () => {
  hasSeenSnapshotInfo.value = true;
  localStorage.setItem('hasSeenSnapshotInfo', 'true');
};

// Goals functions
const fetchGoals = async () => {
  try {
    const response = await window.axios.get('/api/journey/goals');
    // Flatten all goal groups into a single array
    allGoals.value = Object.values(response.data).flat();
  } catch (error) {
    console.error('Error fetching goals:', error);
  }
};

const getGoalsForArea = (lifeArea) => {
  return allGoals.value.filter(goal =>
    goal.life_area === lifeArea &&
    (goal.status === 'active' || goal.status === 'completed')
  ).slice(0, 5); // Limit to 5 goals per area for clean UI
};

// Watch for history modal opening to fetch snapshots
watch(showHistoryModal, (newValue) => {
  if (newValue) {
    fetchSnapshots();
  }
});

onMounted(() => {
  fetchVision();
  fetchGoals();
});
</script>
