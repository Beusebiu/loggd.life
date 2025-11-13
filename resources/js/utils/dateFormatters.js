/**
 * Centralized date formatting utilities
 */

/**
 * Format date as "Nov 12" or "Dec 25"
 */
export const formatDateShort = (dateString) => {
  const date = new Date(dateString);
  return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
};

/**
 * Format date relative to today ("Today", "Yesterday", "5 days ago", etc.)
 */
export const formatRelative = (dateString) => {
  const date = new Date(dateString);
  const todayDate = new Date();
  const diffTime = todayDate.getTime() - date.getTime();
  const diffDays = Math.floor(diffTime / (1000 * 60 * 60 * 24));

  if (diffDays === 0) return 'Today';
  if (diffDays === 1) return 'Yesterday';
  if (diffDays < 7) return `${diffDays} days ago`;
  if (diffDays < 30) return `${Math.floor(diffDays / 7)} weeks ago`;
  return `${Math.floor(diffDays / 30)} months ago`;
};

/**
 * Format time horizon ("3-Year", "Quarterly", etc.)
 */
export const formatTimeHorizon = (horizon) => {
  const labels = {
    '3_year': '3-Year',
    'yearly': 'Yearly',
    'quarterly': 'Quarterly',
    'monthly': 'Monthly',
  };
  return labels[horizon] || horizon;
};

/**
 * Format date as "Nov 2025" or "Dec 2024"
 */
export const formatDate = (date) => {
  if (!date) return '';
  return new Date(date).toLocaleDateString('en-US', {
    month: 'short',
    year: 'numeric',
  });
};

/**
 * Format full date for display
 */
export const formatDateFull = (date) => {
  if (!date) return '';
  return new Date(date).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
  });
};
