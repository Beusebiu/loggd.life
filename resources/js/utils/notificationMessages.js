/**
 * Notification messages for different events and styles
 *
 * Style types:
 * - polite: Professional, encouraging, calm
 * - raw: High-energy, unfiltered, intense motivation
 */

export const notificationMessages = {
  // Habit completed
  habitCompleted: {
    polite: [
      { emoji: '✅', title: 'Well Done!', message: 'You\'ve completed your habit for today.' },
      { emoji: '🎯', title: 'Great Work!', message: 'Another habit checked off. Keep it up!' },
      { emoji: '⭐', title: 'Excellent!', message: 'You\'re making great progress.' },
      { emoji: '👏', title: 'Congratulations!', message: 'You\'ve completed your habit successfully.' },
      { emoji: '💪', title: 'Nice Job!', message: 'You\'re building strong habits.' },
    ],
    raw: [
      { emoji: '🔥', title: 'FUCK YEAH!', message: 'You crushed that habit today!' },
      { emoji: '💪', title: 'BEAST MODE!', message: 'You\'re a fucking machine! Keep going!' },
      { emoji: '⚡', title: 'LEGENDARY!', message: 'Holy shit, you actually did it!' },
      { emoji: '🚀', title: 'UNSTOPPABLE!', message: 'Nothing can stop you now!' },
      { emoji: '👑', title: 'LIKE A BOSS!', message: 'You\'re absolutely killing it!' },
    ],
  },

  // Streak milestones
  streak3Days: {
    polite: [
      { emoji: '🔥', title: '3-Day Streak!', message: 'You\'re building momentum. Great work!' },
      { emoji: '⭐', title: 'Three in a Row!', message: 'You\'re developing a solid habit.' },
    ],
    raw: [
      { emoji: '🔥', title: '3 FUCKING DAYS!', message: 'You\'re on fire! Don\'t stop now!' },
      { emoji: '💣', title: 'THREE STRAIGHT!', message: 'You\'re a goddamn champion!' },
    ],
  },

  streak7Days: {
    polite: [
      { emoji: '🎉', title: '7-Day Streak!', message: 'One week strong! You\'re doing amazing.' },
      { emoji: '🌟', title: 'Week Complete!', message: 'Seven days of consistency. Impressive!' },
    ],
    raw: [
      { emoji: '🔥', title: 'HOLY SHIT!', message: 'Seven fucking days straight! You\'re unstoppable!' },
      { emoji: '💪', title: 'ONE WEEK BEAST!', message: 'Look at you crushing it all week long!' },
    ],
  },

  streak14Days: {
    polite: [
      { emoji: '⭐', title: '2-Week Streak!', message: 'Two weeks of dedication. Fantastic job!' },
      { emoji: '🎯', title: 'Fortnight Champion!', message: 'Fourteen days strong. Keep going!' },
    ],
    raw: [
      { emoji: '🚀', title: 'TWO WEEKS!', message: 'You\'re a fucking legend! Fourteen days!' },
      { emoji: '👑', title: 'ABSOLUTE KING!', message: 'Two weeks straight! Nothing stops you!' },
    ],
  },

  streak30Days: {
    polite: [
      { emoji: '🏆', title: '30-Day Streak!', message: 'One month of excellence. Truly outstanding!' },
      { emoji: '💎', title: 'Month Mastery!', message: 'Thirty days of commitment. You\'re incredible!' },
    ],
    raw: [
      { emoji: '👑', title: 'UNFUCKINGBELIEVABLE!', message: 'THIRTY DAYS! You\'re a goddamn champion!' },
      { emoji: '🔥', title: 'LEGENDARY STATUS!', message: 'One month straight! You\'re absolutely insane!' },
    ],
  },

  streak60Days: {
    polite: [
      { emoji: '🌟', title: '60-Day Streak!', message: 'Two months of dedication. Exceptional work!' },
      { emoji: '💫', title: 'Double Month!', message: 'Sixty days of consistency. Amazing!' },
    ],
    raw: [
      { emoji: '🚀', title: 'HOLY FUCKING SHIT!', message: 'SIXTY DAYS! You\'re unstoppable!' },
      { emoji: '💪', title: 'ABSOLUTE BEAST!', message: 'Two months! You\'re a fucking machine!' },
    ],
  },

  streak90Days: {
    polite: [
      { emoji: '🏅', title: '90-Day Streak!', message: 'Three months of excellence. Extraordinary!' },
      { emoji: '⭐', title: 'Quarter Year!', message: 'Ninety days strong. You\'re inspiring!' },
    ],
    raw: [
      { emoji: '👑', title: 'LEGENDARY!', message: 'THREE FUCKING MONTHS! You\'re a god!' },
      { emoji: '🔥', title: 'UNSTOPPABLE FORCE!', message: 'Ninety days! Nobody can touch you!' },
    ],
  },

  streak365Days: {
    polite: [
      { emoji: '🏆', title: 'One Year!', message: 'An entire year of dedication. You\'re remarkable!' },
      { emoji: '👑', title: 'Year Champion!', message: 'Three hundred sixty-five days. Incredible!' },
    ],
    raw: [
      { emoji: '🔥', title: 'ONE FUCKING YEAR!', message: 'YOU\'RE A LEGEND! 365 DAYS! UNSTOPPABLE!' },
      { emoji: '👑', title: 'ABSOLUTE GOD!', message: 'A WHOLE YEAR! You\'re beyond incredible!' },
    ],
  },

  // Missed habit
  missedHabit: {
    polite: [
      { emoji: '💭', title: 'Don\'t Worry', message: 'Tomorrow is a new opportunity to continue.' },
      { emoji: '🌅', title: 'Keep Going', message: 'One missed day doesn\'t define you. Start fresh tomorrow.' },
      { emoji: '💪', title: 'Stay Strong', message: 'You\'ve got this. Get back on track tomorrow.' },
    ],
    raw: [
      { emoji: '⚡', title: 'GET BACK UP!', message: 'Fuck that! Tomorrow you\'re going to crush it!' },
      { emoji: '🔥', title: 'DON\'T GIVE UP!', message: 'One day means nothing! Get up and destroy tomorrow!' },
      { emoji: '💪', title: 'BOUNCE BACK!', message: 'You\'re better than this! Show the world what you\'re made of!' },
    ],
  },

  // Perfect week (all habits completed all week)
  perfectWeek: {
    polite: [
      { emoji: '🌟', title: 'Perfect Week!', message: 'You\'ve completed all your habits this week. Outstanding!' },
      { emoji: '💯', title: 'Flawless!', message: 'Seven days, every habit done. You\'re amazing!' },
    ],
    raw: [
      { emoji: '🔥', title: 'PERFECT FUCKING WEEK!', message: 'You crushed EVERY habit! You\'re unstoppable!' },
      { emoji: '👑', title: 'ABSOLUTE DOMINATION!', message: 'Not a single habit missed! You\'re a goddamn legend!' },
    ],
  },

  // Milestone: First habit completion
  firstHabit: {
    polite: [
      { emoji: '🌱', title: 'Great Start!', message: 'You\'ve completed your first habit. This is the beginning of something special!' },
      { emoji: '🎯', title: 'First Step!', message: 'Your journey begins now. Well done!' },
    ],
    raw: [
      { emoji: '🚀', title: 'HELL YEAH!', message: 'First habit down! This is just the fucking beginning!' },
      { emoji: '💪', title: 'LET\'S GO!', message: 'You just started your journey to greatness!' },
    ],
  },
};

/**
 * Get a random message for a specific event and style
 *
 * @param {string} event - The event type (e.g., 'habitCompleted', 'streak7Days')
 * @param {string} style - The notification style ('polite' or 'raw')
 * @returns {object} Message object with emoji, title, and message
 */
export function getNotificationMessage(event, style = 'polite') {
  const messages = notificationMessages[event];

  if (!messages || !messages[style]) {
    // Fallback to default message
    return {
      emoji: '✅',
      title: 'Great!',
      message: 'Keep up the good work!',
    };
  }

  const styleMessages = messages[style];
  const randomIndex = Math.floor(Math.random() * styleMessages.length);
  return styleMessages[randomIndex];
}

/**
 * Get streak milestone event name based on streak count
 *
 * @param {number} streak - The current streak count
 * @returns {string|null} Event name or null if no milestone
 */
export function getStreakMilestone(streak) {
  const milestones = {
    3: 'streak3Days',
    7: 'streak7Days',
    14: 'streak14Days',
    30: 'streak30Days',
    60: 'streak60Days',
    90: 'streak90Days',
    365: 'streak365Days',
  };

  return milestones[streak] || null;
}
