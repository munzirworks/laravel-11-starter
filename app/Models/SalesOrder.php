<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'doc_no',
        'customer_id',
        'order_date',
        'subtotal',
        'discount',
        'tax',
        'total',
        'status',
        'remarks',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'order_date' => 'date',
            'subtotal' => 'decimal:2',
            'discount' => 'decimal:2',
            'tax' => 'decimal:2',
            'total' => 'decimal:2',
        ];
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function items()
    {
        return $this->hasMany(SalesOrderItem::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public static function generateDocNo(): string
    {
        $lastDocNo = self::whereNotNull('doc_no')
            ->latest('id')
            ->value('doc_no');

        if (! $lastDocNo || ! preg_match('/SO-(\d{6})$/', $lastDocNo, $matches)) {
            return 'SO-000001';
        }

        $next = (int) $matches[1] + 1;

        return sprintf('SO-%06d', $next);
    }
}
