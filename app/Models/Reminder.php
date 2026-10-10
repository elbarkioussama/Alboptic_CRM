<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ReminderStatus;
use App\Enums\ReminderType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reminder extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'client_id',
        'order_id',
        'reminder_type',
        'reminder_date',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'reminder_type' => ReminderType::class,
            'reminder_date' => 'date',
            'status' => ReminderStatus::class,
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', ReminderStatus::Pending->value);
    }

    public function scopeDueToday(Builder $query): Builder
    {
        return $query->where('status', ReminderStatus::Pending->value)
            ->whereDate('reminder_date', '<=', now()->toDateString());
    }
}
