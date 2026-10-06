<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class ReferenceTable extends Model
{
    protected $fillable = ['external_id', 'name', 'row_count', 'script', 'default_values', 'default_result_values', 'source_json', 'created_by', 'updated_by'];

    protected function casts(): array
    {
        return [
            'default_values' => 'array',
            'default_result_values' => 'array',
            'source_json' => 'array',
        ];
    }

    public function columns(): HasMany
    {
        return $this->hasMany(ReferenceTableColumn::class)->orderBy('position');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function editor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
