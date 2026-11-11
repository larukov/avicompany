<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookingPassenger extends Model
{
    protected $fillable = [
        'booking_id', 'firstname', 'lastname', 'patronomic',
        'birth_date', 'gender', 'document_type', 'document_series',
        'document_number', 'document_issued_by', 'document_issued_at',
        'document_expires_at', 'phone', 'email',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'document_issued_at' => 'date',
        'document_expires_at' => 'date',
    ];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }
}