<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     * Note: Gamification fields are fillable for system updates but should NOT
     * be accepted from user input. Controllers must validate requests properly.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'username',
        'password',
        'bio',
        'profile_public',
        'dark_mode',
        'timezone',
        'notification_style',
        // Gamification fields (only updated by system, not user input)
        'total_points',
        'weekly_points',
        'current_level',
        'current_streak',
        'longest_streak',
        'last_activity_date',
        'last_activity_at',
        'comeback_multiplier_active',
        'comeback_multiplier_expires_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'profile_public' => 'boolean',
            'dark_mode' => 'boolean',
            'comeback_multiplier_active' => 'boolean',
            'last_activity_at' => 'datetime',
            'comeback_multiplier_expires_at' => 'datetime',
        ];
    }

    /**
     * Get the user's habits.
     */
    public function habits()
    {
        return $this->hasMany(Habit::class);
    }

    /**
     * Get the user's entries.
     */
    public function entries()
    {
        return $this->hasMany(Entry::class);
    }

    /**
     * Get the user's categories.
     */
    public function categories()
    {
        return $this->hasMany(Category::class);
    }

    /**
     * Get the user's check-ins.
     */
    public function checkIns()
    {
        return $this->hasMany(CheckIn::class);
    }

    /**
     * Get the user's custom templates.
     */
    public function templates()
    {
        return $this->hasMany(Template::class);
    }

    /**
     * Get the user's habit checks.
     */
    public function habitChecks()
    {
        return $this->hasMany(HabitCheck::class);
    }

    /**
     * Get the user's goals.
     */
    public function goals()
    {
        return $this->hasMany(Goal::class);
    }

    /**
     * Get the user's daily check-ins.
     */
    public function dailyCheckIns()
    {
        return $this->hasMany(DailyCheckIn::class);
    }

    /**
     * Get the user's activity logs.
     */
    public function activityLogs()
    {
        return $this->hasMany(UserActivityLog::class);
    }
}
