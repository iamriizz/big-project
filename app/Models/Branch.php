<?php

namespace App\Models;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    use HasFactory;

    public const OFFICIAL_NAMES = [
        'Fav Dimsum Bobotsari',
        'Fav Dimsum Karangmoncol',
        'Fav Dimsum Pengadegan',
    ];

    protected $guarded = ['id'];

    /** Determine whether this outlet can accept orders at the given WIB time. */
    public function isOpenAt(?CarbonInterface $time = null): bool
    {
        if (! $this->open_time || ! $this->close_time) {
            return false;
        }

        $currentTime = ($time ?? now())->timezone('Asia/Jakarta')->format('H:i:s');
        $openTime = substr((string) $this->open_time, 0, 8);
        $closeTime = substr((string) $this->close_time, 0, 8);

        if ($openTime > $closeTime) {
            return $currentTime >= $openTime || $currentTime <= $closeTime;
        }

        return $currentTime >= $openTime && $currentTime <= $closeTime;
    }

    public function operatingHours(): string
    {
        return sprintf(
            '%s - %s WIB',
            substr(str_replace(':', '.', (string) $this->open_time), 0, 5),
            substr(str_replace(':', '.', (string) $this->close_time), 0, 5),
        );
    }

    public function closedMessage(): string
    {
        return sprintf(
            'Mohon maaf, gerai %s saat ini sedang TUTUP. Jam operasional cabang ini adalah %s - %s WIB.',
            $this->name,
            (string) $this->open_time,
            (string) $this->close_time,
        );
    }

    /** Limit dashboard data to the three official Fav Dimsum branches. */
    public function scopeOfficial($query)
    {
        return $query->whereIn('name', self::OFFICIAL_NAMES);
    }

    public function stocks()
    {
        return $this->hasMany(Stock::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
