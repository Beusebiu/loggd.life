<template>
  <div class="min-h-screen bg-gradient-to-b from-white to-gray-50">
    <AppNav />

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div class="flex gap-6">
        <!-- Sidebar -->
        <aside class="w-64 flex-shrink-0">
          <div class="bg-white rounded-2xl border border-gray-200 shadow-lg sticky top-8 overflow-hidden">
            <!-- Overview -->
            <nav class="py-2 border-b border-gray-200">
              <button
                @click="showOverview"
                class="w-full text-left px-4 py-2.5 text-sm transition-colors flex items-center gap-2"
                :class="selectedTemplate === null
                  ? 'bg-gray-100 text-gray-900 font-medium border-r-2 border-gray-500'
                  : 'text-gray-700 hover:bg-gray-50'"
              >
                <span>📊</span>
                <span>Overview</span>
              </button>
            </nav>

            <!-- Vision Section -->
            <div class="p-4 bg-gradient-to-r from-purple-50 to-indigo-50 border-b border-gray-200">
              <h3 class="text-sm font-bold text-gray-900 flex items-center gap-2">
                🎯 My Vision
              </h3>
            </div>
            <nav class="py-2">
              <button
                v-for="template in visionTemplates"
                :key="template.id"
                @click="selectTemplate(template)"
                class="w-full text-left px-4 py-2.5 text-sm transition-colors flex items-center gap-2"
                :class="selectedTemplate?.id === template.id
                  ? 'bg-purple-50 text-purple-700 font-medium border-r-2 border-purple-500'
                  : 'text-gray-700 hover:bg-gray-50'"
              >
                <span>{{ template.icon }}</span>
                <span class="truncate">{{ template.name }}</span>
              </button>
            </nav>

            <!-- Goals Section -->
            <div class="p-4 bg-gradient-to-r from-green-50 to-emerald-50 border-y border-gray-200">
              <h3 class="text-sm font-bold text-gray-900 flex items-center gap-2">
                🎯 Goals
              </h3>
            </div>
            <nav class="py-2">
              <button
                v-for="template in goalTemplates"
                :key="template.id"
                @click="selectTemplate(template)"
                class="w-full text-left px-4 py-2.5 text-sm transition-colors flex items-center gap-2"
                :class="selectedTemplate?.id === template.id
                  ? 'bg-green-50 text-green-700 font-medium border-r-2 border-green-500'
                  : 'text-gray-700 hover:bg-gray-50'"
              >
                <span>{{ template.icon }}</span>
                <span class="truncate">{{ template.name }}</span>
              </button>
            </nav>

            <!-- Reviews Section -->
            <div class="p-4 bg-gradient-to-r from-blue-50 to-cyan-50 border-y border-gray-200">
              <h3 class="text-sm font-bold text-gray-900 flex items-center gap-2">
                📊 Reviews
              </h3>
            </div>
            <nav class="py-2">
              <button
                v-for="template in reviewTemplates"
                :key="template.id"
                @click="selectTemplate(template)"
                class="w-full text-left px-4 py-2.5 text-sm transition-colors flex items-center gap-2"
                :class="selectedTemplate?.id === template.id
                  ? 'bg-blue-50 text-blue-700 font-medium border-r-2 border-blue-500'
                  : 'text-gray-700 hover:bg-gray-50'"
              >
                <span>{{ template.icon }}</span>
                <span class="truncate">{{ template.name }}</span>
              </button>
            </nav>

            <!-- Manage Templates (subtle footer) -->
            <div class="mt-auto p-4 border-t border-gray-200">
              <button
                @click="openTemplateManager"
                class="w-full text-xs text-gray-500 hover:text-gray-700 transition-colors flex items-center justify-center gap-1.5 py-2"
              >
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <span>Manage Templates</span>
              </button>
            </div>
          </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 min-w-0">
          <!-- Header -->
          <div v-if="selectedTemplate" class="mb-6">
            <div class="flex items-center justify-between">
              <div>
                <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-3">
                  <span class="text-4xl">{{ selectedTemplate.icon }}</span>
                  {{ selectedTemplate.name }}
                </h1>
                <p class="text-sm text-gray-600 mt-1">
                  {{ getBehaviorDescription(selectedTemplate.behavior) }}
                </p>
              </div>

              <!-- Action Button -->
              <button
                v-if="canCreate"
                @click="openCreateModal"
                class="px-6 py-2.5 bg-gradient-to-r from-green-500 to-emerald-600 text-white text-sm font-semibold rounded-lg hover:from-green-600 hover:to-emerald-700 transition-all shadow-lg shadow-green-500/20"
              >
                {{ getActionButtonText() }}
              </button>
            </div>
          </div>

          <!-- Content Area -->
          <div v-if="selectedTemplate">
            <!-- Singleton/Versioned: Show current + history -->
            <div v-if="['singleton', 'versioned'].includes(selectedTemplate.behavior)">
              <!-- Current Version -->
              <div v-if="currentCheckIn" class="bg-white rounded-2xl border border-gray-200 shadow-lg p-8 mb-6">
                <div class="flex justify-between items-start mb-6">
                  <div>
                    <div class="flex items-center gap-3 mb-2">
                      <span class="px-3 py-1 bg-green-100 text-green-700 text-xs font-bold rounded-full">
                        Current {{ selectedTemplate.behavior === 'versioned' ? `v${currentCheckIn.version}` : '' }}
                      </span>
                      <span class="text-sm text-gray-500">Updated {{ formatDate(currentCheckIn.updated_at) }}</span>
                    </div>
                  </div>
                  <div class="flex gap-2">
                    <button
                      v-if="selectedTemplate.behavior === 'versioned'"
                      @click="showVersionHistory = !showVersionHistory"
                      class="text-gray-400 hover:text-gray-600 transition-colors text-sm font-medium"
                    >
                      {{ showVersionHistory ? 'Hide' : 'View' }} History
                    </button>
                    <button
                      @click="openEditModal(currentCheckIn)"
                      class="text-blue-500 hover:text-blue-600 transition-colors"
                    >
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                      </svg>
                    </button>
                  </div>
                </div>

                <div class="space-y-6">
                  <div v-for="field in selectedTemplate.structure" :key="field.id">
                    <h4 class="text-xs font-bold uppercase tracking-wide text-gray-500 mb-2">{{ field.label }}</h4>

                    <!-- Textarea / Text -->
                    <p v-if="field.type === 'textarea' || field.type === 'text'" class="text-base text-gray-900 whitespace-pre-wrap leading-relaxed pl-3 border-l-2 border-gray-200">
                      {{ currentCheckIn?.content?.[field.id] || '—' }}
                    </p>

                    <!-- Number -->
                    <p v-else-if="field.type === 'number'" class="text-base text-gray-900 pl-3 border-l-2 border-gray-200">
                      {{ currentCheckIn?.content?.[field.id] ?? '—' }}
                    </p>

                    <!-- Checkbox -->
                    <div v-else-if="field.type === 'checkbox'" class="pl-3">
                      <span v-if="currentCheckIn?.content?.[field.id]" class="inline-flex items-center gap-2 text-green-600 font-medium">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                          <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        Yes
                      </span>
                      <span v-else class="text-gray-400">No</span>
                    </div>

                    <!-- Select -->
                    <p v-else-if="field.type === 'select'" class="text-base text-gray-900 pl-3 border-l-2 border-gray-200">
                      {{ currentCheckIn?.content?.[field.id] || '—' }}
                    </p>

                    <!-- Checklist -->
                    <div v-else-if="field.type === 'checklist'" class="pl-3 space-y-2">
                      <div v-if="currentCheckIn?.content?.[field.id]?.length > 0">
                        <div v-for="(item, idx) in currentCheckIn.content[field.id]" :key="idx" class="flex items-start gap-2">
                          <svg v-if="item.checked" class="w-5 h-5 text-green-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                          </svg>
                          <svg v-else class="w-5 h-5 text-gray-300 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm0-2a6 6 0 100-12 6 6 0 000 12z" clip-rule="evenodd"/>
                          </svg>
                          <span :class="item.checked ? 'line-through text-gray-500' : 'text-gray-900'">{{ item.text }}</span>
                        </div>
                      </div>
                      <p v-else class="text-gray-400">—</p>
                    </div>

                    <!-- Fallback -->
                    <p v-else class="text-base text-gray-900 whitespace-pre-wrap leading-relaxed pl-3 border-l-2 border-gray-200">
                      {{ currentCheckIn?.content?.[field.id] || '—' }}
                    </p>
                  </div>
                </div>
              </div>

              <!-- Version History -->
              <div v-if="showVersionHistory && versions.length > 1" class="space-y-4">
                <h3 class="text-lg font-bold text-gray-900">Previous Versions</h3>
                <div
                  v-for="version in versions.slice(1)"
                  :key="version.id"
                  class="bg-gray-50 rounded-xl border border-gray-200 p-6"
                >
                  <div class="flex justify-between items-center mb-4">
                    <span class="text-sm font-medium text-gray-600">v{{ version.version }} — {{ formatDate(version.updated_at) }}</span>
                  </div>
                  <div class="space-y-5">
                    <div v-for="field in selectedTemplate.structure" :key="field.id">
                      <h5 class="text-xs font-bold uppercase tracking-wide text-gray-500 mb-2">{{ field.label }}</h5>
                      <p class="text-sm text-gray-800 whitespace-pre-wrap leading-relaxed pl-3 border-l-2 border-gray-200">{{ version?.content?.[field.id] || '—' }}</p>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Empty State -->
              <div v-if="!currentCheckIn" class="bg-white rounded-2xl border border-gray-200 shadow-lg p-12 text-center">
                <div class="text-6xl mb-4">{{ selectedTemplate.icon }}</div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Not created yet</h3>
                <p class="text-gray-600 mb-6">Start by creating your first entry for {{ selectedTemplate.name }}</p>
                <button
                  @click="openCreateModal"
                  class="px-6 py-3 bg-gradient-to-r from-green-500 to-emerald-600 text-white font-semibold rounded-lg hover:from-green-600 hover:to-emerald-700 transition-all shadow-lg shadow-green-500/30"
                >
                  Create {{ selectedTemplate.name }}
                </button>
              </div>
            </div>

            <!-- Recurring: Show inline timeline view -->
            <div v-else-if="selectedTemplate.behavior === 'recurring'">
              <!-- Context Panel for Reference (Daily Check-in only) -->
              <div v-if="contextEntries.length > 0 && selectedTemplate.name === 'Daily Check-in'" class="mb-6 space-y-3">
                <h3 class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                  <span>💡</span> Reference (for inspiration)
                </h3>
                <div
                  v-for="context in contextEntries"
                  :key="context.id"
                  class="bg-gradient-to-r from-blue-50 to-cyan-50 rounded-xl border border-blue-200 overflow-hidden"
                >
                  <!-- Context Header (Always Visible, Clickable) -->
                  <div
                    @click="toggleContextEntry(context.id)"
                    class="p-4 cursor-pointer hover:bg-blue-100/50 transition-colors"
                  >
                    <div class="flex justify-between items-center">
                      <div class="flex items-center gap-3">
                        <span class="text-lg">{{ context.templateIcon }}</span>
                        <div>
                          <h4 class="font-semibold text-gray-900">{{ context.title }}</h4>
                          <p class="text-xs text-blue-600">{{ context.subtitle }}</p>
                        </div>
                      </div>
                      <svg
                        class="w-5 h-5 text-gray-400 transition-transform"
                        :class="expandedContextEntries.has(context.id) ? 'rotate-180' : ''"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                      >
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                      </svg>
                    </div>
                  </div>

                  <!-- Context Content (Expandable) -->
                  <Transition name="expand">
                    <div v-if="expandedContextEntries.has(context.id)" class="px-4 pb-4 border-t border-blue-200/50">
                      <div class="space-y-4 mt-4">
                        <div v-for="field in context.structure" :key="field.id">
                          <h5 class="text-xs font-bold uppercase tracking-wide text-gray-500 mb-2">{{ field.label }}</h5>
                          <p class="text-sm text-gray-800 whitespace-pre-wrap leading-relaxed pl-3 border-l-2 border-blue-300">{{ context.content?.[field.id] || '—' }}</p>
                        </div>
                      </div>
                    </div>
                  </Transition>
                </div>
              </div>

              <!-- Inline Create/Edit Form -->
              <div v-if="showInlineForm" class="bg-white rounded-2xl border-2 border-green-500 shadow-lg p-6 mb-6">
                <div class="flex justify-between items-center mb-4">
                  <h3 class="text-lg font-bold text-gray-900">{{ editingCheckIn ? 'Edit Entry' : 'New Entry' }}</h3>
                  <button @click="cancelInlineForm" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                  </button>
                </div>

                <form @submit.prevent="saveInlineCheckIn" class="space-y-4">
                  <!-- Date Field -->
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                      {{ selectedTemplate.name === 'Weekly Review' ? 'Week Ending Date' : 'Date' }}
                    </label>
                    <input
                      v-model="formData.date"
                      type="date"
                      required
                      class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                    />
                    <p v-if="selectedTemplate.name === 'Weekly Review'" class="text-xs text-gray-500 mt-1">
                      This will be displayed as a week range (e.g., "Week 5 - Sept. 13-19")
                    </p>
                  </div>

                  <!-- Dynamic Fields -->
                  <div v-for="field in selectedTemplate.structure" :key="field.id" class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700">
                      {{ field.label }}
                    </label>

                    <!-- Textarea -->
                    <div v-if="field.type === 'textarea'" class="relative">
                      <textarea
                        :ref="el => { if (el) textFieldRefs[field.id] = el }"
                        v-model="formData.content[field.id]"
                        :rows="field.rows || 4"
                        :placeholder="field.placeholder || 'Share your thoughts...'"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent resize-none"
                      ></textarea>
                      <button
                        type="button"
                        @click="openEmojiPicker($event, field.id)"
                        class="absolute top-2 right-2 p-1.5 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded transition-colors"
                        title="Insert emoji"
                      >
                        😊
                      </button>
                    </div>

                    <!-- Text -->
                    <div v-else-if="field.type === 'text'" class="relative">
                      <input
                        :ref="el => { if (el) textFieldRefs[field.id] = el }"
                        v-model="formData.content[field.id]"
                        type="text"
                        :placeholder="field.placeholder || ''"
                        class="w-full px-4 py-2.5 pr-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                      />
                      <button
                        type="button"
                        @click="openEmojiPicker($event, field.id)"
                        class="absolute top-1/2 -translate-y-1/2 right-2 p-1.5 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded transition-colors"
                        title="Insert emoji"
                      >
                        😊
                      </button>
                    </div>

                    <!-- Select -->
                    <select
                      v-else-if="field.type === 'select'"
                      v-model="formData.content[field.id]"
                      class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                    >
                      <option value="">Select...</option>
                      <option v-for="option in field.options" :key="option" :value="option">
                        {{ option }}
                      </option>
                    </select>

                    <!-- Number -->
                    <input
                      v-else-if="field.type === 'number'"
                      v-model.number="formData.content[field.id]"
                      type="number"
                      :min="field.min"
                      :max="field.max"
                      class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                    />

                    <!-- Checkbox -->
                    <label
                      v-else-if="field.type === 'checkbox'"
                      class="flex items-center gap-2 cursor-pointer"
                    >
                      <input
                        v-model="formData.content[field.id]"
                        type="checkbox"
                        class="w-5 h-5 text-green-500 rounded focus:ring-2 focus:ring-green-500"
                      />
                      <span class="text-sm text-gray-700">{{ field.placeholder || 'Check if yes' }}</span>
                    </label>

                    <!-- Checklist -->
                    <div v-else-if="field.type === 'checklist'" class="space-y-3">
                      <div
                        v-for="(item, idx) in formData.content[field.id]"
                        :key="idx"
                        class="flex items-center gap-2"
                      >
                        <input
                          v-model="item.checked"
                          type="checkbox"
                          class="w-5 h-5 text-green-500 rounded focus:ring-2 focus:ring-green-500 flex-shrink-0"
                        />
                        <input
                          v-model="item.text"
                          type="text"
                          placeholder="Item..."
                          class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                        />
                        <button
                          type="button"
                          @click="removeChecklistItem(field.id, idx)"
                          class="p-2 text-red-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                        >
                          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                          </svg>
                        </button>
                      </div>
                      <button
                        type="button"
                        @click="addChecklistItem(field.id)"
                        class="text-sm text-green-600 hover:text-green-700 font-medium flex items-center gap-1"
                      >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Add item
                      </button>
                    </div>
                  </div>

                  <!-- Actions -->
                  <div class="flex gap-3 pt-4">
                    <button
                      type="submit"
                      :disabled="saving || !hasChanges"
                      class="flex-1 px-6 py-3 bg-gradient-to-r from-green-500 to-emerald-600 text-white font-semibold rounded-lg hover:from-green-600 hover:to-emerald-700 disabled:opacity-50 disabled:cursor-not-allowed transition-all shadow-lg shadow-green-500/30"
                    >
                      {{ saving ? 'Saving...' : (editingCheckIn ? 'Save Changes' : 'Save Entry') }}
                    </button>
                    <button
                      type="button"
                      @click="cancelInlineForm"
                      class="px-6 py-3 border-2 border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-all"
                    >
                      Cancel
                    </button>
                  </div>
                </form>
              </div>

              <!-- Timeline of Entries -->
              <div v-if="checkIns.length > 0" class="space-y-4">
                <div
                  v-for="(checkIn, index) in checkIns"
                  :key="checkIn.id"
                  v-show="!editingCheckIn || editingCheckIn.id !== checkIn.id"
                  class="bg-white rounded-2xl border border-gray-200 shadow-lg transition-all"
                  :class="expandedEntries.has(checkIn.id) ? 'ring-2 ring-green-500' : ''"
                >
                  <!-- Entry Header (Always Visible) -->
                  <div
                    @click="toggleEntry(checkIn.id)"
                    class="p-6 cursor-pointer hover:bg-gray-50 transition-colors"
                  >
                    <div class="flex justify-between items-start">
                      <div class="flex-1">
                        <div class="flex items-center gap-3 mb-1">
                          <h3 class="text-lg font-bold text-gray-900">
                            {{ selectedTemplate.name === 'Weekly Review' ? formatWeekRange(checkIn.date) : formatDateShort(checkIn.date) }}
                          </h3>
                          <span v-if="index === 0" class="px-2 py-0.5 bg-green-100 text-green-700 text-xs font-bold rounded-full">
                            Latest
                          </span>
                        </div>
                        <p class="text-xs text-gray-500">
                          {{ selectedTemplate.name === 'Weekly Review' ? 'Week ending ' + formatDateShort(checkIn.date) : formatRelativeDate(checkIn.date) }}
                        </p>
                      </div>

                      <div class="flex items-center gap-2">
                        <button
                          @click.stop="startEditInline(checkIn)"
                          class="text-blue-500 hover:text-blue-600 transition-colors p-1"
                        >
                          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                          </svg>
                        </button>
                        <button
                          @click.stop="deleteCheckIn(checkIn.id)"
                          class="text-red-400 hover:text-red-500 transition-colors p-1"
                        >
                          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                          </svg>
                        </button>
                        <svg
                          class="w-5 h-5 text-gray-400 transition-transform"
                          :class="expandedEntries.has(checkIn.id) ? 'rotate-180' : ''"
                          fill="none"
                          stroke="currentColor"
                          viewBox="0 0 24 24"
                        >
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                      </div>
                    </div>
                  </div>

                  <!-- Entry Content (Expandable) -->
                  <Transition name="expand">
                    <div v-if="expandedEntries.has(checkIn.id)" class="px-6 pb-6 pt-2 border-t border-gray-100">
                      <div class="space-y-5">
                        <div v-for="field in selectedTemplate.structure" :key="field.id">
                          <h4 class="text-xs font-bold uppercase tracking-wide text-gray-500 mb-2">{{ field.label }}</h4>

                          <!-- Textarea / Text -->
                          <p v-if="field.type === 'textarea' || field.type === 'text'" class="text-base text-gray-900 whitespace-pre-wrap leading-relaxed pl-3 border-l-2 border-gray-200">
                            {{ checkIn?.content?.[field.id] || '—' }}
                          </p>

                          <!-- Number -->
                          <p v-else-if="field.type === 'number'" class="text-base text-gray-900 pl-3 border-l-2 border-gray-200">
                            {{ checkIn?.content?.[field.id] ?? '—' }}
                          </p>

                          <!-- Select -->
                          <p v-else-if="field.type === 'select'" class="text-base text-gray-900 pl-3 border-l-2 border-gray-200">
                            {{ checkIn?.content?.[field.id] || '—' }}
                          </p>

                          <!-- Checkbox -->
                          <div v-else-if="field.type === 'checkbox'" class="pl-3">
                            <span v-if="checkIn?.content?.[field.id]" class="inline-flex items-center gap-2 text-green-600 font-medium">
                              <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                              </svg>
                              Yes
                            </span>
                            <span v-else class="text-gray-400">No</span>
                          </div>

                          <!-- Checklist -->
                          <div v-else-if="field.type === 'checklist'" class="pl-3 space-y-2">
                            <div v-if="checkIn?.content?.[field.id]?.length > 0">
                              <div v-for="(item, idx) in checkIn.content[field.id]" :key="idx" class="flex items-start gap-2">
                                <svg v-if="item.checked" class="w-5 h-5 text-green-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <svg v-else class="w-5 h-5 text-gray-300 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm0-2a6 6 0 100-12 6 6 0 000 12z" clip-rule="evenodd"/>
                                </svg>
                                <span :class="item.checked ? 'line-through text-gray-500' : 'text-gray-900'">{{ item.text }}</span>
                              </div>
                            </div>
                            <p v-else class="text-gray-400">—</p>
                          </div>
                        </div>
                      </div>
                    </div>
                  </Transition>
                </div>
              </div>

              <!-- Empty State -->
              <div v-else class="bg-white rounded-2xl border border-gray-200 shadow-lg p-12 text-center">
                <div class="text-6xl mb-4">{{ selectedTemplate.icon }}</div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">No entries yet</h3>
                <p class="text-gray-600 mb-6">Create your first {{ selectedTemplate.name }}</p>
              </div>
            </div>
          </div>

          <!-- Dashboard Overview -->
          <div v-else>
            <div class="mb-6">
              <h1 class="text-3xl font-bold text-gray-900 mb-2">Overview</h1>
              <p class="text-gray-600">Your progress at a glance</p>
            </div>

            <div v-if="dashboardData" class="space-y-6">
              <!-- Quick Actions (Smart - adapts to visible templates) -->
              <div v-if="quickActionButton1 || quickActionButton2" class="flex gap-3">
                <button
                  v-if="quickActionButton1"
                  @click="selectTemplate(quickActionButton1); setTimeout(() => openCreateModal(), 100)"
                  class="flex-1 bg-gradient-to-r from-blue-500 to-cyan-600 text-white px-4 py-3 rounded-xl font-semibold hover:from-blue-600 hover:to-cyan-700 transition-all shadow-lg shadow-blue-500/30 flex items-center justify-center gap-2"
                >
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                  </svg>
                  Quick {{ quickActionButton1.name }}
                </button>
                <button
                  v-if="quickActionButton2"
                  @click="selectTemplate(quickActionButton2); setTimeout(() => openCreateModal(), 100)"
                  class="px-4 py-3 border-2 border-gray-300 text-gray-700 font-semibold rounded-xl hover:bg-gray-50 transition-all flex items-center gap-2"
                  :class="!quickActionButton1 ? 'flex-1' : ''"
                >
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                  </svg>
                  {{ quickActionButton2.name }}
                </button>
              </div>

              <!-- Random Vision Reminder -->
              <div v-if="randomVisionItem" class="bg-gradient-to-br from-purple-50 to-indigo-50 rounded-2xl border border-purple-200 p-6">
                <div class="flex items-start justify-between mb-4">
                  <div class="flex items-center gap-3">
                    <span class="text-3xl">{{ randomVisionItem.template.icon }}</span>
                    <div>
                      <h3 class="text-lg font-bold text-gray-900">{{ randomVisionItem.template.name }}</h3>
                      <p class="text-xs text-purple-600">Your vision reminder</p>
                    </div>
                  </div>
                  <button
                    @click="selectTemplateById(randomVisionItem.template_id)"
                    class="text-purple-600 hover:text-purple-700 text-sm font-medium"
                  >
                    View →
                  </button>
                </div>
                <div class="text-sm text-gray-700 bg-white/60 rounded-lg p-4 italic line-clamp-3">
                  "{{ getFirstFieldPreview(randomVisionItem) }}"
                </div>
              </div>

              <!-- Latest Reviews (Dynamic - shows any visible review templates) -->
              <div v-if="latestReviews.length > 0 || reviewTemplates.length > 0">
                <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                  <span>📝</span>
                  Latest Check-ins
                </h2>
                <div class="grid grid-cols-2 gap-4">
                  <!-- First latest review -->
                  <div v-if="latestReviews[0]" class="bg-white rounded-xl border-2 border-blue-200 p-6 hover:shadow-lg transition-all group cursor-pointer" @click="selectTemplateById(latestReviews[0].template_id)">
                    <div class="flex items-center justify-between mb-5">
                      <div class="flex items-center gap-2">
                        <span class="text-3xl">{{ latestReviews[0].template.icon }}</span>
                        <div>
                          <div class="text-xs font-semibold text-blue-600 uppercase">{{ latestReviews[0].template.name }}</div>
                          <div class="text-sm font-bold text-gray-900">{{ latestReviews[0].template.period === 'weekly' ? formatWeekRange(latestReviews[0].date) : formatDateShort(latestReviews[0].date) }}</div>
                        </div>
                      </div>
                      <span class="text-xs text-gray-500">{{ formatRelativeDate(latestReviews[0].date) }}</span>
                    </div>
                    <!-- Show first 4-5 fields with labels -->
                    <div class="space-y-3.5">
                      <div v-for="(field, index) in latestReviews[0].template.structure.slice(0, 5)" :key="field.id">
                        <div class="text-xs font-semibold text-gray-500 uppercase mb-1.5">{{ field.label }}</div>

                        <!-- Textarea / Text -->
                        <div v-if="field.type === 'textarea' || field.type === 'text'" class="text-sm text-gray-700 leading-relaxed" :class="index >= 4 ? 'line-clamp-2' : 'line-clamp-4'">
                          {{ latestReviews[0].content?.[field.id] || '—' }}
                        </div>

                        <!-- Number / Select -->
                        <div v-else-if="field.type === 'number' || field.type === 'select'" class="text-sm text-gray-700 font-medium">
                          {{ latestReviews[0].content?.[field.id] ?? '—' }}
                        </div>

                        <!-- Checkbox -->
                        <div v-else-if="field.type === 'checkbox'" class="text-sm">
                          <span v-if="latestReviews[0].content?.[field.id]" class="text-green-600 font-medium">✓ Yes</span>
                          <span v-else class="text-gray-400">✗ No</span>
                        </div>

                        <!-- Checklist -->
                        <div v-else-if="field.type === 'checklist'" class="text-sm space-y-1.5">
                          <div v-if="latestReviews[0].content?.[field.id]?.length > 0" class="space-y-1.5">
                            <div v-for="(item, idx) in latestReviews[0].content[field.id].slice(0, 5)" :key="idx" class="flex items-start gap-2">
                              <span v-if="item.checked" class="text-green-500 text-base">✓</span>
                              <span v-else class="text-gray-300 text-base">○</span>
                              <span :class="item.checked ? 'line-through text-gray-500' : 'text-gray-700'">{{ item.text }}</span>
                            </div>
                            <div v-if="latestReviews[0].content[field.id].length > 5" class="text-xs text-gray-400 ml-6">
                              +{{ latestReviews[0].content[field.id].length - 5 }} more items
                            </div>
                          </div>
                          <span v-else class="text-gray-400">—</span>
                        </div>
                      </div>
                      <div v-if="latestReviews[0].template.structure.length > 5" class="text-xs text-gray-400 italic pt-2 border-t border-gray-100">
                        +{{ latestReviews[0].template.structure.length - 5 }} more fields
                      </div>
                    </div>
                  </div>
                  <div v-else class="bg-blue-50 rounded-xl border-2 border-dashed border-blue-200 p-6 text-center">
                    <div class="text-3xl mb-2">📝</div>
                    <p class="text-sm text-gray-600 font-medium mb-3">No check-ins yet</p>
                    <button v-if="reviewTemplates[0]" @click="selectTemplate(reviewTemplates[0])" class="text-sm text-blue-600 hover:text-blue-700 font-semibold">
                      Create {{ reviewTemplates[0].name }} →
                    </button>
                  </div>

                  <!-- Second latest review -->
                  <div v-if="latestReviews[1]" class="bg-white rounded-xl border-2 border-green-200 p-6 hover:shadow-lg transition-all group cursor-pointer" @click="selectTemplateById(latestReviews[1].template_id)">
                    <div class="flex items-center justify-between mb-5">
                      <div class="flex items-center gap-2">
                        <span class="text-3xl">{{ latestReviews[1].template.icon }}</span>
                        <div>
                          <div class="text-xs font-semibold text-green-600 uppercase">{{ latestReviews[1].template.name }}</div>
                          <div class="text-sm font-bold text-gray-900">{{ latestReviews[1].template.period === 'weekly' ? formatWeekRange(latestReviews[1].date) : formatDateShort(latestReviews[1].date) }}</div>
                        </div>
                      </div>
                      <span class="text-xs text-gray-500">{{ formatRelativeDate(latestReviews[1].date) }}</span>
                    </div>
                    <!-- Show first 4-5 fields with labels -->
                    <div class="space-y-3.5">
                      <div v-for="(field, index) in latestReviews[1].template.structure.slice(0, 5)" :key="field.id">
                        <div class="text-xs font-semibold text-gray-500 uppercase mb-1.5">{{ field.label }}</div>

                        <!-- Textarea / Text -->
                        <div v-if="field.type === 'textarea' || field.type === 'text'" class="text-sm text-gray-700 leading-relaxed" :class="index >= 4 ? 'line-clamp-2' : 'line-clamp-4'">
                          {{ latestReviews[1].content?.[field.id] || '—' }}
                        </div>

                        <!-- Number / Select -->
                        <div v-else-if="field.type === 'number' || field.type === 'select'" class="text-sm text-gray-700 font-medium">
                          {{ latestReviews[1].content?.[field.id] ?? '—' }}
                        </div>

                        <!-- Checkbox -->
                        <div v-else-if="field.type === 'checkbox'" class="text-sm">
                          <span v-if="latestReviews[1].content?.[field.id]" class="text-green-600 font-medium">✓ Yes</span>
                          <span v-else class="text-gray-400">✗ No</span>
                        </div>

                        <!-- Checklist -->
                        <div v-else-if="field.type === 'checklist'" class="text-sm space-y-1.5">
                          <div v-if="latestReviews[1].content?.[field.id]?.length > 0" class="space-y-1.5">
                            <div v-for="(item, idx) in latestReviews[1].content[field.id].slice(0, 5)" :key="idx" class="flex items-start gap-2">
                              <span v-if="item.checked" class="text-green-500 text-base">✓</span>
                              <span v-else class="text-gray-300 text-base">○</span>
                              <span :class="item.checked ? 'line-through text-gray-500' : 'text-gray-700'">{{ item.text }}</span>
                            </div>
                            <div v-if="latestReviews[1].content[field.id].length > 5" class="text-xs text-gray-400 ml-6">
                              +{{ latestReviews[1].content[field.id].length - 5 }} more items
                            </div>
                          </div>
                          <span v-else class="text-gray-400">—</span>
                        </div>
                      </div>
                      <div v-if="latestReviews[1].template.structure.length > 5" class="text-xs text-gray-400 italic pt-2 border-t border-gray-100">
                        +{{ latestReviews[1].template.structure.length - 5 }} more fields
                      </div>
                    </div>
                  </div>
                  <div v-else-if="latestReviews.length < 2 && reviewTemplates[1]" class="bg-green-50 rounded-xl border-2 border-dashed border-green-200 p-6 text-center">
                    <div class="text-3xl mb-2">📝</div>
                    <p class="text-sm text-gray-600 font-medium mb-3">No {{ reviewTemplates[1].name }} yet</p>
                    <button @click="selectTemplate(reviewTemplates[1])" class="text-sm text-green-600 hover:text-green-700 font-semibold">
                      Create one now →
                    </button>
                  </div>
                </div>
              </div>

              <!-- Active Goals (Enhanced Cards) -->
              <div v-if="currentGoals.length > 0">
                <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                  <span>🎯</span>
                  Active Goals
                  <span class="text-sm font-normal text-gray-500">({{ currentGoals.length }})</span>
                </h2>
                <div class="grid gap-4" :class="currentGoals.length === 1 ? 'grid-cols-1' : 'grid-cols-2'">
                  <div
                    v-for="goal in currentGoals"
                    :key="goal.id"
                    @click="selectTemplateById(goal.template_id)"
                    class="bg-white rounded-xl border-2 border-amber-200 p-6 cursor-pointer hover:shadow-lg transition-all group"
                  >
                    <div class="flex items-start justify-between mb-4">
                      <div class="flex items-center gap-2.5">
                        <span class="text-3xl">{{ goal.template.icon }}</span>
                        <div>
                          <h3 class="font-bold text-gray-900 group-hover:text-amber-600 transition-colors">{{ goal.template.name }}</h3>
                          <span class="text-xs text-amber-600 font-semibold uppercase">v{{ goal.version || 1 }}</span>
                        </div>
                      </div>
                      <div class="text-right">
                        <span class="text-xs text-gray-500">Updated</span>
                        <div class="text-xs text-gray-700 font-medium">{{ formatRelativeDate(goal.updated_at) }}</div>
                      </div>
                    </div>

                    <!-- Show first 2-3 fields with content -->
                    <div class="space-y-3">
                      <div v-for="(field, index) in goal.template.structure.slice(0, 3)" :key="field.id">
                        <div class="text-xs font-semibold text-gray-500 uppercase mb-1">{{ field.label }}</div>

                        <!-- Textarea / Text -->
                        <div v-if="field.type === 'textarea' || field.type === 'text'" class="text-sm text-gray-700 leading-relaxed line-clamp-3">
                          {{ goal.content?.[field.id] || '—' }}
                        </div>

                        <!-- Number / Select -->
                        <div v-else-if="field.type === 'number' || field.type === 'select'" class="text-sm text-gray-700 font-medium">
                          {{ goal.content?.[field.id] ?? '—' }}
                        </div>

                        <!-- Checkbox -->
                        <div v-else-if="field.type === 'checkbox'" class="text-sm">
                          <span v-if="goal.content?.[field.id]" class="text-green-600 font-medium">✓ Yes</span>
                          <span v-else class="text-gray-400">✗ No</span>
                        </div>

                        <!-- Checklist -->
                        <div v-else-if="field.type === 'checklist'" class="text-sm space-y-1">
                          <div v-if="goal.content?.[field.id]?.length > 0" class="space-y-1">
                            <div v-for="(item, idx) in goal.content[field.id].slice(0, 3)" :key="idx" class="flex items-start gap-2">
                              <span v-if="item.checked" class="text-green-500 text-base">✓</span>
                              <span v-else class="text-gray-300 text-base">○</span>
                              <span :class="item.checked ? 'line-through text-gray-500' : 'text-gray-700'">{{ item.text }}</span>
                            </div>
                            <div v-if="goal.content[field.id].length > 3" class="text-xs text-gray-400 ml-6">
                              +{{ goal.content[field.id].length - 3 }} more items
                            </div>
                          </div>
                          <span v-else class="text-gray-400">—</span>
                        </div>
                      </div>
                      <div v-if="goal.template.structure.length > 3" class="text-xs text-gray-400 italic pt-2 border-t border-gray-100">
                        +{{ goal.template.structure.length - 3 }} more fields
                      </div>
                    </div>

                    <!-- View arrow -->
                    <div class="flex justify-end mt-4 pt-3 border-t border-gray-100">
                      <span class="text-xs text-amber-600 group-hover:text-amber-700 font-semibold flex items-center gap-1">
                        View Full Goal
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                      </span>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Empty State -->
            <div v-else class="bg-white rounded-2xl border border-gray-200 shadow-lg p-12 text-center">
              <div class="text-6xl mb-4">📊</div>
              <h3 class="text-xl font-semibold text-gray-900 mb-2">Loading...</h3>
            </div>
          </div>
        </div>
      </div>
    </main>

    <!-- Template Manager Modal -->
    <Transition name="modal">
      <div v-if="showTemplateManager" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4" @click.self="closeTemplateManager">
        <div v-if="!showTemplateForm" class="bg-white rounded-2xl shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
            <div class="sticky top-0 bg-white border-b border-gray-200 px-8 py-6 flex justify-between items-center">
              <h2 class="text-2xl font-bold text-gray-900">Manage Templates</h2>
              <div class="flex gap-3">
                <button
                  @click="openNewTemplateForm"
                  class="px-4 py-2 bg-green-500 text-white text-sm font-semibold rounded-lg hover:bg-green-600 transition-all"
                >
                  + Create Template
                </button>
                <button @click="closeTemplateManager" class="text-gray-400 hover:text-gray-600 transition-colors">
                  <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                  </svg>
                </button>
              </div>
            </div>

            <div class="p-8 space-y-8">
              <!-- Vision Templates -->
              <div v-if="groupedTemplates.vision.length > 0">
                <div class="flex items-center gap-2 mb-4">
                  <span class="text-2xl">🎯</span>
                  <h3 class="text-lg font-bold text-gray-900">Vision</h3>
                  <span class="text-xs text-gray-500">({{ groupedTemplates.vision.length }})</span>
                </div>
                <div class="space-y-3">
                  <div
                    v-for="(template, index) in groupedTemplates.vision"
                    :key="template.id"
                    class="border border-gray-200 rounded-xl p-4 hover:shadow-md transition-shadow"
                    :class="template.is_hidden ? 'opacity-50' : ''"
                  >
                    <div class="flex items-start justify-between">
                      <div class="flex items-start gap-3 flex-1">
                        <span class="text-2xl">{{ template.icon }}</span>
                        <div class="flex-1">
                          <h3 class="font-bold text-gray-900">{{ template.name }}</h3>
                          <div class="flex items-center gap-2 mt-1">
                            <span class="text-xs text-gray-500">{{ template.behavior }}</span>
                            <span v-if="template.is_system" class="text-xs px-2 py-0.5 bg-gray-100 text-gray-600 rounded-full">
                              System
                            </span>
                          </div>
                          <p class="text-xs text-gray-500 mt-2">{{ template.structure.length }} field(s)</p>
                        </div>
                      </div>

                      <div class="flex items-center gap-2">
                        <!-- Reorder Controls -->
                        <div class="flex flex-col gap-0.5">
                          <button
                            @click="moveTemplateUp('vision', index)"
                            :disabled="index === 0"
                            class="p-1 hover:bg-gray-100 rounded transition-colors"
                            :class="index === 0 ? 'opacity-30 cursor-not-allowed' : 'text-gray-600'"
                            title="Move up"
                          >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                            </svg>
                          </button>
                          <button
                            @click="moveTemplateDown('vision', index)"
                            :disabled="index === groupedTemplates.vision.length - 1"
                            class="p-1 hover:bg-gray-100 rounded transition-colors"
                            :class="index === groupedTemplates.vision.length - 1 ? 'opacity-30 cursor-not-allowed' : 'text-gray-600'"
                            title="Move down"
                          >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                          </button>
                        </div>

                        <!-- Toggle Visibility -->
                        <button
                          @click="toggleTemplateVisibility(template)"
                          class="p-2 hover:bg-gray-100 rounded-lg transition-colors"
                          :title="template.is_hidden ? 'Show' : 'Hide'"
                        >
                          <svg v-if="template.is_hidden" class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                          </svg>
                          <svg v-else class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                          </svg>
                        </button>

                        <!-- Duplicate -->
                        <button
                          @click="duplicateTemplate(template)"
                          class="p-2 text-green-500 hover:bg-green-50 rounded-lg transition-colors"
                          title="Duplicate"
                        >
                          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                          </svg>
                        </button>

                        <!-- Edit (only for user templates) -->
                        <button
                          v-if="!template.is_system"
                          @click="openEditTemplateForm(template)"
                          class="p-2 text-blue-500 hover:bg-blue-50 rounded-lg transition-colors"
                          title="Edit"
                        >
                          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                          </svg>
                        </button>

                        <!-- Delete (only for user templates) -->
                        <button
                          v-if="!template.is_system"
                          @click="deleteTemplate(template)"
                          class="p-2 text-red-400 hover:bg-red-50 rounded-lg transition-colors"
                          title="Delete"
                        >
                          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                          </svg>
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Goals Templates -->
              <div v-if="groupedTemplates.goal.length > 0">
                <div class="flex items-center gap-2 mb-4">
                  <span class="text-2xl">🎯</span>
                  <h3 class="text-lg font-bold text-gray-900">Goals</h3>
                  <span class="text-xs text-gray-500">({{ groupedTemplates.goal.length }})</span>
                </div>
                <div class="space-y-3">
                  <div
                    v-for="(template, index) in groupedTemplates.goal"
                    :key="template.id"
                    class="border border-gray-200 rounded-xl p-4 hover:shadow-md transition-shadow"
                    :class="template.is_hidden ? 'opacity-50' : ''"
                  >
                    <div class="flex items-start justify-between">
                      <div class="flex items-start gap-3 flex-1">
                        <span class="text-2xl">{{ template.icon }}</span>
                        <div class="flex-1">
                          <h3 class="font-bold text-gray-900">{{ template.name }}</h3>
                          <div class="flex items-center gap-2 mt-1">
                            <span class="text-xs text-gray-500">{{ template.behavior }}</span>
                            <span v-if="template.is_system" class="text-xs px-2 py-0.5 bg-gray-100 text-gray-600 rounded-full">
                              System
                            </span>
                          </div>
                          <p class="text-xs text-gray-500 mt-2">{{ template.structure.length }} field(s)</p>
                        </div>
                      </div>

                      <div class="flex items-center gap-2">
                        <!-- Reorder Controls -->
                        <div class="flex flex-col gap-0.5">
                          <button
                            @click="moveTemplateUp('goal', index)"
                            :disabled="index === 0"
                            class="p-1 hover:bg-gray-100 rounded transition-colors"
                            :class="index === 0 ? 'opacity-30 cursor-not-allowed' : 'text-gray-600'"
                            title="Move up"
                          >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                            </svg>
                          </button>
                          <button
                            @click="moveTemplateDown('goal', index)"
                            :disabled="index === groupedTemplates.goal.length - 1"
                            class="p-1 hover:bg-gray-100 rounded transition-colors"
                            :class="index === groupedTemplates.goal.length - 1 ? 'opacity-30 cursor-not-allowed' : 'text-gray-600'"
                            title="Move down"
                          >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                          </button>
                        </div>

                        <!-- Toggle Visibility -->
                        <button
                          @click="toggleTemplateVisibility(template)"
                          class="p-2 hover:bg-gray-100 rounded-lg transition-colors"
                          :title="template.is_hidden ? 'Show' : 'Hide'"
                        >
                          <svg v-if="template.is_hidden" class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                          </svg>
                          <svg v-else class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                          </svg>
                        </button>

                        <!-- Duplicate -->
                        <button
                          @click="duplicateTemplate(template)"
                          class="p-2 text-green-500 hover:bg-green-50 rounded-lg transition-colors"
                          title="Duplicate"
                        >
                          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                          </svg>
                        </button>

                        <!-- Edit (only for user templates) -->
                        <button
                          v-if="!template.is_system"
                          @click="openEditTemplateForm(template)"
                          class="p-2 text-blue-500 hover:bg-blue-50 rounded-lg transition-colors"
                          title="Edit"
                        >
                          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                          </svg>
                        </button>

                        <!-- Delete (only for user templates) -->
                        <button
                          v-if="!template.is_system"
                          @click="deleteTemplate(template)"
                          class="p-2 text-red-400 hover:bg-red-50 rounded-lg transition-colors"
                          title="Delete"
                        >
                          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                          </svg>
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Reviews Templates -->
              <div v-if="groupedTemplates.review.length > 0">
                <div class="flex items-center gap-2 mb-4">
                  <span class="text-2xl">📊</span>
                  <h3 class="text-lg font-bold text-gray-900">Reviews</h3>
                  <span class="text-xs text-gray-500">({{ groupedTemplates.review.length }})</span>
                </div>
                <div class="space-y-3">
                  <div
                    v-for="(template, index) in groupedTemplates.review"
                    :key="template.id"
                    class="border border-gray-200 rounded-xl p-4 hover:shadow-md transition-shadow"
                    :class="template.is_hidden ? 'opacity-50' : ''"
                  >
                    <div class="flex items-start justify-between">
                      <div class="flex items-start gap-3 flex-1">
                        <span class="text-2xl">{{ template.icon }}</span>
                        <div class="flex-1">
                          <h3 class="font-bold text-gray-900">{{ template.name }}</h3>
                          <div class="flex items-center gap-2 mt-1">
                            <span class="text-xs text-gray-500">{{ template.behavior }}</span>
                            <span v-if="template.is_system" class="text-xs px-2 py-0.5 bg-gray-100 text-gray-600 rounded-full">
                              System
                            </span>
                          </div>
                          <p class="text-xs text-gray-500 mt-2">{{ template.structure.length }} field(s)</p>
                        </div>
                      </div>

                      <div class="flex items-center gap-2">
                        <!-- Reorder Controls -->
                        <div class="flex flex-col gap-0.5">
                          <button
                            @click="moveTemplateUp('review', index)"
                            :disabled="index === 0"
                            class="p-1 hover:bg-gray-100 rounded transition-colors"
                            :class="index === 0 ? 'opacity-30 cursor-not-allowed' : 'text-gray-600'"
                            title="Move up"
                          >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                            </svg>
                          </button>
                          <button
                            @click="moveTemplateDown('review', index)"
                            :disabled="index === groupedTemplates.review.length - 1"
                            class="p-1 hover:bg-gray-100 rounded transition-colors"
                            :class="index === groupedTemplates.review.length - 1 ? 'opacity-30 cursor-not-allowed' : 'text-gray-600'"
                            title="Move down"
                          >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                          </button>
                        </div>

                        <!-- Toggle Visibility -->
                        <button
                          @click="toggleTemplateVisibility(template)"
                          class="p-2 hover:bg-gray-100 rounded-lg transition-colors"
                          :title="template.is_hidden ? 'Show' : 'Hide'"
                        >
                          <svg v-if="template.is_hidden" class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                          </svg>
                          <svg v-else class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                          </svg>
                        </button>

                        <!-- Duplicate -->
                        <button
                          @click="duplicateTemplate(template)"
                          class="p-2 text-green-500 hover:bg-green-50 rounded-lg transition-colors"
                          title="Duplicate"
                        >
                          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                          </svg>
                        </button>

                        <!-- Edit (only for user templates) -->
                        <button
                          v-if="!template.is_system"
                          @click="openEditTemplateForm(template)"
                          class="p-2 text-blue-500 hover:bg-blue-50 rounded-lg transition-colors"
                          title="Edit"
                        >
                          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                          </svg>
                        </button>

                        <!-- Delete (only for user templates) -->
                        <button
                          v-if="!template.is_system"
                          @click="deleteTemplate(template)"
                          class="p-2 text-red-400 hover:bg-red-50 rounded-lg transition-colors"
                          title="Delete"
                        >
                          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                          </svg>
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Template Form -->
          <div v-if="showTemplateForm" class="bg-white rounded-2xl shadow-2xl max-w-5xl w-full max-h-[90vh] overflow-hidden flex flex-col">
            <div class="sticky top-0 bg-gradient-to-r from-green-500 to-emerald-600 px-8 py-6 flex justify-between items-center">
              <div>
                <h2 class="text-2xl font-bold text-white">
                  {{ editingTemplate ? 'Edit Template' : 'Create Your Template' }}
                </h2>
                <p class="text-green-100 text-sm mt-1">Design a custom form for your vision, goals, or reviews</p>
              </div>
              <button @click="showTemplateForm = false" class="text-white/80 hover:text-white transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
              </button>
            </div>

            <form @submit.prevent="saveTemplate" class="flex-1 overflow-y-auto">
              <div class="grid grid-cols-2 gap-6 p-8">
                <!-- LEFT COLUMN: Form Builder -->
                <div class="space-y-6">
                  <!-- Step 1: Basic Info -->
                  <div class="bg-gray-50 rounded-xl p-6 border-2 border-gray-200">
                    <div class="flex items-center gap-2 mb-4">
                      <div class="w-8 h-8 bg-green-500 text-white rounded-full flex items-center justify-center font-bold text-sm">1</div>
                      <h3 class="text-lg font-bold text-gray-900">Basic Information</h3>
                    </div>

                    <div class="space-y-4">
                      <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Template Name</label>
                        <input
                          v-model="templateForm.name"
                          type="text"
                          required
                          class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                          placeholder="e.g., My Weekly Reflection"
                          @keydown.enter.prevent
                        />
                      </div>

                      <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Icon</label>
                        <EmojiPicker v-model="templateForm.icon" />
                      </div>
                    </div>
                  </div>

                  <!-- Step 2: Where & How -->
                  <div class="bg-gray-50 rounded-xl p-6 border-2 border-gray-200">
                    <div class="flex items-center gap-2 mb-4">
                      <div class="w-8 h-8 bg-green-500 text-white rounded-full flex items-center justify-center font-bold text-sm">2</div>
                      <h3 class="text-lg font-bold text-gray-900">Where & How It Works</h3>
                    </div>

                    <div class="space-y-4">
                      <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Category - Where will this appear?</label>
                        <div class="grid grid-cols-3 gap-2">
                          <button
                            type="button"
                            @click="templateForm.category = 'vision'"
                            class="p-3 rounded-lg border-2 transition-all text-center"
                            :class="templateForm.category === 'vision' ? 'border-purple-500 bg-purple-50' : 'border-gray-300 hover:border-gray-400'"
                          >
                            <div class="text-2xl mb-1">🎯</div>
                            <div class="text-xs font-semibold">Vision</div>
                          </button>
                          <button
                            type="button"
                            @click="templateForm.category = 'goal'"
                            class="p-3 rounded-lg border-2 transition-all text-center"
                            :class="templateForm.category === 'goal' ? 'border-green-500 bg-green-50' : 'border-gray-300 hover:border-gray-400'"
                          >
                            <div class="text-2xl mb-1">🎯</div>
                            <div class="text-xs font-semibold">Goals</div>
                          </button>
                          <button
                            type="button"
                            @click="templateForm.category = 'review'"
                            class="p-3 rounded-lg border-2 transition-all text-center"
                            :class="templateForm.category === 'review' ? 'border-blue-500 bg-blue-50' : 'border-gray-300 hover:border-gray-400'"
                          >
                            <div class="text-2xl mb-1">📊</div>
                            <div class="text-xs font-semibold">Reviews</div>
                          </button>
                        </div>
                      </div>

                      <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Behavior - How many times can you fill it?</label>
                        <div class="space-y-2">
                          <button
                            type="button"
                            @click="templateForm.behavior = 'singleton'"
                            class="w-full p-4 rounded-lg border-2 transition-all text-left"
                            :class="templateForm.behavior === 'singleton' ? 'border-green-500 bg-green-50' : 'border-gray-300 hover:border-gray-400'"
                          >
                            <div class="flex items-start gap-3">
                              <div class="text-2xl">📄</div>
                              <div class="flex-1">
                                <div class="font-semibold text-sm">One Document</div>
                                <div class="text-xs text-gray-600 mt-1">Edit anytime, no history. Perfect for ever-changing documents.</div>
                              </div>
                            </div>
                          </button>
                          <button
                            type="button"
                            @click="templateForm.behavior = 'versioned'"
                            class="w-full p-4 rounded-lg border-2 transition-all text-left"
                            :class="templateForm.behavior === 'versioned' ? 'border-green-500 bg-green-50' : 'border-gray-300 hover:border-gray-400'"
                          >
                            <div class="flex items-start gap-3">
                              <div class="text-2xl">📚</div>
                              <div class="flex-1">
                                <div class="font-semibold text-sm">One Document with History</div>
                                <div class="text-xs text-gray-600 mt-1">Keep past versions when you update. Great for evolving vision statements.</div>
                              </div>
                            </div>
                          </button>
                          <button
                            type="button"
                            @click="templateForm.behavior = 'recurring'"
                            class="w-full p-4 rounded-lg border-2 transition-all text-left"
                            :class="templateForm.behavior === 'recurring' ? 'border-green-500 bg-green-50' : 'border-gray-300 hover:border-gray-400'"
                          >
                            <div class="flex items-start gap-3">
                              <div class="text-2xl">📝</div>
                              <div class="flex-1">
                                <div class="font-semibold text-sm">Multiple Entries (Timeline)</div>
                                <div class="text-xs text-gray-600 mt-1">Create unlimited entries over time. Ideal for daily/weekly check-ins.</div>
                              </div>
                            </div>
                          </button>
                        </div>
                      </div>

                      <div v-if="templateForm.behavior === 'recurring'">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Period (Optional)</label>
                        <input
                          v-model="templateForm.period"
                          type="text"
                          class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                          placeholder="e.g., daily, weekly, monthly"
                          @keydown.enter.prevent
                        />
                        <p class="text-xs text-gray-500 mt-1">Helps organize your entries by timeframe</p>
                      </div>
                    </div>
                  </div>

                  <!-- Step 3: Form Fields -->
                  <div class="bg-gray-50 rounded-xl p-6 border-2 border-gray-200">
                    <div class="flex items-center justify-between mb-4">
                      <div class="flex items-center gap-2">
                        <div class="w-8 h-8 bg-green-500 text-white rounded-full flex items-center justify-center font-bold text-sm">3</div>
                        <h3 class="text-lg font-bold text-gray-900">Design Your Questions</h3>
                      </div>
                      <button
                        type="button"
                        @click="addField"
                        class="px-3 py-2 bg-green-500 text-white text-sm font-semibold rounded-lg hover:bg-green-600 flex items-center gap-1"
                      >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Add Question
                      </button>
                    </div>

                    <div class="space-y-3">
                      <div
                        v-for="(field, index) in templateForm.structure"
                        :key="field.id"
                        class="bg-white border-2 border-gray-200 rounded-lg p-4 hover:border-green-300 transition-colors"
                      >
                        <div class="flex items-start gap-3 mb-3">
                          <div class="flex-shrink-0 w-6 h-6 bg-gray-200 text-gray-600 rounded-full flex items-center justify-center text-xs font-bold">
                            {{ index + 1 }}
                          </div>
                          <div class="flex-1 space-y-3">
                            <div>
                              <label class="block text-xs font-semibold text-gray-600 mb-1">Question / Label</label>
                              <input
                                v-model="field.label"
                                type="text"
                                required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                placeholder="e.g., What did you accomplish today?"
                                @keydown.enter.prevent
                              />
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                              <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1">Answer Type</label>
                                <select
                                  v-model="field.type"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-green-500"
                                >
                                  <option value="textarea">📝 Long Text</option>
                                  <option value="text">✏️ Short Text</option>
                                  <option value="select">📋 Multiple Choice</option>
                                  <option value="number">🔢 Number</option>
                                  <option value="checkbox">☑️ Checkbox</option>
                                  <option value="checklist">✅ Checklist</option>
                                </select>
                              </div>

                              <div v-if="field.type === 'textarea'">
                                <label class="block text-xs font-semibold text-gray-600 mb-1">Text Box Height</label>
                                <input
                                  v-model.number="field.rows"
                                  type="number"
                                  min="2"
                                  max="20"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm"
                                  placeholder="4"
                                />
                              </div>

                              <div v-if="field.type !== 'textarea'">
                                <label class="block text-xs font-semibold text-gray-600 mb-1">Placeholder Text</label>
                                <input
                                  v-model="field.placeholder"
                                  type="text"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm"
                                  placeholder="Helper text..."
                                  @keydown.enter.prevent
                                />
                              </div>
                            </div>

                            <div v-if="field.type === 'select'">
                              <label class="block text-xs font-semibold text-gray-600 mb-1">Choices (comma-separated)</label>
                              <input
                                v-model="field.optionsText"
                                @blur="field.options = field.optionsText?.split(',').map(o => o.trim()).filter(o => o)"
                                type="text"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm"
                                placeholder="Yes, No, Maybe"
                                @keydown.enter.prevent
                              />
                            </div>
                          </div>

                          <button
                            type="button"
                            @click="removeField(index)"
                            class="flex-shrink-0 p-1.5 text-red-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-colors"
                            title="Remove field"
                          >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                          </button>
                        </div>
                      </div>

                      <div v-if="templateForm.structure.length === 0" class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center">
                        <div class="text-4xl mb-2">📝</div>
                        <p class="text-gray-600 text-sm font-medium mb-1">No questions yet</p>
                        <p class="text-gray-500 text-xs">Click "Add Question" to start building your form</p>
                      </div>
                    </div>
                  </div>

                  <!-- Submit Buttons -->
                  <div class="space-y-3">
                    <!-- Warning if no fields -->
                    <div v-if="templateForm.structure.length === 0" class="bg-amber-50 border-2 border-amber-200 rounded-lg p-4 flex items-start gap-3">
                      <svg class="w-5 h-5 text-amber-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                      </svg>
                      <div class="flex-1">
                        <p class="text-sm font-semibold text-amber-800">At least one question is required</p>
                        <p class="text-xs text-amber-700 mt-1">Click "Add Question" above to start building your template</p>
                      </div>
                    </div>

                    <div class="flex gap-3">
                      <button
                        type="submit"
                        :disabled="templateForm.structure.length === 0"
                        class="flex-1 px-6 py-3 bg-gradient-to-r from-green-500 to-emerald-600 text-white font-semibold rounded-lg hover:from-green-600 hover:to-emerald-700 transition-all shadow-lg shadow-green-500/30 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:from-green-500 disabled:hover:to-emerald-600"
                      >
                        {{ editingTemplate ? 'Update Template' : 'Create Template' }}
                      </button>
                      <button
                        type="button"
                        @click="showTemplateForm = false"
                        class="px-6 py-3 border-2 border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-all"
                      >
                        Cancel
                      </button>
                    </div>
                  </div>
                </div>

                <!-- RIGHT COLUMN: Live Preview -->
                <div class="space-y-6">
                  <div class="sticky top-6">
                    <div class="bg-gradient-to-br from-gray-100 to-gray-50 rounded-xl p-6 border-2 border-gray-200">
                      <div class="flex items-center gap-2 mb-4">
                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        <h3 class="text-lg font-bold text-gray-900">Live Preview</h3>
                      </div>

                      <!-- Preview Card -->
                      <div class="bg-white rounded-xl border-2 border-gray-300 shadow-lg overflow-hidden">
                        <div class="p-6 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-white">
                          <div class="flex items-center gap-3">
                            <span class="text-3xl">{{ templateForm.icon || '📝' }}</span>
                            <div>
                              <h4 class="font-bold text-gray-900">{{ templateForm.name || 'Your Template Name' }}</h4>
                              <div class="flex items-center gap-2 mt-1">
                                <span class="text-xs px-2 py-0.5 rounded-full"
                                  :class="{
                                    'bg-purple-100 text-purple-700': templateForm.category === 'vision',
                                    'bg-green-100 text-green-700': templateForm.category === 'goal',
                                    'bg-blue-100 text-blue-700': templateForm.category === 'review'
                                  }"
                                >
                                  {{ templateForm.category }}
                                </span>
                                <span class="text-xs text-gray-500">
                                  {{ templateForm.behavior === 'singleton' ? '📄 One Document' : templateForm.behavior === 'versioned' ? '📚 Versioned' : '📝 Timeline' }}
                                </span>
                              </div>
                            </div>
                          </div>
                        </div>

                        <div class="p-6 space-y-5">
                          <div v-if="templateForm.structure.length === 0" class="text-center py-8 text-gray-400">
                            <div class="text-3xl mb-2">👀</div>
                            <p class="text-sm">Add questions to see them here</p>
                          </div>

                          <div v-for="(field, index) in templateForm.structure" :key="field.id" class="space-y-2">
                            <label class="block text-xs font-bold uppercase tracking-wide text-gray-500">
                              {{ field.label || `Question ${index + 1}` }}
                            </label>

                            <textarea
                              v-if="field.type === 'textarea'"
                              :rows="field.rows || 4"
                              :placeholder="field.placeholder"
                              disabled
                              class="w-full px-3 py-2 border-2 border-gray-200 rounded-lg text-sm text-gray-400 bg-gray-50 resize-none"
                            ></textarea>

                            <input
                              v-else-if="field.type === 'text'"
                              type="text"
                              :placeholder="field.placeholder"
                              disabled
                              class="w-full px-3 py-2 border-2 border-gray-200 rounded-lg text-sm text-gray-400 bg-gray-50"
                            />

                            <select
                              v-else-if="field.type === 'select'"
                              disabled
                              class="w-full px-3 py-2 border-2 border-gray-200 rounded-lg text-sm text-gray-400 bg-gray-50"
                            >
                              <option>{{ field.placeholder || 'Select an option...' }}</option>
                              <option v-for="option in field.options" :key="option">{{ option }}</option>
                            </select>

                            <input
                              v-else-if="field.type === 'number'"
                              type="number"
                              :placeholder="field.placeholder"
                              disabled
                              class="w-full px-3 py-2 border-2 border-gray-200 rounded-lg text-sm text-gray-400 bg-gray-50"
                            />

                            <label v-else-if="field.type === 'checkbox'" class="flex items-center gap-2 text-sm text-gray-500">
                              <input type="checkbox" disabled class="w-4 h-4 rounded border-gray-300" />
                              <span>{{ field.placeholder || 'Checkbox option' }}</span>
                            </label>

                            <div v-else-if="field.type === 'checklist'" class="space-y-2">
                              <div class="flex items-center gap-2 text-sm text-gray-500">
                                <input type="checkbox" disabled class="w-4 h-4 rounded border-gray-300 flex-shrink-0" />
                                <input type="text" placeholder="Item 1..." disabled class="flex-1 px-3 py-1.5 border-2 border-gray-200 rounded-lg text-sm text-gray-400 bg-gray-50" />
                              </div>
                              <div class="flex items-center gap-2 text-sm text-gray-500">
                                <input type="checkbox" disabled class="w-4 h-4 rounded border-gray-300 flex-shrink-0" />
                                <input type="text" placeholder="Item 2..." disabled class="flex-1 px-3 py-1.5 border-2 border-gray-200 rounded-lg text-sm text-gray-400 bg-gray-50" />
                              </div>
                              <p class="text-xs text-gray-400 italic">Users can add/remove items dynamically</p>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </form>
          </div>
      </div>
    </Transition>

    <!-- Create/Edit Modal -->
    <Transition name="modal">
      <div v-if="showModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
        <Transition name="modal-content">
          <div v-if="showModal" class="bg-white rounded-2xl shadow-2xl max-w-3xl w-full max-h-[90vh] overflow-y-auto">
            <div class="sticky top-0 bg-white border-b border-gray-200 px-8 py-6 flex justify-between items-center">
              <h2 class="text-2xl font-bold text-black flex items-center gap-3">
                <span class="text-3xl">{{ selectedTemplate.icon }}</span>
                {{ editingCheckIn ? 'Edit' : 'Create' }} {{ selectedTemplate.name }}
              </h2>
              <button @click="closeModal" class="text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
              </button>
            </div>

            <form @submit.prevent="saveCheckIn" class="p-8 space-y-6">
              <!-- Date Field -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  {{ selectedTemplate.name === 'Weekly Review' ? 'Week Ending Date' : 'Date' }}
                </label>
                <input
                  v-model="formData.date"
                  type="date"
                  required
                  class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                />
                <p v-if="selectedTemplate.name === 'Weekly Review'" class="text-xs text-gray-500 mt-1">
                  This will be displayed as a week range (e.g., "Week 5 - Sept. 13-19")
                </p>
              </div>

              <!-- Dynamic Fields -->
              <div v-for="field in selectedTemplate.structure" :key="field.id" class="space-y-2">
                <label class="block text-sm font-medium text-gray-700">
                  {{ field.label }}
                </label>

                <!-- Textarea -->
                <div v-if="field.type === 'textarea'" class="relative">
                  <textarea
                    :ref="el => { if (el) textFieldRefs[field.id] = el }"
                    v-model="formData.content[field.id]"
                    :rows="field.rows || 4"
                    :placeholder="field.placeholder || 'Share your thoughts...'"
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent resize-none"
                  ></textarea>
                  <button
                    type="button"
                    @click="openEmojiPicker($event, field.id)"
                    class="absolute top-2 right-2 p-1.5 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded transition-colors"
                    title="Insert emoji"
                  >
                    😊
                  </button>
                </div>

                <!-- Text -->
                <div v-else-if="field.type === 'text'" class="relative">
                  <input
                    :ref="el => { if (el) textFieldRefs[field.id] = el }"
                    v-model="formData.content[field.id]"
                    type="text"
                    :placeholder="field.placeholder || ''"
                    class="w-full px-4 py-2.5 pr-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                  />
                  <button
                    type="button"
                    @click="openEmojiPicker($event, field.id)"
                    class="absolute top-1/2 -translate-y-1/2 right-2 p-1.5 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded transition-colors"
                    title="Insert emoji"
                  >
                    😊
                  </button>
                </div>

                <!-- Select -->
                <select
                  v-else-if="field.type === 'select'"
                  v-model="formData.content[field.id]"
                  class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                >
                  <option value="">Select...</option>
                  <option v-for="option in field.options" :key="option" :value="option">
                    {{ option }}
                  </option>
                </select>

                <!-- Number -->
                <input
                  v-else-if="field.type === 'number'"
                  v-model.number="formData.content[field.id]"
                  type="number"
                  :min="field.min"
                  :max="field.max"
                  class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                />

                <!-- Checkbox -->
                <label
                  v-else-if="field.type === 'checkbox'"
                  class="flex items-center gap-2 cursor-pointer"
                >
                  <input
                    v-model="formData.content[field.id]"
                    type="checkbox"
                    class="w-5 h-5 text-green-500 rounded focus:ring-2 focus:ring-green-500"
                  />
                  <span class="text-sm text-gray-700">{{ field.placeholder || 'Check if yes' }}</span>
                </label>

                <!-- Checklist -->
                <div v-else-if="field.type === 'checklist'" class="space-y-3">
                  <div
                    v-for="(item, idx) in formData.content[field.id]"
                    :key="idx"
                    class="flex items-center gap-2"
                  >
                    <input
                      v-model="item.checked"
                      type="checkbox"
                      class="w-5 h-5 text-green-500 rounded focus:ring-2 focus:ring-green-500 flex-shrink-0"
                    />
                    <input
                      v-model="item.text"
                      type="text"
                      placeholder="Item..."
                      class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                    />
                    <button
                      type="button"
                      @click="removeChecklistItem(field.id, idx)"
                      class="p-2 text-red-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                    >
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                      </svg>
                    </button>
                  </div>
                  <button
                    type="button"
                    @click="addChecklistItem(field.id)"
                    class="text-sm text-green-600 hover:text-green-700 font-medium flex items-center gap-1"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Add item
                  </button>
                </div>
              </div>

              <!-- Public Toggle -->
              <div class="flex items-center gap-3 pt-4 border-t border-gray-200">
                <input
                  v-model="formData.is_public"
                  type="checkbox"
                  id="is_public"
                  class="w-4 h-4 text-green-600 border-gray-300 rounded focus:ring-green-500"
                />
                <label for="is_public" class="text-sm text-gray-700">
                  Make this public (visible on your profile)
                </label>
              </div>

              <!-- Actions -->
              <div class="flex gap-3 pt-4">
                <button
                  type="submit"
                  :disabled="saving || !hasChanges"
                  class="flex-1 px-6 py-3 bg-gradient-to-r from-green-500 to-emerald-600 text-white font-semibold rounded-lg hover:from-green-600 hover:to-emerald-700 disabled:opacity-50 disabled:cursor-not-allowed transition-all shadow-lg shadow-green-500/30"
                >
                  {{ saving ? 'Saving...' : (editingCheckIn ? 'Save Changes' : 'Save Entry') }}
                </button>
                <button
                  type="button"
                  @click="closeModal"
                  class="px-6 py-3 border-2 border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-all"
                >
                  Cancel
                </button>
              </div>
            </form>
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
import AppNav from '../Components/AppNav.vue';
import { ref, computed, onMounted, nextTick, watch } from 'vue';
import 'emoji-picker-element';

