<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'user_type',
        'company_name',
        'business_registration_number',
        'is_admin',
        'is_active',
        'role_id',
        'b2b_status',
        'credit_limit',
        'password',
    ];

    public const B2B_TYPES = ['professionnel', 'hotel', 'restaurant', 'entreprise', 'institution', 'revendeur'];

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
            'is_admin' => 'boolean',
            'is_active' => 'boolean',
            'credit_limit' => 'decimal:2',
        ];
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class);
    }

    public function supplier(): HasOne
    {
        return $this->hasOne(Supplier::class);
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class)->latest();
    }

    public function unreadNotificationsCount(): int
    {
        return $this->notifications()->where('channel', 'interne')->whereNull('read_at')->count();
    }

    public function hasPermission(string $permission): bool
    {
        if ($this->is_admin) {
            return true;
        }

        return $this->role?->hasPermission($permission) ?? false;
    }

    public function isStaff(): bool
    {
        return $this->is_admin || $this->role_id !== null;
    }

    public function quotes(): HasMany
    {
        return $this->hasMany(Quote::class);
    }

    public function recurringOrders(): HasMany
    {
        return $this->hasMany(RecurringOrder::class);
    }

    public function isProfessionalType(): bool
    {
        return in_array($this->user_type, self::B2B_TYPES, true);
    }

    public function isApprovedB2B(): bool
    {
        return $this->b2b_status === 'valide';
    }

    public function creditUsed(): float
    {
        return (float) $this->orders()
            ->where('payment_method', 'credit')
            ->where('payment_status', '!=', 'paye')
            ->whereNotIn('status', ['annulee', 'remboursee'])
            ->sum('total');
    }

    public function creditAvailable(): float
    {
        if (! $this->credit_limit) {
            return 0;
        }

        return max(0, (float) $this->credit_limit - $this->creditUsed());
    }
}
