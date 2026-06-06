<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'doc_no',
        'invoice_id',
        'payment_date',
        'amount',
        'payment_method',
        'reference_no',
        'remarks',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'payment_date' => 'date',
            'amount' => 'decimal:2',
        ];
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
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

        if (! $lastDocNo || ! preg_match('/RCPT-(\d{6})$/', $lastDocNo, $matches)) {
            return 'RCPT-000001';
        }

        $number = (int) $matches[1] + 1;

        return 'RCPT-' . str_pad($number, 6, '0', STR_PAD_LEFT);
    }
}
