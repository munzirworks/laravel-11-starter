<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quotation extends Model
{
    use HasFactory;

    protected $fillable = [
        'doc_no',
        'customer_id',
        'quotation_date',
        'valid_until',
        'subtotal',
        'discount',
        'tax',
        'total',
        'status',
        'remarks',
        'converted_sales_order_id',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'quotation_date' => 'date',
            'valid_until' => 'date',
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
        return $this->hasMany(QuotationItem::class);
    }

    public function convertedSalesOrder()
    {
        return $this->belongsTo(SalesOrder::class, 'converted_sales_order_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public static function generateDocNo(): string
    {
        $last = self::whereNotNull('doc_no')
            ->latest('id')
            ->value('doc_no');

        if (! $last || ! preg_match('/QT-(\d{6})$/', $last, $m)) {
            return 'QT-000001';
        }

        $next = (int) $m[1] + 1;

        return sprintf('QT-%06d', $next);
    }
}
