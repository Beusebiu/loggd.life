<template>
  <div class="relative">
    <textarea
      ref="textareaRef"
      :value="modelValue"
      @input="handleInput"
      @blur="$emit('blur')"
      v-bind="$attrs"
      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 pr-12"
      :class="$attrs.class"
    ></textarea>

    <!-- Emoji Button (positioned absolute in top-right corner) -->
    <button
      type="button"
      @click="toggleEmojiPicker"
      class="absolute top-2 right-2 w-8 h-8 text-xl hover:bg-gray-100 rounded-lg transition-colors flex items-center justify-center z-10"
      title="Add emoji"
    >
      😀
    </button>

    <!-- Emoji Picker Popover -->
    <div
      v-if="showEmojiPicker"
      ref="emojiPickerRef"
      class="absolute z-50 right-0"
      :class="pickerPosition"
      @click.stop
    >
    </div>

    <!-- Backdrop -->
    <div
      v-if="showEmojiPicker"
      class="fixed inset-0 z-40"
      @click="showEmojiPicker = false"
    ></div>
  </div>
</template>

<script setup>
import { ref, watch, nextTick } from 'vue';
import 'emoji-picker-element';

defineOptions({
  inheritAttrs: false
});

const props = defineProps({
  modelValue: {
    type: String,
    default: '',
  },
});

const emit = defineEmits(['update:modelValue', 'blur']);

const textareaRef = ref(null);
const showEmojiPicker = ref(false);
const emojiPickerRef = ref(null);
const pickerPosition = ref('mt-2'); // default: below
let pickerInstance = null;
let cursorPosition = 0;

const handleInput = (event) => {
  emit('update:modelValue', event.target.value);
};

const toggleEmojiPicker = () => {
  if (showEmojiPicker.value) {
    showEmojiPicker.value = false;
    return;
  }

  // Save cursor position before opening picker
  if (textareaRef.value) {
    cursorPosition = textareaRef.value.selectionStart;

    // Calculate if picker should show above or below
    const rect = textareaRef.value.getBoundingClientRect();
    const pickerHeight = 435; // emoji-picker-element approximate height
    const spaceBelow = window.innerHeight - rect.bottom;
    const spaceAbove = rect.top;

    // Show above if not enough space below and there's more space above
    if (spaceBelow < pickerHeight && spaceAbove > spaceBelow) {
      pickerPosition.value = 'bottom-full mb-2';
    } else {
      pickerPosition.value = 'mt-2';
    }
  }

  showEmojiPicker.value = true;
};

const insertEmoji = (emojiData) => {
  const emoji = emojiData.unicode || emojiData.emoji || emojiData;
  const currentValue = props.modelValue || '';

  // Insert emoji at cursor position
  const before = currentValue.substring(0, cursorPosition);
  const after = currentValue.substring(cursorPosition);
  const newValue = before + emoji + after;

  emit('update:modelValue', newValue);
  showEmojiPicker.value = false;

  // Restore focus to textarea and position cursor after emoji
  nextTick(() => {
    if (textareaRef.value) {
      textareaRef.value.focus();
      const newCursorPos = cursorPosition + emoji.length;
      textareaRef.value.setSelectionRange(newCursorPos, newCursorPos);
      cursorPosition = newCursorPos;
    }
  });
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
</script>
