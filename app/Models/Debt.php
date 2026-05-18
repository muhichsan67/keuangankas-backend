<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Debt extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'source',
        'monthly_cost',
        'monthly_deadline',
        'total_tenor',
    ];

    protected function casts(): array
    {
        return [
            'monthly_cost'     => 'decimal:2',
            'monthly_deadline' => 'integer',
            'total_tenor'      => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke transaksi yang merupakan pengeluaran (cicilan hutang).
     * Otomatis memfilter hanya transaksi dengan type = 'out'.
     */
    public function expenses(): HasMany
    {
        return $this->hasMany(Transaction::class)->where('type', 'out');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }
}
