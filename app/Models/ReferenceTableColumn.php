<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class ReferenceTableColumn extends Model
{
    protected $fillable = ['section', 'name', 'data_type', 'elma_type', 'is_required', 'is_readonly', 'is_key', 'width', 'values', 'settings', 'position'];

    protected function casts(): array
    {
        return ['values' => AsArrayObject::class, 'settings' => AsArrayObject::class, 'is_required' => 'boolean', 'is_readonly' => 'boolean', 'is_key' => 'boolean'];
    }

    public function referenceTable(): BelongsTo
    {
        return $this->belongsTo(ReferenceTable::class);
    }
}