const templates = ref({});
const selectedTemplate = ref(null);
const checkIns = ref([]);
const currentCheckIn = ref(null);
const versions = ref([]);
const showVersionHistory = ref(false);
const showModal = ref(false);
const showInlineForm = ref(false);
const expandedEntries = ref(new Set());
const expandedContextEntries = ref(new Set());
const saving = ref(false);
const editingCheckIn = ref(null);
const dashboardData = ref(null);
const allCheckIns = ref({}); // Store check-ins from other templates for context

// Template management
const showTemplateManager = ref(false);
const allTemplates = ref([]);
const showTemplateForm = ref(false);
const editingTemplate = ref(null);
const templateForm = ref({
  name: '',
  category: 'custom',
  behavior: 'recurring',
  period: '',
  icon: '📝',
  structure: [],
});

// Grouped templates by category
const groupedTemplates = computed(() => {
  const vision = allTemplates.value.filter(t => t.category === 'vision').sort((a, b) => a.order - b.order);
  const goal = allTemplates.value.filter(t => t.category === 'goal').sort((a, b) => a.order - b.order);
  const review = allTemplates.value.filter(t => t.category === 'review').sort((a, b) => a.order - b.order);
  return { vision, goal, review };
});

const formData = ref({
  date: new Date().toISOString().split('T')[0],
  content: {},
  is_public: false,
});

