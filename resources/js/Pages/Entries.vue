<template>
  <AppLayout>
    <div class="max-w-7xl mx-auto">
      <!-- Page Header -->
      <div class="sticky top-0 z-10 bg-white/80 backdrop-blur-md border-b border-gray-200 px-4 py-3">
        <h2 class="text-xl font-bold text-black">Entries</h2>
      </div>

      <div class="px-4 sm:px-6 lg:px-8 py-8 pb-32">

      <!-- Entries List -->
      <div v-if="entries.length > 0" class="space-y-6">
        <div v-for="entry in entries" :key="entry.id" class="bg-white border border-gray-200 rounded-lg p-6 hover:border-gray-300 transition-colors">
          <div class="flex justify-between items-start mb-3">
            <div class="flex items-center space-x-2">
              <span v-if="entry.category" :style="{ color: entry.category.color }" class="text-sm font-medium">
                <span v-if="entry.category.icon">{{ entry.category.icon }}</span>
                {{ entry.category.name }}
              </span>
              <span class="text-sm text-gray-500">{{ formatDate(entry.entry_date) }}</span>
            </div>
            <button @click="deleteEntry(entry.id)" class="text-gray-400 hover:text-red-500 text-sm">
              Delete
            </button>
          </div>
          <p class="text-black whitespace-pre-wrap">{{ entry.content }}</p>
          <span class="text-xs text-gray-400">{{ formatDate(entry.date) }}</span>
        </div>
      </div>

      <!-- Empty State -->
      <div v-else class="text-center py-12 bg-gray-50 rounded-lg border-2 border-dashed border-gray-300">
        <p class="text-gray-500 mb-2">No entries yet</p>
        <p class="text-sm text-gray-400">Use the quick capture below to create your first entry</p>
      </div>
      </div>

    <!-- Quick Capture - Sticky Bottom -->
    <div class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 shadow-lg">
      <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
        <form @submit.prevent="createEntry" class="space-y-3">
          <div class="flex space-x-2">
            <select
              v-model="newEntry.category_id"
              class="px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
            >
              <option :value="null">No category</option>
              <option v-for="category in categories" :key="category.id" :value="category.id">
                <span v-if="category.icon">{{ category.icon }}</span> {{ category.name }}
              </option>
            </select>
            <input
              type="date"
              v-model="newEntry.date"
              class="px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
            />
          </div>
          <div class="flex space-x-2">
            <textarea
              v-model="newEntry.content"
              @keydown.enter.meta.prevent="createEntry"
              @keydown.enter.ctrl.prevent="createEntry"
              placeholder="What's on your mind? (Cmd/Ctrl + Enter to submit)"
              rows="2"
              class="flex-1 px-3 py-2 border border-gray-300 rounded-md text-black placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 resize-none"
              required
            ></textarea>
            <button
              type="submit"
              :disabled="submitting || !newEntry.content.trim()"
              class="px-6 py-2 bg-black text-white text-sm font-medium rounded-md hover:bg-gray-800 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              {{ submitting ? 'Saving...' : 'Capture' }}
            </button>
          </div>
        </form>
      </div>
    </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { router } from '@inertiajs/vue3';
import AppLayout from '../Layouts/AppLayout.vue';

const entries = ref([]);
const categories = ref([]);
const submitting = ref(false);
const newEntry = ref({
  content: '',
  category_id: null,
  date: new Date().toISOString().split('T')[0]
});

const formatDate = (dateString) => {
  const date = new Date(dateString);
  const today = new Date();
  const yesterday = new Date(today);
  yesterday.setDate(yesterday.getDate() - 1);

  if (date.toDateString() === today.toDateString()) {
    return 'Today';
  } else if (date.toDateString() === yesterday.toDateString()) {
    return 'Yesterday';
  } else {
    return date.toLocaleDateString('en-US', {
      month: 'short',
      day: 'numeric',
      year: date.getFullYear() !== today.getFullYear() ? 'numeric' : undefined
    });
  }
};

const fetchEntries = async () => {
  try {
    const response = await axios.get('/api/entries', {
      params: {
        sort: 'desc',
        limit: 50
      }
    });
    entries.value = response.data;
  } catch (error) {
    console.error('Error fetching entries:', error);
  }
};

const fetchCategories = async () => {
  try {
    const response = await axios.get('/api/categories');
    categories.value = response.data;
  } catch (error) {
    console.error('Error fetching categories:', error);
  }
};

const createEntry = async () => {
  if (!newEntry.value.content.trim()) return;

  submitting.value = true;
  try {
    const response = await axios.post('/api/entries', {
      content: newEntry.value.content,
      category_id: newEntry.value.category_id,
      date: newEntry.value.date
    });

    // Add new entry to the top of the list
    entries.value.unshift(response.data);

    // Reset form
    newEntry.value.content = '';
    newEntry.value.category_id = null;
    newEntry.value.date = new Date().toISOString().split('T')[0];
  } catch (error) {
    console.error('Error creating entry:', error);
    alert('Failed to create entry');
  } finally {
    submitting.value = false;
  }
};

const deleteEntry = async (id) => {
  if (!confirm('Are you sure you want to delete this entry?')) return;

  try {
    await axios.delete(`/api/entries/${id}`);
    entries.value = entries.value.filter(e => e.id !== id);
  } catch (error) {
    console.error('Error deleting entry:', error);
    alert('Failed to delete entry');
  }
};


onMounted(() => {
  fetchEntries();
  fetchCategories();
});
</script>
