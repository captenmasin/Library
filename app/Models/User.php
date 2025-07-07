<?php

namespace App\Models;

use Filament\Panel;
use App\Traits\HasAvatar;
use App\Enums\UserPermission;
use Laravel\Sanctum\HasApiTokens;
use Spatie\MediaLibrary\HasMedia;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Glorand\Model\Settings\Traits\HasSettingsField;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable implements HasMedia, MustVerifyEmail
{
    use HasApiTokens, HasAvatar, HasFactory, HasRoles, HasSettingsField, InteractsWithMedia, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'remember_token',
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
        'library' => [
            'view' => 'grid',
            'tilt_books' => true,
        ],
    ];

    public function getSettingsRules(): array
    {
        return [
            'library' => 'array',
            'library.view' => ['string', 'in:grid,list,shelf'],
            'library.tilt_books' => ['boolean'],
        ];
    }

    public function getBookIdentifiers(): array
    {
        $books = $this->relationLoaded('books')
            ? $this->books
            : $this->books()->withPivot('status')->get();

        return $books->pluck('pivot.status', 'identifier')->toArray();
    }

    public function getAvatarAttribute(): string
    {
        return $this->getFirstMediaUrl('avatar');
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->can(UserPermission::VIEW_ADMIN_PANEL);
    }

    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class);
    }

    public function book_covers(): HasMany
    {
        return $this->hasMany(Cover::class);
    }

    public function books(): BelongsToMany
    {
        return $this->belongsToMany(Book::class)
            ->using(BookUser::class)
            ->withPivot(['status', 'tags', 'created_at', 'updated_at']);
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
