<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Prescription extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'client_id',
        'prescription_date',
        'od_sphere',
        'od_cylinder',
        'od_axis',
        'od_addition',
        'og_sphere',
        'og_cylinder',
        'og_axis',
        'og_addition',
        'pd',
        'pd_right',
        'pd_left',
        'prescription_file_path',
        'prescribed_by',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'prescription_date' => 'date',
            'od_sphere' => 'decimal:2',
            'od_cylinder' => 'decimal:2',
            'od_axis' => 'integer',
            'od_addition' => 'decimal:2',
            'og_sphere' => 'decimal:2',
            'og_cylinder' => 'decimal:2',
            'og_axis' => 'integer',
            'og_addition' => 'decimal:2',
            'pd' => 'decimal:1',
            'pd_right' => 'decimal:1',
            'pd_left' => 'decimal:1',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function scopeSearch($query, ?string $term)
    {
        if (blank($term)) {
            return $query;
        }

        $cleanTerm = trim($term);
        $phoneSearch = preg_replace('/[^\d+]/', '', $cleanTerm);

        return $query->where(function ($subQuery) use ($cleanTerm, $phoneSearch): void {
            $subQuery->where('prescribed_by', 'like', "%{$cleanTerm}%")
                ->orWhere('notes', 'like', "%{$cleanTerm}%")
                ->orWhereHas('client', function ($clientQuery) use ($cleanTerm, $phoneSearch): void {
                    $clientQuery->where('full_name', 'like', "%{$cleanTerm}%")
                        ->orWhere('first_name', 'like', "%{$cleanTerm}%")
                        ->orWhere('last_name', 'like', "%{$cleanTerm}%");

                    if (!empty($phoneSearch)) {
                        $clientQuery->orWhere('phone', 'like', "%{$phoneSearch}%");
                    }
                });
        });
    }

    public function formatValue(?string $val): string
    {
        if ($val === null || $val === '') {
            return '-';
        }
        $num = (float) $val;
        return $num > 0 ? sprintf('+%.2f', $num) : sprintf('%.2f', $num);
    }

    public function getOdSummaryAttribute(): string
    {
        $parts = [];
        if ($this->od_sphere !== null) {
            $parts[] = 'SPH: ' . $this->formatValue((string)$this->od_sphere);
        }
        if ($this->od_cylinder !== null) {
            $parts[] = 'CYL: ' . $this->formatValue((string)$this->od_cylinder);
        }
        if ($this->od_axis !== null) {
            $parts[] = 'AXE: ' . $this->od_axis . '°';
        }
        if ($this->od_addition !== null) {
            $parts[] = 'ADD: ' . $this->formatValue((string)$this->od_addition);
        }
        return empty($parts) ? 'Sans correction saisie' : implode(' | ', $parts);
    }

    public function getOgSummaryAttribute(): string
    {
        $parts = [];
        if ($this->og_sphere !== null) {
            $parts[] = 'SPH: ' . $this->formatValue((string)$this->og_sphere);
        }
        if ($this->og_cylinder !== null) {
            $parts[] = 'CYL: ' . $this->formatValue((string)$this->og_cylinder);
        }
        if ($this->og_axis !== null) {
            $parts[] = 'AXE: ' . $this->og_axis . '°';
        }
        if ($this->og_addition !== null) {
            $parts[] = 'ADD: ' . $this->formatValue((string)$this->og_addition);
        }
        return empty($parts) ? 'Sans correction saisie' : implode(' | ', $parts);
    }

    public function getHasFileAttribute(): bool
    {
        return !empty($this->prescription_file_path);
    }
}