const originalFormData = ref(null); // Store original data when editing

// Deep comparison helper
const deepEqual = (obj1, obj2) => {
  // Same reference or both primitives with same value
  if (obj1 === obj2) return true;

  // Handle null/undefined
  if (obj1 == null || obj2 == null) return obj1 === obj2;

  // Handle primitives
  if (typeof obj1 !== 'object' || typeof obj2 !== 'object') {
    return obj1 === obj2;
  }

  // Handle arrays
  if (Array.isArray(obj1) && Array.isArray(obj2)) {
    if (obj1.length !== obj2.length) return false;
    for (let i = 0; i < obj1.length; i++) {
      if (!deepEqual(obj1[i], obj2[i])) return false;
    }
    return true;
  }

  // One is array, other is not
  if (Array.isArray(obj1) || Array.isArray(obj2)) return false;

  // Handle objects
  const keys1 = Object.keys(obj1).sort();
  const keys2 = Object.keys(obj2).sort();

  if (keys1.length !== keys2.length) return false;

  for (const key of keys1) {
    if (!keys2.includes(key)) return false;
    if (!deepEqual(obj1[key], obj2[key])) return false;
  }

  return true;
};

// Check if form has changes
const hasChanges = computed(() => {
  // When creating new (not editing), always enable save
  if (!editingCheckIn.value) {
    return true;
  }

  // When editing, must have originalFormData to compare
  if (!originalFormData.value) {
    return true; // Safe default - allow save
  }

  // Compare date
  if (formData.value.date !== originalFormData.value.date) {
    return true;
  }

  // Compare is_public
  if (formData.value.is_public !== originalFormData.value.is_public) {
    return true;
  }

  // Compare content (deep comparison)
  const hasContentChanges = !deepEqual(formData.value.content, originalFormData.value.content);

  return hasContentChanges;
});

