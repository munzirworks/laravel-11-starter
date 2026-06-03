<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'doc_no',
        'customer_id',
        'sales_order_id',
        'invoice_date',
        'due_date',
        'subtotal',
        'discount',
        'tax',
        'total',
        'paid_amount',
        'balance_amount',
        'status',
        'remarks',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'invoice_date' => 'date',
            'due_date' => 'date',
            'subtotal' => 'decimal:2',
            'discount' => 'decimal:2',
            'tax' => 'decimal:2',
            'total' => 'decimal:2',
            'paid_amount' => 'decimal:2',
            'balance_amount' => 'decimal:2',
        ];
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function salesOrder()
    {
        return $this->belongsTo(SalesOrder::class);
    }

    public static function generateDocNo(): string
    {
        $lastDocNo = self::whereNotNull('doc_no')
            ->latest('id')
            ->value('doc_no');

        if (! $lastDocNo || ! preg_match('/INV-(\d{6})$/', $lastDocNo, $matches)) {
            return 'INV-000001';
        }

        $number = (int) $matches[1] + 1;

        return 'INV-' . str_pad($number, 6, '0', STR_PAD_LEFT);
    }
}
