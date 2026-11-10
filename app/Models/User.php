<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
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
        'currency',
        'monthly_budget',
        'password',
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
            'monthly_budget' => 'decimal:2',
            'password' => 'hashed',
        ];
    }

    /**
     * User subscriptions relationship.
     */
    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    /**
     * Format money with user's preferred currency symbol.
     */
    public function formatCurrency(float|int|string|null $amount): string
    {
        $amount = (float) ($amount ?? 0);
        $curr = $this->currency ?? 'Rp';

        if (trim($curr) === 'Rp') {
            return 'Rp ' . number_format($amount, 0, ',', '.');
        }

        return $curr . number_format($amount, 2, '.', ',');
    }
}
