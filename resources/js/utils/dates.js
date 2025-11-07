/**
 * Date Utility Functions
 * Shared date formatting utilities used across the application
 */

/**
 * Format a date relative to now (e.g., "3d ago", "2w ago", "1y ago")
 */
export function formatStartDate(dateStr) {
  if (!dateStr) return 'recently';

  try {
    // Parse the date string properly (handle various formats)
    let startDate;
    if (typeof dateStr === 'string') {
      // Add time if not present to avoid timezone issues
      if (dateStr.length === 10) {
        startDate = new Date(dateStr + 'T00:00:00');
      } else {
        startDate = new Date(dateStr);
      }
    } else {
      startDate = new Date(dateStr);
    }

    // Check if date is valid
    if (isNaN(startDate.getTime())) {
      return 'recently';
    }

    const now = new Date();
    now.setHours(0, 0, 0, 0);
    startDate.setHours(0, 0, 0, 0);

    // Calculate difference in days
    const diffTime = now.getTime() - startDate.getTime();
    const diffDays = Math.floor(diffTime / (1000 * 60 * 60 * 24));

    if (diffDays < 0) return 'today';
    if (diffDays === 0) return 'today';
    if (diffDays === 1) return 'yesterday';
    if (diffDays < 7) return `${diffDays}d ago`;
    if (diffDays < 30) return `${Math.floor(diffDays / 7)}w ago`;
    if (diffDays < 365) return `${Math.floor(diffDays / 30)}mo ago`;
    return `${Math.floor(diffDays / 365)}y ago`;
  } catch (error) {
    console.error('Error formatting date:', error, dateStr);
    return 'recently';
  }
}

/**
 * Format day name as 3-letter abbreviation (e.g., "Mon", "Tue")
 */
export function formatDayName(date) {
  const days = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
  return days[new Date(date + 'T00:00:00').getDay()];
}

/**
 * Format day name as single letter (e.g., "M", "T")
 */
export function formatDayShort(date) {
  const days = ['S', 'M', 'T', 'W', 'T', 'F', 'S'];
  return days[new Date(date).getDay()];
}

/**
 * Get day number (1-31)
 */
export function formatDayNumber(date) {
  return new Date(date).getDate();
}

/**
 * Format date with month (e.g., "Jan 15" or "15")
 * Shows month only on 1st of month or first day in context
 */
export function formatDateWithMonth(dateStr, showMonthOnFirst = true) {
  const date = new Date(dateStr + 'T00:00:00');
  const day = date.getDate();
  const month = date.toLocaleDateString('en-US', { month: 'short' });

  if (showMonthOnFirst && day === 1) {
    return `${month} ${day}`;
  }

  return day.toString();
}

/**
 * Format date as "Jan 15"
 */
export function formatDateShort(date) {
  return new Date(date).toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
}

/**
 * Format date as "Monday, January 15, 2025"
 */
export function formatDateFull(date) {
  return new Date(date).toLocaleDateString('en-US', {
    weekday: 'long',
    month: 'long',
    day: 'numeric',
    year: 'numeric',
  });
}

/**
 * Format date for tooltips as "Mon, Jan 15, 2025"
 */
export function formatTooltipDate(date) {
  if (!date) return '';
  return new Date(date + 'T00:00:00').toLocaleDateString('en-US', {
    weekday: 'short',
    month: 'short',
    day: 'numeric',
    year: 'numeric',
  });
}

/**
 * Format time from HH:MM:SS to 12-hour format (e.g., "2:30 PM")
 */
export function formatTime(time) {
  if (!time) return '';
  // Time is in HH:MM:SS format, convert to 12-hour format
  const [hours, minutes] = time.split(':');
  const hour = parseInt(hours);
  const ampm = hour >= 12 ? 'PM' : 'AM';
  const hour12 = hour % 12 || 12;
  return `${hour12}:${minutes} ${ampm}`;
}

/**
 * Check if a date string is today
 */
export function isToday(date) {
  return date === new Date().toISOString().split('T')[0];
}

/**
 * Check if a date string is in the future
 */
export function isFutureDate(date) {
  const today = new Date().toISOString().split('T')[0];
  return date > today;
}

/**
 * Get current date as YYYY-MM-DD string
 */
export function getTodayString() {
  return new Date().toISOString().split('T')[0];
}