const visionTemplates = computed(() => templates.value.vision || []);
const goalTemplates = computed(() => templates.value.goal || []);
const reviewTemplates = computed(() => templates.value.review || []);

const randomVisionItem = computed(() => {
  if (!dashboardData.value?.visionGoals) return null;
  const visionItems = dashboardData.value.visionGoals.filter(
    item => item.template?.category === 'vision' && !item.template?.is_hidden
  );
  if (visionItems.length === 0) return null;
  const randomIndex = Math.floor(Math.random() * visionItems.length);
  return visionItems[randomIndex];
});

const currentGoals = computed(() => {
  if (!dashboardData.value?.visionGoals) return [];
  return dashboardData.value.visionGoals.filter(
    item => item.template?.category === 'goal' && !item.template?.is_hidden
  );
});

// Get latest review from each distinct visible template (up to 2)
const latestReviews = computed(() => {
  if (!dashboardData.value?.recentReviews) return [];

  // Filter to only show reviews from visible (non-hidden) templates
  const visibleReviews = dashboardData.value.recentReviews.filter(
    review => review.template && !review.template.is_hidden
  );

  // Group by template_id and get the latest from each
  const seenTemplates = new Set();
  const uniqueLatest = [];

  for (const review of visibleReviews) {
    if (!seenTemplates.has(review.template_id)) {
      seenTemplates.add(review.template_id);
      uniqueLatest.push(review);

      // Only show 2 cards
      if (uniqueLatest.length >= 2) break;
    }
  }

  return uniqueLatest;
});

