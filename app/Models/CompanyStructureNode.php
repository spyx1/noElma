<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CompanyStructureNode extends Model
{
    use HasFactory;

    protected $fillable = [
        'parent_id',
        'title',
        'allows_multiple_users',
        'sort_order',
    ];

    protected $casts = [
        'allows_multiple_users' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')
            ->orderBy('sort_order')
            ->orderBy('id');
    }

    public function childrenRecursive(): HasMany
    {
        return $this->children()->with(['users', 'childrenRecursive']);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'company_structure_node_user')
            ->withTimestamps();
    }
}
