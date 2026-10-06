<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KvcMeetingSection extends Model
{
    protected $fillable = ['meeting_id', 'node_id', 'node_name', 'sort_order'];

    public function meeting(): BelongsTo
    {
        return $this->belongsTo(KvcMeeting::class, 'meeting_id');
    }
}
