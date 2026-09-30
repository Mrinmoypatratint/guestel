<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceRequest extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'hotel_id',
        'room_id',
        'guest_session_id',
        'service_id',
        'department_id',
        'request_number',
        'priority',
        'status',
        'guest_note',
        'response_due_at',
        'completion_due_at',
        'accepted_at',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'response_due_at' => 'datetime',
            'completion_due_at' => 'datetime',
            'accepted_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }
}
