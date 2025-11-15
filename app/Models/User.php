<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
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
        'weekly_points',
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
