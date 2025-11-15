<?php

namespace App;

enum UserLevel: int
{
    case Awakened = 1;      // 1-2: Eyes opened (2-3 days)
    case Committed = 3;     // 3-5: Decision made (5-7 days)
    case Devoted = 6;       // 6-9: Daily dedication (2 weeks)
    case Relentless = 10;   // 10-14: Never stops (3 weeks)
    case Unwavering = 15;   // 15-20: Steady aim (4 weeks)
    case Formidable = 21;   // 21-27: Impressive power (6 weeks)
    case Indomitable = 28;  // 28-35: Can't be defeated (8 weeks)
    case Invincible = 36;   // 36-45: Can't be conquered (10 weeks)
    case Immortal = 46;     // 46-56: Beyond death (12 weeks)
    case Eternal = 57;      // 57-69: Beyond time (14 weeks)
    case Omnipotent = 70;   // 70-84: All-powerful (16 weeks)
    case Absolute = 85;     // 85-100: Ultimate being (18 weeks)

    /**
     * Get the tier name for this level range
     */
    public function tierName(): string
    {
        return match ($this) {
            self::Awakened => 'Awakened',
            self::Committed => 'Committed',
            self::Devoted => 'Devoted',
            self::Relentless => 'Relentless',
            self::Unwavering => 'Unwavering',
            self::Formidable => 'Formidable',
            self::Indomitable => 'Indomitable',
            self::Invincible => 'Invincible',
            self::Immortal => 'Immortal',
            self::Eternal => 'Eternal',
            self::Omnipotent => 'Omnipotent',
            self::Absolute => 'Absolute',
        };
    }

    /**
     * Get the emoji for this tier
     */
    public function emoji(): string
    {
        return match ($this) {
            self::Awakened => '👁️',
            self::Committed => '🤝',
            self::Devoted => '🙏',
            self::Relentless => '🔄',
            self::Unwavering => '🎯',
            self::Formidable => '💪',
            self::Indomitable => '🛡️',
            self::Invincible => '⚡',
            self::Immortal => '💎',
            self::Eternal => '♾️',
            self::Omnipotent => '👑',
            self::Absolute => '🌌',
        };
    }

