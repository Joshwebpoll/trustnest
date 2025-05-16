<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Str;

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
        'surname',
        'name',
        'lastname',
        'email',
        "username",
        'password',
        "otp_number",
        "otp_expires_at",
        "last_login_at",
        "phone_number",
        "address",
        "city",
        "state",
        "country",
        "role",
        "is_verified",
        "status",
        'date_of_birth',
        'gender',
        "bvn",
        "nin",
        "referred_by",
        "referral_code"
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
        ];
    }

    public function bankAccount()
    {
        return $this->hasOne(AccountDetail::class);
    }
    public function ninRecord()
    {
        return $this->hasOne(CpNinVerification::class);
    }
    // Generate a unique referral code
    public static function generateUniqueCode()
    {
        $code = Str::random(10);
        while (self::where('referral_code', $code)->exists()) {
            $code = Str::random(8);
        }
        return $code;
    }

    protected static function boot()
    {
        parent::boot();

        // Temporary code to allow creation
        static::creating(function ($user) {
            $user->referral_code = 'TEMP'; // Will be updated post-save
        });

        static::created(function ($user) {
            $prefix = 'ARAROMI';
            $date = now()->format('Ymd');
            $random = strtoupper(Str::random(6));
            $user->referral_code = "{$prefix}-{$user->id}-{$date}-{$random}";
            $user->save();
        });
    }
    public function referredUsers()
    {
        return $this->hasMany(User::class, 'referred_by');
    }
}
