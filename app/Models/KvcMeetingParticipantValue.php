<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KvcMeetingParticipantValue extends Model
{
    protected $fillable = [
        'participant_id', 'current_obligation', 'current_result', 'current_comment',
        'version', 'updated_by_user_id', 'updated_by_name',
    ];

    protected function casts(): array
    {
        return ['version' => 'integer'];
    }

    public function participant(): BelongsTo
    {
        return $this->belongsTo(KvcMeetingParticipant::class, 'participant_id');
    }
}
