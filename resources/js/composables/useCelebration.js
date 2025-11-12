import { ref } from 'vue';
import { usePage } from '@inertiajs/vue3';
import axios from 'axios';
import { getNotificationMessage } from '../utils/notificationMessages';

// Shared state for the celebration notification
const showCelebration = ref(false);
const celebrationData = ref({
  emoji: '🎉',
  title: 'Congratulations!',
  message: 'You did it!',
});

// Queue for pending achievements
const achievementQueue = ref([]);
const currentAchievementId = ref(null);

export function useCelebration() {
  const page = usePage();

  /**
   * Get the user's notification style preference
   */
  const getNotificationStyle = () => {
    return page.props.auth?.user?.notification_style || 'polite';
  };

  /**
   * Convert backend achievement type to frontend event name
   */
  const mapAchievementTypeToEvent = (achievementType) => {
    // Map backend types to frontend message keys
    const mapping = {
      'habit_completed': 'habitCompleted',
      'first_habit': 'firstHabit',
      'perfect_week': 'perfectWeek',
      'check_in_created': 'habitCompleted', // Use habit completed for now
      'streak_3_days': 'streak3Days',
      'streak_7_days': 'streak7Days',
      'streak_14_days': 'streak14Days',
      'streak_30_days': 'streak30Days',
      'streak_60_days': 'streak60Days',
      'streak_90_days': 'streak90Days',
      'streak_365_days': 'streak365Days',
    };

    return mapping[achievementType] || 'habitCompleted';
  };

  /**
   * Fetch pending achievements from the backend
   */
  const fetchPendingAchievements = async () => {
    try {
      const response = await axios.get('/api/achievements/pending');
      achievementQueue.value = response.data;

      console.log('📊 Fetched pending achievements:', achievementQueue.value.length);
      if (achievementQueue.value.length > 0) {
        console.log('🎯 Achievements in queue:', achievementQueue.value.map(a => ({
          type: a.achievement_type,
          metadata: a.metadata
        })));
      }

      // Show the first one if we have any
      if (achievementQueue.value.length > 0 && !showCelebration.value) {
        showNextAchievement();
      }
    } catch (error) {
      console.error('Error fetching pending achievements:', error);
    }
  };

  /**
   * Show the next achievement in the queue
   */
  const showNextAchievement = () => {
    if (achievementQueue.value.length === 0) {
      console.log('⏸️  No achievements to show');
      return;
    }

    const achievement = achievementQueue.value[0];
    currentAchievementId.value = achievement.id;

    const style = getNotificationStyle();
    const eventType = mapAchievementTypeToEvent(achievement.achievement_type);

    console.log('🎉 Showing achievement:', {
      id: achievement.id,
      type: achievement.achievement_type,
      eventType,
      style,
      metadata: achievement.metadata
    });

    celebrationData.value = getNotificationMessage(eventType, style);
    console.log('💬 Celebration message:', celebrationData.value);
    showCelebration.value = true;
  };

  /**
   * Mark current achievement as shown and show next
   */
  const markCurrentAsShown = async () => {
    if (currentAchievementId.value) {
      try {
        await axios.post(`/api/achievements/${currentAchievementId.value}/mark-shown`);
      } catch (error) {
        console.error('Error marking achievement as shown:', error);
      }
    }

    // Remove from queue
    achievementQueue.value.shift();
    currentAchievementId.value = null;

    // Show next achievement if any
    if (achievementQueue.value.length > 0) {
      setTimeout(() => {
        showNextAchievement();
      }, 500); // Small delay between celebrations
    }
  };

  /**
   * Close the celebration notification
   */
  const closeCelebration = () => {
    showCelebration.value = false;
    markCurrentAsShown();
  };

  /**
   * Check for new achievements (call after actions that might trigger achievements)
   */
  const checkForAchievements = async () => {
    await fetchPendingAchievements();
  };

  return {
    showCelebration,
    celebrationData,
    closeCelebration,
    checkForAchievements,
    fetchPendingAchievements,
  };
}