// For backwards compatibility / specific use cases
const latestDaily = computed(() => {
  return latestReviews.value.find(review => review.template?.period === 'daily') || null;
});

const latestWeekly = computed(() => {
  return latestReviews.value.find(review => review.template?.period === 'weekly') || null;
});

// Smart Quick Action buttons - prefer Daily/Weekly, fallback to other visible review templates
const quickActionButton1 = computed(() => {
  // Try to find Daily first
  const dailyTemplate = reviewTemplates.value.find(t => t.period === 'daily');
  if (dailyTemplate) return dailyTemplate;

  // Fallback to first visible review template
  return reviewTemplates.value[0] || null;
});

const quickActionButton2 = computed(() => {
  // Try to find Weekly first
  const weeklyTemplate = reviewTemplates.value.find(t => t.period === 'weekly');
  if (weeklyTemplate) return weeklyTemplate;

  // Fallback to second visible review template (different from first)
  const secondTemplate = reviewTemplates.value.find(t => t.id !== quickActionButton1.value?.id);
  return secondTemplate || null;
});

const contextEntries = computed(() => {
  if (!selectedTemplate.value || selectedTemplate.value.name !== 'Daily Check-in') return [];

  const contexts = [];

  // Get yesterday's daily check-in
  const yesterday = new Date();
  yesterday.setDate(yesterday.getDate() - 1);
  const yesterdayStr = yesterday.toISOString().split('T')[0];

  const yesterdayEntry = checkIns.value.find(c => c.date === yesterdayStr);
  if (yesterdayEntry) {
    contexts.push({
      id: `context-yesterday-${yesterdayEntry.id}`,
      templateIcon: '✅',
      title: "Yesterday's Daily Check-in",
      subtitle: formatDateShort(yesterdayEntry.date),
      structure: selectedTemplate.value.structure,
      content: yesterdayEntry.content,
    });
  }

  // Get latest weekly review
  const weeklyTemplate = reviewTemplates.value.find(t => t.name === 'Weekly Review');
  if (weeklyTemplate && allCheckIns.value[weeklyTemplate.id]) {
    const latestWeekly = allCheckIns.value[weeklyTemplate.id][0];
    if (latestWeekly) {
      contexts.push({
        id: `context-weekly-${latestWeekly.id}`,
        templateIcon: '📋',
        title: 'Latest Weekly Review',
        subtitle: formatDateShort(latestWeekly.date),
        structure: weeklyTemplate.structure,
        content: latestWeekly.content,
      });
    }
  }

  return contexts;
});

