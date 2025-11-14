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
     * Get the color theme for this tier (Tailwind classes)
     * Progression: Colorless → Vibrant (Gray → Silver → Blue → Gold → Purple → Cosmic)
     */
    public function colorTheme(): array
    {
        return match ($this) {
            // Awakened - Pale & Colorless
            self::Awakened => [
                'primary' => '#d1d5db',
                'light' => '#f3f4f6',
                'medium' => '#e5e7eb',
                'dark' => '#9ca3af',
                'border' => 'border-gray-300',
                'bg' => 'bg-white',
            ],
            // Committed - Light Gray
            self::Committed => [
                'primary' => '#9ca3af',
                'light' => '#e5e7eb',
                'medium' => '#d1d5db',
                'dark' => '#6b7280',
                'border' => 'border-gray-400',
                'bg' => 'bg-gradient-to-br from-white to-gray-50',
            ],
            // Devoted - Silver
            self::Devoted => [
                'primary' => '#71717a',
                'light' => '#d4d4d8',
                'medium' => '#a1a1aa',
                'dark' => '#52525b',
                'border' => 'border-zinc-400',
                'bg' => 'bg-gradient-to-br from-gray-50 to-zinc-100',
            ],
            // Relentless - Steel Blue-Gray
            self::Relentless => [
                'primary' => '#64748b',
                'light' => '#cbd5e1',
                'medium' => '#94a3b8',
                'dark' => '#475569',
                'border' => 'border-slate-400',
                'bg' => 'bg-gradient-to-br from-slate-50 to-slate-100',
            ],
            // Unwavering - Steel Blue
            self::Unwavering => [
                'primary' => '#0ea5e9',
                'light' => '#bae6fd',
                'medium' => '#7dd3fc',
                'dark' => '#0284c7',
                'border' => 'border-sky-400',
                'bg' => 'bg-gradient-to-br from-sky-50 to-blue-100',
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
