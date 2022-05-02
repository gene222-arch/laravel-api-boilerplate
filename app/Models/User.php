<?php

namespace App\Models;

use App\Models\Profile;
use Illuminate\Support\Str;
use Laravel\Passport\HasApiTokens;
use App\Jobs\QueueEmailVerification;
use Illuminate\Notifications\Notifiable;
use App\Jobs\QueuePasswordResetNotification;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'uuid',
        'first_name',
        'last_name',
        'email',
        'password',
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
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($user) {
            $user->uuid = Str::orderedUuid();
        });
    }

    public function getUuidKey()
    {
        return $this->uuid;
    }

    public function name(): Attribute
    {
        return Attribute::make(
            get: fn () => "{$this->first_name} {$this->last_name}"
        );
    }

    public function profile(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Profile::class);
    }

    public function sendEmailVerificationNotification(): void
    {
        QueueEmailVerification::dispatch($this);
    }

    public function sendPasswordResetNotification($token): void
    {
        QueuePasswordResetNotification::dispatch($this, $token);
    }
}
