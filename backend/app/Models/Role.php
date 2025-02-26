<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    use HasFactory, HasUuids;

    /**
     * La clé primaire associée à la table.
     *
     * @var string
     */
    protected $primaryKey = 'uuid';

    /**
     * Indique si la clé primaire est auto-incrémentée.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * Le type de la clé primaire.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'authority',
        'color_hex',
        'enterprise_uuid',
        'is_shared'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'authority' => 'json',
        'is_shared' => 'boolean',
    ];

    /**
     * Get the enterprise that owns the role.
     */
    public function enterprise(): BelongsTo
    {
        return $this->belongsTo(Enterprise::class, 'enterprise_uuid', 'uuid');
    }

    /**
     * Get the users for the role.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'role_uuid', 'uuid');
    }
}