<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'product_id',
        'initial_stock',
        'sold_stock',
        'current_stock',
        'min_stock_alert',
    ];

    protected function casts(): array
    {
        return [
            'initial_stock' => 'integer',
            'sold_stock' => 'integer',
            'current_stock' => 'integer',
            'min_stock_alert' => 'integer',
        ];
    }

    public function isLowStock(): bool
    {
        return $this->current_stock < $this->min_stock_alert;
    }

    public function getNextRestockAtAttribute(): ?Carbon
    {
        return $this->updated_at?->copy()->addDays(3);
    }

    public function restockScheduleStatus(): string
    {
        $nextRestockAt = $this->next_restock_at;

        if (! $nextRestockAt) {
            return 'unknown';
        }

        if ($nextRestockAt->lt(now()->startOfDay())) {
            return 'overdue';
        }

        if ($nextRestockAt->isToday()) {
            return 'today';
        }

        if ($nextRestockAt->isTomorrow()) {
            return 'tomorrow';
        }

        return 'upcoming';
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
