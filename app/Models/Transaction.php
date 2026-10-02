<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';
    public const STATUS_PROCESSING = 'processing';
    public const STATUS_READY = 'ready';
    public const STATUS_COMPLETED = 'completed';

    public const STATUS_SEQUENCE = [
        self::STATUS_PENDING,
        self::STATUS_PROCESSING,
        self::STATUS_READY,
        self::STATUS_COMPLETED,
    ];

    protected $guarded = [];

    public function statusDetails(): array
    {
        return match ($this->status) {
            self::STATUS_PENDING => [
                'label' => 'Pesanan Berhasil Dibuat',
                'color' => 'primary',
                'classes' => 'bg-orange-50 text-orange-700 border-orange-200',
            ],
            self::STATUS_PROCESSING => [
                'label' => 'Pesanan Sedang Disiapkan',
                'color' => 'primary',
                'classes' => 'bg-orange-50 text-orange-600 border-orange-200',
            ],
            self::STATUS_READY => [
                'label' => 'Pesanan Siap Diambil',
                'color' => 'primary',
                'classes' => 'bg-orange-50 text-orange-700 border-orange-200',
            ],
            default => [
                'label' => 'Selesai',
                'color' => 'primary',
                'classes' => 'bg-orange-50 text-orange-700 border-orange-200',
            ],
        };
    }

    public static function statusLabel(string|null $status): string
    {
        return (new self(['status' => $status]))->statusDetails()['label'];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function details()
    {
        return $this->hasMany(TransactionDetail::class);
    }
}
