<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'first_name',
        'last_name',
        'full_name',
        'phone',
        'email',
        'gender',
        'date_of_birth',
        'address',
        'city',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (Client $client): void {
            $client->first_name = trim($client->first_name ?? '');
            $client->last_name = trim($client->last_name ?? '');
            $client->full_name = trim("{$client->first_name} {$client->last_name}");
        });
    }

    public function prescriptions(): HasMany
    {
        return $this->hasMany(Prescription::class)->orderByDesc('prescription_date')->orderByDesc('id');
    }

    public function latestPrescription(): HasOne
    {
        return $this->hasOne(Prescription::class)->latestOfMany('prescription_date');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class)->orderByDesc('order_date')->orderByDesc('id');
    }

    public function latestOrder(): HasOne
    {
        return $this->hasOne(Order::class)->latestOfMany('order_date');
    }

    public function payments(): HasManyThrough
    {
        return $this->hasManyThrough(Payment::class, Order::class)->orderByDesc('payment_date');
    }

    public function documents(): HasManyThrough
    {
        return $this->hasManyThrough(Document::class, Order::class)->orderByDesc('created_at');
    }

    public function reminders(): HasMany
    {
        return $this->hasMany(Reminder::class)->orderBy('reminder_date');
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (blank($term)) {
            return $query;
        }

        $cleanTerm = trim($term);
        $phoneSearch = preg_replace('/[^\d+]/', '', $cleanTerm);

        return $query->where(function (Builder $subQuery) use ($cleanTerm, $phoneSearch): void {
            $subQuery->where('full_name', 'like', "%{$cleanTerm}%")
                ->orWhere('first_name', 'like', "%{$cleanTerm}%")
                ->orWhere('last_name', 'like', "%{$cleanTerm}%")
                ->orWhere('phone', 'like', "%{$cleanTerm}%")
                ->orWhere('email', 'like', "%{$cleanTerm}%");

            if (!empty($phoneSearch)) {
                $subQuery->orWhere('phone', 'like', "%{$phoneSearch}%");
            }
        });
    }

    public function totalOutstandingDebt(): float
    {
        return (float) $this->orders()
            ->whereNotIn('status', ['cancelled'])
            ->sum('remaining_amount');
    }

    public function totalLifetimeSpend(): float
    {
        return (float) $this->orders()
            ->whereNotIn('status', ['cancelled'])
            ->sum('total_amount');
    }

    public function totalPaid(): float
    {
        return (float) $this->orders()
            ->whereNotIn('status', ['cancelled'])
            ->sum('amount_paid');
    }

    public function canBeSafelyDeleted(): bool
    {
        $hasActiveOrders = $this->orders()
            ->whereNotIn('status', ['cancelled'])
            ->exists();

        $hasDebt = $this->totalOutstandingDebt() > 0.0;

        return ! $hasActiveOrders && ! $hasDebt;
    }

    public function getAgeAttribute(): ?int
    {
        if (! $this->date_of_birth) {
            return null;
        }

        return (int) $this->date_of_birth->age;
    }
}