    /**
     * Get the complete visual theme for this tier
     * Single source of truth for all tier styling
     */
    public function colorTheme(): array
    {
        return match ($this) {
            // Awakened - Green (GitHub style)
            self::Awakened => [
                // Activity grid
                'primary' => '#22c55e',

                // Card background gradients
                'gradient_light' => 'linear-gradient(135deg, rgba(240, 253, 244, 0.5) 0%, rgba(220, 252, 231, 0.4) 50%, rgba(209, 250, 229, 0.3) 100%)',
                'gradient_dark' => 'linear-gradient(135deg, rgba(5, 46, 22, 0.3) 0%, rgba(20, 83, 45, 0.2) 50%, rgba(22, 101, 52, 0.15) 100%)',

                // Borders
                'border_light' => '1px solid rgba(34, 197, 94, 0.3)',
                'border_dark' => '1px solid rgba(34, 197, 94, 0.3)',

                // Shadows
                'shadow_light' => '0 2px 8px rgba(0, 0, 0, 0.05), 0 0 0 1px rgba(34, 197, 94, 0.1)',
                'shadow_dark' => '0 2px 8px rgba(0, 0, 0, 0.3), 0 0 0 1px rgba(34, 197, 94, 0.2)',

                // Text colors
                'text_primary' => '#22c55e',
                'text_light' => '#16a34a',
                'text_dark' => '#86efac',

                // Avatar
                'avatar_gradient' => 'linear-gradient(135deg, #22c55e 0%, #16a34a 100%)',
                'avatar_shadow' => '0 3px 12px rgba(34, 197, 94, 0.3)',
            ],
            // Committed - Cyan
            self::Committed => [
                'primary' => '#06b6d4',  // cyan-500 for activity grid cells
                'light' => '#cffafe',
                'medium' => '#67e8f9',
                'dark' => '#0891b2',
                'border' => 'border-cyan-400',
                'bg' => 'bg-gradient-to-br from-cyan-50 to-sky-50',
            ],
            // Devoted - Blue
            self::Devoted => [
                'primary' => '#60a5fa',  // blue-400 for bright activity grid cells
                'light' => '#dbeafe',
                'medium' => '#93c5fd',
                'dark' => '#3b82f6',
                'border' => 'border-blue-400',
                'bg' => 'bg-gradient-to-br from-blue-50 to-sky-100',
            ],
            // Relentless - Purple
            self::Relentless => [
                'primary' => '#a855f7',  // purple-500 for activity grid cells
                'light' => '#f3e8ff',
                'medium' => '#d8b4fe',
                'dark' => '#9333ea',
                'border' => 'border-purple-400',
                'bg' => 'bg-gradient-to-br from-purple-50 to-violet-50',
            ],
            // Unwavering - Rose/Pink
            self::Unwavering => [
                'primary' => '#f43f5e',  // rose-500 for activity grid cells
                'light' => '#ffe4e6',
                'medium' => '#fda4af',
                'dark' => '#e11d48',
                'border' => 'border-rose-400',
                'bg' => 'bg-gradient-to-br from-rose-50 to-pink-50',
            ],
            // Formidable - Bronze/Gold
            self::Formidable => [
                'primary' => '#d97706',
                'light' => '#fde68a',
                'medium' => '#fbbf24',
                'dark' => '#b45309',
                'border' => 'border-amber-400',
                'bg' => 'bg-gradient-to-br from-amber-50 to-yellow-100',
            ],
            // Indomitable - Deep Blue
            self::Indomitable => [
                'primary' => '#3b82f6',
                'light' => '#bfdbfe',
                'medium' => '#93c5fd',
                'dark' => '#2563eb',
                'border' => 'border-blue-400',
                'bg' => 'bg-gradient-to-br from-blue-50 to-indigo-100',
            ],
            // Invincible - Electric Purple
            self::Invincible => [
                'primary' => '#8b5cf6',
                'light' => '#ddd6fe',
                'medium' => '#c4b5fd',
                'dark' => '#7c3aed',
                'border' => 'border-violet-400',
                'bg' => 'bg-gradient-to-br from-violet-100 to-purple-100',
            ],
            // Immortal - Diamond/Crystal
            self::Immortal => [
                'primary' => '#06b6d4',
                'light' => '#cffafe',
                'medium' => '#67e8f9',
                'dark' => '#0891b2',
                'border' => 'border-cyan-400',
                'bg' => 'bg-gradient-to-br from-cyan-50 to-sky-100',
            ],
            // Eternal - Cosmic Gold
            self::Eternal => [
                'primary' => '#f59e0b',
                'light' => '#fef3c7',
                'medium' => '#fcd34d',
                'dark' => '#d97706',
                'border' => 'border-yellow-400',
                'bg' => 'bg-gradient-to-br from-yellow-50 via-amber-50 to-orange-50',
            ],
            // Omnipotent - Royal Gold/Purple
            self::Omnipotent => [
                'primary' => '#a855f7',
                'light' => '#f3e8ff',
                'medium' => '#d8b4fe',
                'dark' => '#9333ea',
                'border' => 'border-purple-400',
                'bg' => 'bg-gradient-to-br from-purple-100 via-fuchsia-50 to-amber-50',
            ],
            // Absolute - Prismatic Everything
            self::Absolute => [
                'primary' => '#d946ef',
                'light' => '#fef3c7',
                'medium' => '#fbbf24',
                'dark' => '#a855f7',
                'border' => 'border-fuchsia-400',
                'bg' => 'bg-gradient-to-br from-yellow-50 via-fuchsia-50 to-purple-50',
            ],
        };
    }

    /**
     * Calculate required points for a given level
     * New formula for faster early progression:
     * - Levels 1-20: Exponential growth starting slow (level^2.2 * 15)
     * - Levels 21-50: Moderate growth (level^2.5 * 12)
     * - Levels 51-100: Steeper growth (level^2.8 * 10)
     *
     * Estimated progression at ~35 points/day:
     * - Level 2: ~2-3 days
     * - Level 5: ~1 week
     * - Level 10: ~2-3 weeks
     * - Level 20: ~4-5 weeks
     * - Level 50: ~6-8 months
     * - Level 100: ~2-3 years
     */
    public static function pointsForLevel(int $level): int
    {
        if ($level <= 1) {
            return 0;
        }

        // Fast early progression
        if ($level <= 20) {
            return (int) (pow($level, 2.2) * 15);
        }

        // Moderate mid-game
        if ($level <= 50) {
            return (int) (pow($level, 2.5) * 12);
        }

        // Challenging late-game
        return (int) (pow($level, 2.8) * 10);
    }

    /**
     * Get the tier for a given level number
     */
    public static function getTierForLevel(int $level): self
    {
        return match (true) {
            $level >= 85 => self::Absolute,
            $level >= 70 => self::Omnipotent,
            $level >= 57 => self::Eternal,
            $level >= 46 => self::Immortal,
            $level >= 36 => self::Invincible,
            $level >= 28 => self::Indomitable,
            $level >= 21 => self::Formidable,
            $level >= 15 => self::Unwavering,
            $level >= 10 => self::Relentless,
            $level >= 6 => self::Devoted,
            $level >= 3 => self::Committed,
            default => self::Awakened,
        };
    }

    /**
     * Calculate level from total points
     */
    public static function calculateLevel(int $totalPoints): int
    {
        $level = 1;
        while (self::pointsForLevel($level + 1) <= $totalPoints) {
            $level++;
            if ($level >= 100) {
                break;
            }
        }

        return min($level, 100);
    }
}
