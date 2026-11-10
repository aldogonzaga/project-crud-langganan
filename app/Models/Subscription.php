<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subscription extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'name',
        'cost',
        'billing_cycle',
        'next_billing_date',
        'category',
        'status',
        'icon',
        'color',
        'notes',
        'trial_ends_at',
    ];

    protected function casts(): array
    {
        return [
            'cost' => 'decimal:2',
            'next_billing_date' => 'date',
            'trial_ends_at' => 'date',
        ];
    }

    /**
     * Relationship to owner user.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Calculate normalized monthly cost.
     */
    public function getMonthlyCostAttribute(): float
    {
        $cost = (float) $this->cost;
        return match ($this->billing_cycle) {
            'Weekly' => round(($cost * 52) / 12, 2),
            'Yearly' => round($cost / 12, 2),
            default => $cost,
        };
    }

    /**
     * Calculate yearly equivalent cost.
     */
    public function getYearlyCostAttribute(): float
    {
        $cost = (float) $this->cost;
        return match ($this->billing_cycle) {
            'Weekly' => round($cost * 52, 2),
            'Monthly' => round($cost * 12, 2),
            default => $cost,
        };
    }

    /**
     * Calculate days until next billing date.
     */
    public function getDaysUntilDueAttribute(): int
    {
        if (!$this->next_billing_date) {
            return 999;
        }

        $today = Carbon::today();
        $due = Carbon::parse($this->next_billing_date)->startOfDay();

        return (int) $today->diffInDays($due, false);
    }

    /**
     * Urgency level: overdue, critical (<= 3 days), warning (4-7 days), normal (> 7 days).
     */
    public function getUrgencyLevelAttribute(): string
    {
        if ($this->status === 'Cancelled') {
            return 'inactive';
        }

        $days = $this->days_until_due;

        if ($days < 0) {
            return 'overdue';
        }
        if ($days <= 3) {
            return 'critical'; // Highlighted in RED per requirement
        }
        if ($days <= 7) {
            return 'warning'; // Highlighted in YELLOW/WARNING
        }
        return 'normal';
    }

    /**
     * Smart category icon fallback if no icon provided or map existing emojis to Bootstrap icons.
     */
    public function getDisplayIconAttribute(): string
    {
        $icon = $this->icon;

        if (empty($icon)) {
            return match ($this->category) {
                'Entertainment' => 'bi-film',
                'Utilities' => 'bi-lightning-charge',
                'Health' => 'bi-heart-pulse',
                'Work' => 'bi-laptop',
                'Education' => 'bi-book',
                'Food' => 'bi-cup-hot',
                'Shopping' => 'bi-bag',
                default => 'bi-tag',
            };
        }

        // Map any legacy emojis to Bootstrap icons
        return match ($icon) {
            '🎵' => 'bi-music-note-beamed',
            '🎬' => 'bi-film',
            '💻' => 'bi-laptop',
            '🏋️', '🏋' => 'bi-heart-pulse',
            '⚡' => 'bi-lightning-charge',
            '☁️', '☁' => 'bi-cloud',
            '📚' => 'bi-book',
            '🥗' => 'bi-cup-hot',
            '🛍️', '🛍' => 'bi-bag',
            '⏳' => 'bi-hourglass-split',
            '✨' => 'bi-stars',
            default => $icon,
        };
    }

    /**
     * Render the icon as HTML (Bootstrap icon <i> or fallback).
     */
    public function getRenderedIconAttribute(): string
    {
        $icon = $this->display_icon;
        if (str_starts_with($icon, 'bi-') || str_starts_with($icon, 'bi ')) {
            $class = str_starts_with($icon, 'bi ') ? $icon : 'bi ' . $icon;
            return '<i class="' . e($class) . '"></i>';
        }

        return e($icon);
    }

    /**
     * Scope for active subscriptions.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'Active');
    }

    /**
     * Scope for free trials.
     */
    public function scopeFreeTrial(Builder $query): Builder
    {
        return $query->where('status', 'Free Trial');
    }

    /**
     * Scope for cancelled subscriptions.
     */
    public function scopeCancelled(Builder $query): Builder
    {
        return $query->where('status', 'Cancelled');
    }

    /**
     * Scope for subscriptions due in the next X days.
     */
    public function scopeDueWithin(Builder $query, int $days = 7): Builder
    {
        $today = Carbon::today()->toDateString();
        $maxDate = Carbon::today()->addDays($days)->toDateString();

        return $query->where('status', '!=', 'Cancelled')
            ->whereDate('next_billing_date', '>=', $today)
            ->whereDate('next_billing_date', '<=', $maxDate)
            ->orderBy('next_billing_date', 'asc');
    }

    /**
     * Query filter scope for search, category, status, cycle, sorting.
     */
    public function scopeFilter(Builder $query, array $filters): Builder
    {
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%");
            });
        }

        if (!empty($filters['category']) && $filters['category'] !== 'All') {
            $query->where('category', $filters['category']);
        }

        if (!empty($filters['status']) && $filters['status'] !== 'All') {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['cycle']) && $filters['cycle'] !== 'All') {
            $query->where('billing_cycle', $filters['cycle']);
        }

        // Sorting
        $sort = $filters['sort'] ?? 'due_soon';
        switch ($sort) {
            case 'cost_high':
                $query->orderBy('cost', 'desc');
                break;
            case 'cost_low':
                $query->orderBy('cost', 'asc');
                break;
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;
            case 'newest':
                $query->orderBy('created_at', 'desc');
                break;
            case 'due_soon':
            default:
                $query->orderBy('next_billing_date', 'asc');
                break;
        }

        return $query;
    }
}