const canCreate = computed(() => {
  if (!selectedTemplate.value) return false;
  if (selectedTemplate.value.behavior === 'recurring') return true;
  if (['singleton', 'versioned'].includes(selectedTemplate.value.behavior)) {
    return true; // Can always create/update
  }
  return false;
});

const fetchTemplates = async () => {
  try {
    const response = await window.axios.get('/api/templates');
    templates.value = response.data;
  } catch (error) {
    console.error('Error fetching templates:', error);
  }
};

const fetchDashboard = async () => {
  try {
    const response = await window.axios.get('/api/check-ins/dashboard');
    dashboardData.value = response.data;
  } catch (error) {
    console.error('Error fetching dashboard:', error);
  }
};

const selectTemplate = async (template) => {
  selectedTemplate.value = template;
  showVersionHistory.value = false;
  await fetchCheckIns();

  // Auto-expand latest entry for recurring templates
  if (template.behavior === 'recurring' && checkIns.value.length > 0) {
    expandedEntries.value.add(checkIns.value[0].id);
    expandedEntries.value = new Set(expandedEntries.value);
  }

  // Load context data for Daily Check-in only
  if (template.name === 'Daily Check-in') {
    await loadContextData();

    // Auto-expand first context entry (yesterday's daily)
    if (contextEntries.value.length > 0) {
      expandedContextEntries.value.clear();
      expandedContextEntries.value.add(contextEntries.value[0].id);
      expandedContextEntries.value = new Set(expandedContextEntries.value);
    }
  }
};

