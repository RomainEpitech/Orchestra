<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EnterpriseModule extends Model
{
    use HasUuids;

    /**
     * Indique que l'ID ne s'auto-incrémente pas
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * Le type de l'ID
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * Le nom de la clé primaire
     *
     * @var string
     */
    protected $primaryKey = 'uuid';

    /**
     * Les attributs qui peuvent être assignés en masse
     *
     * @var array
     */
    protected $fillable = [
        'uuid',
        'enterprise_uuid',
        'module_uuid',
        'status',
        'is_premium',
    ];

    /**
     * Les attributs qui doivent être convertis
     *
     * @var array
     */
    protected $casts = [
        'is_premium' => 'boolean',
    ];

    /**
     * Relation avec l'entreprise
     *
     * @return BelongsTo
     */
    public function enterprise(): BelongsTo
    {
        return $this->belongsTo(Enterprise::class, 'enterprise_uuid', 'uuid');
    }

    /**
     * Relation avec le module
     *
     * @return BelongsTo
     */
    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class, 'module_uuid', 'uuid');
    }
}