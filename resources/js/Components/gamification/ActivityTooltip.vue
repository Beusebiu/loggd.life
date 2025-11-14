<template>
  <Teleport to="body">
    <div
      v-if="visible && dayData"
      ref="tooltipRef"
      class="activity-tooltip"
      :style="tooltipStyle"
    >
      <!-- Header with date and total points -->
      <div class="tooltip-header">
        <div class="tooltip-date">{{ formattedDate }}</div>
        <div class="tooltip-total">{{ dayData.points }} points</div>
      </div>

      <!-- Activity breakdown -->
      <div v-if="dayData.activities && dayData.activities.length > 0" class="tooltip-activities">
        <div
          v-for="(activity, index) in dayData.activities"
          :key="index"
          class="tooltip-activity-group"
        >
          <div class="activity-type">
            <span class="activity-count">{{ activity.count }}×</span>
            <span class="activity-label">{{ activity.type }}</span>
          </div>
          <div class="activity-points">+{{ activity.points }} pts</div>
        </div>
      </div>

      <!-- Empty state -->
      <div v-else class="tooltip-empty">
        No activity recorded
      </div>
    </div>
  </Teleport>
</template>

<script setup>
import { ref, computed, watch, nextTick } from 'vue';

const props = defineProps({
  visible: {
    type: Boolean,
    default: false,
  },
  dayData: {
    type: Object,
    default: null,
  },
  mouseEvent: {
    type: Object,
    default: null,
  },
});

const tooltipRef = ref(null);
const tooltipStyle = ref({});

const formattedDate = computed(() => {
  if (!props.dayData?.date) return '';
  const date = new Date(props.dayData.date);
  return date.toLocaleDateString('en-US', {
    weekday: 'short',
    month: 'short',
    day: 'numeric',
    year: 'numeric'
  });
});

// Position tooltip near mouse cursor
watch(() => props.mouseEvent, async (event) => {
  if (!event) return;

  await nextTick();

  const tooltip = tooltipRef.value;
  if (!tooltip) return;

  const tooltipRect = tooltip.getBoundingClientRect();
  const padding = 12;

  let left = event.clientX + padding;
  let top = event.clientY + padding;

  // Adjust if tooltip would go off screen
  if (left + tooltipRect.width > window.innerWidth) {
    left = event.clientX - tooltipRect.width - padding;
  }

  if (top + tooltipRect.height > window.innerHeight) {
    top = event.clientY - tooltipRect.height - padding;
  }

  tooltipStyle.value = {
    left: `${left}px`,
    top: `${top}px`,
  };
}, { immediate: true });
</script>

<style scoped>
.activity-tooltip {
  position: fixed;
  z-index: 9999;
  background: linear-gradient(135deg, rgba(17, 24, 39, 0.98) 0%, rgba(31, 41, 55, 0.98) 100%);
  border: 1px solid rgba(75, 85, 99, 0.5);
  border-radius: 8px;
  padding: 12px;
  min-width: 200px;
  max-width: 300px;
  box-shadow: 0 10px 40px rgba(0, 0, 0, 0.5), 0 0 0 1px rgba(255, 255, 255, 0.1);
  pointer-events: none;
  backdrop-filter: blur(8px);
}

.tooltip-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding-bottom: 10px;
  margin-bottom: 10px;
  border-bottom: 1px solid rgba(75, 85, 99, 0.3);
}

.tooltip-date {
  font-size: 13px;
  font-weight: 600;
  color: #e5e7eb;
}

.tooltip-total {
  font-size: 12px;
  font-weight: 700;
  color: #fbbf24;
  background: rgba(251, 191, 36, 0.15);
  padding: 2px 8px;
  border-radius: 4px;
}

.tooltip-activities {
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.tooltip-activity-group {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 4px 0;
}

.activity-type {
  display: flex;
  align-items: center;
  gap: 6px;
}

.activity-count {
  font-size: 11px;
  font-weight: 600;
  color: #9ca3af;
  background: rgba(156, 163, 175, 0.15);
  padding: 1px 5px;
  border-radius: 3px;
}

.activity-label {
  font-size: 12px;
  color: #d1d5db;
  font-weight: 500;
}

.activity-points {
  font-size: 11px;
  font-weight: 600;
  color: #34d399;
}

.tooltip-activity-detail {
  padding-left: 16px;
  margin-top: 2px;
}

.detail-name {
  font-size: 11px;
  color: #9ca3af;
  font-style: italic;
}

.tooltip-empty {
  font-size: 12px;
  color: #6b7280;
  font-style: italic;
  padding: 4px 0;
}
</style>
