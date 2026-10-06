<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class KvcMeetingParticipant extends Model
{
    protected $fillable = [
        'meeting_id', 'section_node_id', 'user_id', 'user_name',
        'previous_status', 'attendance',
    ];

    public function meeting(): BelongsTo
    {
        return $this->belongsTo(KvcMeeting::class, 'meeting_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function value(): HasOne
    {
        return $this->hasOne(KvcMeetingParticipantValue::class, 'participant_id');
    }
}
