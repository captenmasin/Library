<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Traits\HasAvatar;
use Filament\Panel;
use Glorand\Model\Settings\Traits\HasSettingsField;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements HasMedia
{
    use HasApiTokens, HasRoles, InteractsWithMedia;
    use HasAvatar;

    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;

    use HasSettingsField;
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'email_verified_at',
        'password',
    ];

    protected $appends = [
        'avatar',
        'has_avatar',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
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
            'has_avatar' => 'bool',
        ];
    }

    public array $defaultSettings = [
        'books' => [
            'view' => 'grid',
        ],
    ];

    public array $settingsRules = [
        'books' => 'array',
        'books.view' => ['string', 'in:grid,list'],
    ];

    public function getBookIdentifiers(): array
    {
        $books = $this->books()->withPivot('status')->get();

        return $books->pluck('pivot.status', 'identifier')->toArray();
    }

    public function getAvatarAttribute(): string
    {
        return $this->getFirstMediaUrl('avatar');
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return false;
        //        return str_ends_with($this->email, '@yourdomain.com') && $this->hasVerifiedEmail();
    }

    public function book_covers(): HasMany
    {
        return $this->hasMany(Cover::class);
    }

    public function books(): BelongsToMany
    {
        return $this->belongsToMany(Book::class)
            ->withPivot(['status'])
            ->withTimestamps();
    }

    public function notes(): HasMany
    {
        return $this->hasMany(Note::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('avatar')->singleFile();
    }
}
