<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Notifications\UserCreatedKafkaNotification;
use App\Notifications\WelcomeEmailNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Contracts\JWTSubject;

/**
 * @property int $id
 * @property string $email
 * @property string $username
 * @property string $full_name
 * @property string $password_hash
 * @property bool $is_admin
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'full_name',
        'email',
        'password_hash',
        'is_admin',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password_hash',
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
            'password_hash' => 'hashed',
        ];
    }

    protected static function boot()
    {
        parent::boot();

        // Attach an event listener to the 'created' event
        static::created(function ($user) {
            $user->notify(new WelcomeEmailNotification());
            $user->notify(new UserCreatedKafkaNotification());
        });
    }

    public function getAuthPassword()
    {
        return $this->password_hash;
    }

    // Get the identifier that will be stored in the subject claim of the JWT.
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    // Return a key-value array, containing any custom claims to be added to the JWT.
    public function getJWTCustomClaims()
    {
        return [];
    }

    // Example method to create a password reset token
    public function createPasswordResetToken()
    {
        $token = Str::random(60);
        DB::table('password_resets')->insert([
            'email' => $this->email,
            'token' => $token,
            'created_at' => now(),
        ]);

        return $token;
    }
}
