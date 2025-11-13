/**
 * General formatting utilities
 */

/**
 * Format metric value with commas and decimals
 */
export const formatMetricValue = (value, unit = '') => {
  if (value === null || value === undefined) return unit ? `${unit}0` : '0';
  const num = parseFloat(value);
  const formatted = num.toLocaleString('en-US', {
    minimumFractionDigits: 0,
    maximumFractionDigits: 2,
  });
  return unit ? `${unit}${formatted}` : formatted;
};

/**
 * Truncate text to specified length with ellipsis
 */
export const truncate = (text, length) => {
  if (!text) return '';
  return text.length > length ? text.substring(0, length) + '...' : text;
};
