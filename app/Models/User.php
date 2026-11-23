<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'user_role',
        'qr_token',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Generate a QR token when a user is created.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            $user->qr_token = Str::random(32); // Generate a random 32-character token
        });
    }

    /**
     * Check if the user is an admin.
     *
     * @return bool
     */
    public function isAdmin()
    {
        return $this->user_role === 'Admin';
    }

    /**
     * Check if the user is a regular user.
     *
     * @return bool
     */
    public function isUser()
    {
        return $this->user_role === 'User';
    }

    /**
     * Check if the user is a guest (neither admin nor user).
     *
     * @return bool
     */
    public function isGuest()
    {
        return !$this->isAdmin() && !$this->isUser();
    }

    /**
     * Check if the user has access to the scanner.
     *
     * @return bool
     */
    public function canAccessScanner()
    {
        return $this->isUser() && $this->hasActiveBorrow();
    }

    /**
     * Check if the user has active borrows.
     *
     * @return bool
     */
    public function hasActiveBorrow()
    {
        return $this->borrows()->where('status', 'active')->exists();
    }

    /**
     * Define the relationship between User and borrows.
     */
    public function borrows()
    {
        return $this->hasMany(Borrow::class);
    }
    
    public function borrowedKeys()
    {
        return $this->hasMany(Borrow::class)->where('type', 'key')->where('status', 'borrowed');
    }

    public function borrowedRemotes()
    {
        return $this->hasMany(Borrow::class)->where('type', 'remote')->where('status', 'borrowed');
    }

}