const selectTemplateById = (templateId) => {
  // Find template in all categories
  const allTemplates = [
    ...visionTemplates.value,
    ...goalTemplates.value,
    ...reviewTemplates.value,
  ];
  const template = allTemplates.find(t => t.id === templateId);
  if (template) {
    selectTemplate(template);
  }
};

const getFirstFieldPreview = (checkIn) => {
  if (!checkIn?.content || !checkIn?.template?.structure) return '';
  const firstField = checkIn.template.structure[0];
  if (!firstField) return '';
  const content = checkIn.content[firstField.id];
  return content || 'No content yet';
};

const showOverview = async () => {
  selectedTemplate.value = null;
  await fetchDashboard(); // Refresh dashboard data when returning to overview
};

const toggleContextEntry = (entryId) => {
  if (expandedContextEntries.value.has(entryId)) {
    expandedContextEntries.value.delete(entryId);
  } else {
    expandedContextEntries.value.add(entryId);
  }
  expandedContextEntries.value = new Set(expandedContextEntries.value);
};

const loadContextData = async () => {
  if (!selectedTemplate.value || selectedTemplate.value.name !== 'Daily Check-in') return;

  try {
    // For Daily Check-in, load Weekly Review data
    const weeklyTemplate = reviewTemplates.value.find(t => t.name === 'Weekly Review');
    if (weeklyTemplate) {
      const response = await window.axios.get(`/api/templates/${weeklyTemplate.id}/check-ins`);
      allCheckIns.value[weeklyTemplate.id] = response.data;
    }
  } catch (error) {
    console.error('Error loading context data:', error);
  }
};

const fetchCheckIns = async () => {
  if (!selectedTemplate.value) return;

  try {
    const response = await window.axios.get(`/api/templates/${selectedTemplate.value.id}/check-ins`);

    if (['singleton', 'versioned'].includes(selectedTemplate.value.behavior)) {
      currentCheckIn.value = response.data;
      if (currentCheckIn.value && selectedTemplate.value.behavior === 'versioned') {
        await fetchVersions();
      }
    } else {
      checkIns.value = response.data;
    }
  } catch (error) {
    console.error('Error fetching check-ins:', error);
    checkIns.value = [];
    currentCheckIn.value = null;
  }
};

const fetchVersions = async () => {
  try {
    const response = await window.axios.get(`/api/templates/${selectedTemplate.value.id}/versions`);
    versions.value = response.data;
  } catch (error) {
    console.error('Error fetching versions:', error);
  }
};

const openCreateModal = () => {
  editingCheckIn.value = null;
  const content = {};

  // Initialize checklist fields with empty arrays
  selectedTemplate.value.structure.forEach(field => {
    if (field.type === 'checklist') {
      content[field.id] = [];
    }
  });

  formData.value = {
    date: new Date().toISOString().split('T')[0],
    content,
    is_public: false,
  };

  // Use inline form for recurring templates, modal for others
  if (selectedTemplate.value?.behavior === 'recurring') {
    showInlineForm.value = true;
    expandedEntries.value.clear(); // Collapse all entries
  } else {
    showModal.value = true;
  }
};

const openEditModal = (checkIn) => {
  editingCheckIn.value = checkIn;
  const content = { ...checkIn.content };

  // Ensure checklist fields are arrays
  selectedTemplate.value.structure.forEach(field => {
    if (field.type === 'checklist' && !Array.isArray(content[field.id])) {
      content[field.id] = [];
    }
  });

  // Extract date in YYYY-MM-DD format
  let dateValue = checkIn.date;
  if (checkIn.date instanceof Date) {
    dateValue = checkIn.date.toISOString().split('T')[0];
  } else if (typeof checkIn.date === 'string') {
    // Extract just the date part (handles "2025-11-07", "2025-11-07 00:00:00", or "2025-11-07T00:00:00.000000Z")
    if (checkIn.date.includes('T')) {
      dateValue = checkIn.date.split('T')[0];
    } else if (checkIn.date.includes(' ')) {
      dateValue = checkIn.date.split(' ')[0];
    }
    // else already in correct format
  }

  formData.value = {
    date: dateValue,
    content,
    is_public: checkIn.is_public || false,
  };

  // Store original data for change detection
  originalFormData.value = JSON.parse(JSON.stringify(formData.value));

  showModal.value = true;
};

const closeModal = () => {
  showModal.value = false;
  editingCheckIn.value = null;
  originalFormData.value = null;
  formData.value = {
    date: new Date().toISOString().split('T')[0],
    content: {},
    is_public: false,
  };
};

// Checklist helper functions
const addChecklistItem = (fieldId) => {
  if (!formData.value.content[fieldId]) {
    formData.value.content[fieldId] = [];
  }
  formData.value.content[fieldId].push({ text: '', checked: false });
};

const removeChecklistItem = (fieldId, index) => {
  formData.value.content[fieldId].splice(index, 1);
};

const startEditInline = (checkIn) => {
  editingCheckIn.value = checkIn;
  const content = { ...checkIn.content };

  // Ensure checklist fields are arrays
  selectedTemplate.value.structure.forEach(field => {
    if (field.type === 'checklist' && !Array.isArray(content[field.id])) {
      content[field.id] = [];
    }
  });

  // Extract date in YYYY-MM-DD format
  let dateValue = checkIn.date;
  if (checkIn.date instanceof Date) {
    dateValue = checkIn.date.toISOString().split('T')[0];
  } else if (typeof checkIn.date === 'string') {
    // Extract just the date part (handles "2025-11-07", "2025-11-07 00:00:00", or "2025-11-07T00:00:00.000000Z")
    if (checkIn.date.includes('T')) {
      dateValue = checkIn.date.split('T')[0];
    } else if (checkIn.date.includes(' ')) {
      dateValue = checkIn.date.split(' ')[0];
    }
    // else already in correct format
  }

  formData.value = {
    date: dateValue,
    content,
    is_public: checkIn.is_public || false,
  };

  // Store original data for change detection
  originalFormData.value = JSON.parse(JSON.stringify(formData.value));

  showInlineForm.value = true;
  expandedEntries.value.clear();
};

const cancelInlineForm = () => {
  showInlineForm.value = false;
  editingCheckIn.value = null;
  originalFormData.value = null;
  formData.value = {
    date: new Date().toISOString().split('T')[0],
    content: {},
    is_public: false,
  };
};

const toggleEntry = (entryId) => {
  if (expandedEntries.value.has(entryId)) {
    expandedEntries.value.delete(entryId);
  } else {
    expandedEntries.value.add(entryId);
  }
  // Trigger reactivity
  expandedEntries.value = new Set(expandedEntries.value);
};

const saveCheckIn = async () => {
  saving.value = true;
  try {
    if (editingCheckIn.value) {
      // Update existing
      await window.axios.put(`/api/check-ins/${editingCheckIn.value.id}`, formData.value);
    } else {
      // Create new
      await window.axios.post(`/api/templates/${selectedTemplate.value.id}/check-ins`, formData.value);
    }

    await fetchCheckIns();
    await fetchDashboard(); // Refresh dashboard to show latest entries
    closeModal();
  } catch (error) {
    console.error('Error saving check-in:', error);
    alert('Error saving. Please try again.');
  } finally {
    saving.value = false;
  }
};

const saveInlineCheckIn = async () => {
  saving.value = true;
  try {
    if (editingCheckIn.value) {
      // Update existing
      await window.axios.put(`/api/check-ins/${editingCheckIn.value.id}`, formData.value);
    } else {
      // Create new
      await window.axios.post(`/api/templates/${selectedTemplate.value.id}/check-ins`, formData.value);
    }

    await fetchCheckIns();
    await fetchDashboard(); // Refresh dashboard to show latest entries
    await loadContextData(); // Refresh context in case it changed
    cancelInlineForm();

    // Auto-expand the newly created/edited entry (latest entry)
    if (checkIns.value.length > 0) {
      expandedEntries.value.clear(); // Clear others
      expandedEntries.value.add(checkIns.value[0].id);
      expandedEntries.value = new Set(expandedEntries.value); // Trigger reactivity
    }
  } catch (error) {
    console.error('Error saving check-in:', error);
    alert('Error saving. Please try again.');
  } finally {
    saving.value = false;
  }
};

const deleteCheckIn = async (id) => {
  if (!confirm('Are you sure you want to delete this entry?')) return;

  try {
    await window.axios.delete(`/api/check-ins/${id}`);
    await fetchCheckIns();
  } catch (error) {
    console.error('Error deleting check-in:', error);
  }
};

const getBehaviorDescription = (behavior) => {
  const descriptions = {
    singleton: 'Single living document',
    versioned: 'Living document with version history',
    recurring: 'Multiple dated entries',
  };
  return descriptions[behavior] || '';
};

const getActionButtonText = () => {
  if (!selectedTemplate.value) return '';

  if (selectedTemplate.value.behavior === 'recurring') {
    return '+ New Entry';
  }

  if (currentCheckIn.value) {
    return selectedTemplate.value.behavior === 'versioned' ? 'New Version' : 'Edit';
  }

  return 'Create';
};

const formatDate = (dateString) => {
  if (!dateString) return '';
  const date = new Date(dateString);
  return date.toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
  });
};

const formatDateShort = (dateString) => {
  if (!dateString) return '';
  const date = new Date(dateString);
  return date.toLocaleDateString('en-US', {
    month: 'short',
    day: 'numeric',
    year: 'numeric',
  });
};

const formatRelativeDate = (dateString) => {
  if (!dateString) return '';
  const date = new Date(dateString);
  const today = new Date();
  const yesterday = new Date(today);
  yesterday.setDate(yesterday.getDate() - 1);

  const dateStr = date.toDateString();
  const todayStr = today.toDateString();
  const yesterdayStr = yesterday.toDateString();

  if (dateStr === todayStr) return 'today';
  if (dateStr === yesterdayStr) return 'yesterday';

  const diffTime = today - date;
  const diffDays = Math.floor(diffTime / (1000 * 60 * 60 * 24));

  if (diffDays < 0) return 'in the future'; // Handle future dates
  if (diffDays < 7) return `${diffDays} days ago`;
  if (diffDays < 30) {
    const weeks = Math.floor(diffDays / 7);
    return `${weeks} week${weeks > 1 ? 's' : ''} ago`;
  }
  if (diffDays < 365) {
    const months = Math.floor(diffDays / 30);
    return `${months} month${months > 1 ? 's' : ''} ago`;
  }

  const years = Math.floor(diffDays / 365);
  return `${years} year${years > 1 ? 's' : ''} ago`;
};

const getWeekNumber = (date) => {
  const d = new Date(date);
  d.setHours(0, 0, 0, 0);
  d.setDate(d.getDate() + 4 - (d.getDay() || 7));
  const yearStart = new Date(d.getFullYear(), 0, 1);
  const weekNum = Math.ceil((((d - yearStart) / 86400000) + 1) / 7);
  return weekNum;
};

const getWeekRange = (dateString) => {
  const date = new Date(dateString);
  // Get the week ending date (assuming the date is the end of the week)
  const endDate = new Date(date);

  // Calculate start of week (6 days before end date)
  const startDate = new Date(endDate);
  startDate.setDate(startDate.getDate() - 6);

  return { startDate, endDate };
};

const formatWeekRange = (dateString) => {
  if (!dateString) return '';
  const { startDate, endDate } = getWeekRange(dateString);
  const weekNum = getWeekNumber(endDate);

  const startMonth = startDate.toLocaleDateString('en-US', { month: 'short' });
  const startDay = startDate.getDate();
  const endMonth = endDate.toLocaleDateString('en-US', { month: 'short' });
  const endDay = endDate.getDate();

  // If same month, show "Week 5 - Sept. 13-19"
  if (startMonth === endMonth) {
    return `Week ${weekNum} - ${startMonth}. ${startDay}-${endDay}`;
  }
  // If different months, show "Week 5 - Sept. 28 - Oct. 4"
  return `Week ${weekNum} - ${startMonth}. ${startDay} - ${endMonth}. ${endDay}`;
};

