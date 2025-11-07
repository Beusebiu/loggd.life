<template>
  <div class="flex items-center justify-center" :class="containerClass">
    <div
      class="animate-spin rounded-full border-b-2"
      :class="[sizeClass, colorClass]"
    ></div>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  size: {
    type: String,
    default: 'md',
    validator: (value) => ['sm', 'md', 'lg', 'xl'].includes(value),
  },
  color: {
    type: String,
    default: 'black',
    validator: (value) => ['black', 'white', 'blue', 'gray'].includes(value),
  },
  centered: {
    type: Boolean,
    default: true,
  },
});

const sizeClass = computed(() => {
  const sizes = {
    sm: 'w-4 h-4',
    md: 'w-8 h-8',
    lg: 'w-12 h-12',
    xl: 'w-16 h-16',
  };
  return sizes[props.size];
});

const colorClass = computed(() => {
  const colors = {
    black: 'border-black',
    white: 'border-white',
    blue: 'border-blue-500',
    gray: 'border-gray-500',
  };
  return colors[props.color];
});

const containerClass = computed(() => {
  return props.centered ? 'py-12' : '';
});
</script>
