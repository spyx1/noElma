<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KvcMeeting extends Model
{
    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id', 'kvc_node_id', 'date_time', 'summary', 'closed', 'closed_at',
        'closed_by_user_id', 'closed_by_name', 'created_by_user_id', 'created_by_name',
        'updated_by_user_id', 'updated_by_name',
    ];

    protected function casts(): array
    {
        return [
            'date_time' => 'datetime',
            'closed' => 'boolean',
            'closed_at' => 'datetime',
        ];
    }

    public function sections(): HasMany
    {
        return $this->hasMany(KvcMeetingSection::class, 'meeting_id')->orderBy('sort_order')->orderBy('id');
    }

    public function participants(): HasMany
    {
        return $this->hasMany(KvcMeetingParticipant::class, 'meeting_id')->orderBy('id');
    }
}
