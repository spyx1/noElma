<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KvcNode extends Model
{
    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id', 'parent_id', 'type', 'type_name', 'title', 'description', 'additional',
        'indicator_type', 'range_from', 'range_to', 'true_label', 'false_label',
        'current_value', 'autoaudit', 'owner_user_id', 'owner_name', 'start_date',
        'end_date', 'last_value_date', 'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'range_from' => 'decimal:4',
            'range_to' => 'decimal:4',
            'start_date' => 'date:Y-m-d',
            'end_date' => 'date:Y-m-d',
            'last_value_date' => 'date:Y-m-d',
            'sort_order' => 'integer',
        ];
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('sort_order')->orderBy('id');
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_user_id');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'kvc_node_users', 'kvc_node_id', 'user_id');
    }

    public function meetingManagers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'kvc_node_meeting_managers', 'kvc_node_id', 'user_id');
    }
}