const openTemplateManager = async () => {
  showTemplateManager.value = true;
  await fetchAllTemplates();
};

const closeTemplateManager = () => {
  showTemplateManager.value = false;
  showTemplateForm.value = false;
  editingTemplate.value = null;
};

const fetchAllTemplates = async () => {
  try {
    const response = await window.axios.get('/api/templates-manage');
    allTemplates.value = response.data;
  } catch (error) {
    console.error('Error fetching all templates:', error);
  }
};

const toggleTemplateVisibility = async (template) => {
  try {
    await window.axios.patch(`/api/templates-manage/${template.id}/toggle-visibility`);
    await fetchAllTemplates();
    await fetchTemplates(); // Refresh sidebar
  } catch (error) {
    console.error('Error toggling visibility:', error);
  }
};

const openNewTemplateForm = () => {
  editingTemplate.value = null;
  templateForm.value = {
    name: '',
    category: 'review',
    behavior: 'recurring',
    period: '',
    icon: '📝',
    structure: [],
  };
  showTemplateForm.value = true;
};

const openEditTemplateForm = (template) => {
  editingTemplate.value = template;
  const structureCopy = JSON.parse(JSON.stringify(template.structure));

  // Add optionsText for select fields so they display properly in the form
  structureCopy.forEach(field => {
    if (field.type === 'select' && field.options) {
      field.optionsText = field.options.join(', ');
    }
  });

  templateForm.value = {
    name: template.name,
    category: template.category,
    behavior: template.behavior,
    period: template.period || '',
    icon: template.icon,
    structure: structureCopy,
  };
  showTemplateForm.value = true;
};

const duplicateTemplate = (template) => {
  editingTemplate.value = null; // Not editing, creating new
  const structureCopy = JSON.parse(JSON.stringify(template.structure));

  // Add optionsText for select fields so they display properly in the form
  structureCopy.forEach(field => {
    if (field.type === 'select' && field.options) {
      field.optionsText = field.options.join(', ');
    }
  });

  templateForm.value = {
    name: `${template.name} (Copy)`,
    category: template.category,
    behavior: template.behavior,
    period: template.period || '',
    icon: template.icon,
    structure: structureCopy,
  };
  showTemplateForm.value = true;
};

const saveTemplate = async () => {
  // Validate that at least one field exists
  if (templateForm.value.structure.length === 0) {
    alert('⚠️ Please add at least one question to your template.\n\nClick the "Add Question" button to get started.');
    return;
  }

  // Validate that all fields have labels
  const invalidFields = templateForm.value.structure.filter(field => !field.label || !field.label.trim());
  if (invalidFields.length > 0) {
    alert('⚠️ Please fill in all question labels before saving.');
    return;
  }

  // Validate select fields have options
  const selectFieldsWithoutOptions = templateForm.value.structure.filter(
    field => field.type === 'select' && (!field.options || field.options.length === 0)
  );
  if (selectFieldsWithoutOptions.length > 0) {
    alert('⚠️ Dropdown fields must have at least one option.\n\nPlease add choices for your dropdown questions.');
    return;
  }

  try {
    if (editingTemplate.value) {
      await window.axios.put(`/api/templates-manage/${editingTemplate.value.id}`, templateForm.value);
    } else {
      await window.axios.post('/api/templates-manage', templateForm.value);
    }
    await fetchAllTemplates();
    await fetchTemplates(); // Refresh sidebar
    showTemplateForm.value = false;
    editingTemplate.value = null;
  } catch (error) {
    console.error('Error saving template:', error);

    // Show specific validation errors if available
    if (error.response?.data?.errors) {
      const errorMessages = Object.values(error.response.data.errors).flat().join('\n');
      alert('❌ Validation Error:\n\n' + errorMessages);
    } else if (error.response?.data?.message) {
      alert('❌ Error: ' + error.response.data.message);
    } else {
      alert('❌ Error saving template. Please try again.');
    }
  }
};

const deleteTemplate = async (template) => {
  if (!confirm(`Are you sure you want to delete "${template.name}"?`)) return;

  try {
    await window.axios.delete(`/api/templates-manage/${template.id}`);
    await fetchAllTemplates();
    await fetchTemplates(); // Refresh sidebar
  } catch (error) {
    console.error('Error deleting template:', error);
  }
};

const moveTemplateUp = async (category, index) => {
  if (index === 0) return;

  const templates = [...groupedTemplates.value[category]];
  const template = templates[index];
  const prevTemplate = templates[index - 1];

  // Swap order values
  const tempOrder = template.order;
  template.order = prevTemplate.order;
  prevTemplate.order = tempOrder;

  try {
    // Update both templates
    await window.axios.patch(`/api/templates-manage/${template.id}/reorder`, { order: template.order });
    await window.axios.patch(`/api/templates-manage/${prevTemplate.id}/reorder`, { order: prevTemplate.order });
    await fetchAllTemplates();
    await fetchTemplates(); // Refresh sidebar
  } catch (error) {
    console.error('Error reordering templates:', error);
  }
};

const moveTemplateDown = async (category, index) => {
  const templates = groupedTemplates.value[category];
  if (index === templates.length - 1) return;

  const templatesCopy = [...templates];
  const template = templatesCopy[index];
  const nextTemplate = templatesCopy[index + 1];

  // Swap order values
  const tempOrder = template.order;
  template.order = nextTemplate.order;
  nextTemplate.order = tempOrder;

  try {
    // Update both templates
    await window.axios.patch(`/api/templates-manage/${template.id}/reorder`, { order: template.order });
    await window.axios.patch(`/api/templates-manage/${nextTemplate.id}/reorder`, { order: nextTemplate.order });
    await fetchAllTemplates();
    await fetchTemplates(); // Refresh sidebar
  } catch (error) {
    console.error('Error reordering templates:', error);
  }
};

const addField = () => {
  templateForm.value.structure.push({
    id: `field_${Date.now()}`,
    label: '',
    type: 'textarea',
    rows: 4,
    placeholder: '',
  });
};

const removeField = (index) => {
  templateForm.value.structure.splice(index, 1);
};

// Emoji Support
const showEmojiPicker = ref(false);
const emojiPickerFieldId = ref(null);
const emojiPickerPosition = ref({ top: 0, left: 0 });
const textFieldRefs = ref({});
const emojiPickerRef = ref(null);
let pickerInstance = null;

const openEmojiPicker = (event, fieldId) => {
  const button = event.currentTarget;
  const rect = button.getBoundingClientRect();

  emojiPickerFieldId.value = fieldId;

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
  const fieldId = emojiPickerFieldId.value;
  if (!fieldId) return;

  const emoji = emojiData.unicode || emojiData.emoji || emojiData;
  const input = textFieldRefs.value[fieldId];

  if (input) {
    const start = input.selectionStart || 0;
    const end = input.selectionEnd || 0;
    const text = formData.value.content[fieldId] || '';

    // Insert emoji at cursor position
    formData.value.content[fieldId] = text.substring(0, start) + emoji + text.substring(end);

    // Set cursor after emoji and focus
    nextTick(() => {
      input.focus();
      const newPos = start + emoji.length;
      input.setSelectionRange(newPos, newPos);
    });
  }

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

onMounted(() => {
  fetchTemplates();
  fetchDashboard();
});
</script>

<style scoped>
/* Card Enter Animations */
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

.checkin-card-enter {
  animation: fadeSlideUp 0.5s ease forwards;
}

/* Stagger animation for multiple cards */
.checkin-card-enter:nth-child(1) { animation-delay: 0s; }
.checkin-card-enter:nth-child(2) { animation-delay: 0.05s; }
.checkin-card-enter:nth-child(3) { animation-delay: 0.1s; }
.checkin-card-enter:nth-child(4) { animation-delay: 0.15s; }
.checkin-card-enter:nth-child(5) { animation-delay: 0.2s; }

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
  transition: all 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55);
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

/* Button Interactions */
@keyframes buttonPress {
  0% { transform: scale(1); }
  50% { transform: scale(0.95); }
  100% { transform: scale(1); }
}

.btn-press {
  animation: buttonPress 0.2s ease;
}

/* Save Success Animation */
@keyframes saveSuccess {
  0% { transform: scale(1); }
  50% { transform: scale(1.05); box-shadow: 0 0 0 4px rgba(34, 197, 94, 0.2); }
  100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(34, 197, 94, 0); }
}

.save-success {
  animation: saveSuccess 0.6s ease;
}

/* Delete Confirmation */
@keyframes shake {
  0%, 100% { transform: translateX(0); }
  25% { transform: translateX(-5px); }
  75% { transform: translateX(5px); }
}

.shake-animate {
  animation: shake 0.3s ease;
}

/* Field Focus Animation */
@keyframes fieldFocus {
  from {
    box-shadow: 0 0 0 0 rgba(59, 130, 246, 0.5);
  }
  to {
    box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
  }
}

/* Sidebar Hover Effects */
.sidebar-item {
  transition: all 0.2s ease;
}

.sidebar-item:hover {
  transform: translateX(2px);
}

/* Content Fade Transitions */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease, transform 0.3s ease;
}

.fade-enter-from {
  opacity: 0;
  transform: translateY(10px);
}

.fade-leave-to {
  opacity: 0;
  transform: translateY(-10px);
}

/* Badge/Tag Animations */
@keyframes badgePop {
  0% { transform: scale(0.8); opacity: 0; }
  60% { transform: scale(1.1); }
  100% { transform: scale(1); opacity: 1; }
}

.badge-pop {
  animation: badgePop 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
}

/* Loading Skeleton Animation */
@keyframes shimmer {
  0% { background-position: -1000px 0; }
  100% { background-position: 1000px 0; }
}

.skeleton-loader {
  background: linear-gradient(
    90deg,
    #f0f0f0 0%,
    #e0e0e0 20%,
    #f0f0f0 40%,
    #f0f0f0 100%
  );
  background-size: 1000px 100%;
  animation: shimmer 2s infinite linear;
}

/* Hover Scale Effects */
.hover-scale {
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.hover-scale:hover {
  transform: translateY(-2px) scale(1.02);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

/* Icon Animations */
@keyframes iconBounce {
  0%, 100% { transform: translateY(0); }
  50% { transform: translateY(-4px); }
}

.icon-bounce {
  animation: iconBounce 0.5s ease;
}

/* Progress Bar Animation */
@keyframes progressFill {
  from { width: 0%; }
}

.progress-animate {
  animation: progressFill 1s ease-out forwards;
}

/* Quick Action Button Pulse */
@keyframes quickActionPulse {
  0%, 100% { box-shadow: 0 0 0 0 rgba(59, 130, 246, 0.4); }
  50% { box-shadow: 0 0 0 8px rgba(59, 130, 246, 0); }
}

.quick-action-pulse {
  animation: quickActionPulse 2s infinite;
}

/* Version Badge Animation */
@keyframes versionSlide {
  from {
    opacity: 0;
    transform: translateX(-10px);
  }
  to {
    opacity: 1;
    transform: translateX(0);
  }
}

.version-slide {
  animation: versionSlide 0.3s ease;
}

/* Template Icon Rotate on Hover */
.template-icon {
  transition: transform 0.3s ease;
  display: inline-block;
}

.template-icon:hover {
  transform: rotate(10deg) scale(1.1);
}

/* Smooth Transitions for All Interactive Elements */
button, a, input, textarea, select {
  transition: all 0.2s ease;
}

/* Card Hover Effect */
.card-hover {
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.card-hover:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
}

/* Success Checkmark Animation */
@keyframes checkmarkDraw {
  0% {
    stroke-dashoffset: 100;
  }
  100% {
    stroke-dashoffset: 0;
  }
}

.checkmark-animate {
  stroke-dasharray: 100;
  animation: checkmarkDraw 0.6s ease forwards;
}

/* Emoji Picker Slide Up */
@keyframes emojiSlideUp {
  from {
    opacity: 0;
    transform: translateY(20px) scale(0.9);
  }
  to {
    opacity: 1;
    transform: translateY(0) scale(1);
  }
}

.emoji-picker-enter {
  animation: emojiSlideUp 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55);
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

/* Expand/Collapse Animation */
.expand-enter-active,
.expand-leave-active {
  transition: all 0.3s ease;
  overflow: hidden;
}

.expand-enter-from,
.expand-leave-to {
  max-height: 0;
  opacity: 0;
}

.expand-enter-to,
.expand-leave-from {
  max-height: 2000px;
  opacity: 1;
}

/* Line Clamp */
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
