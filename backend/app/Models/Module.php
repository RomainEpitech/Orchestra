<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Module extends Model
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
        'name',
        'description',
        'key',
        'is_core',
        'free_limits',
        'price',
    ];

    /**
     * Les attributs qui doivent être convertis
     *
     * @var array
     */
    protected $casts = [
        'is_core' => 'boolean',
        'free_limits' => 'json',
        'price' => 'decimal:2',
    ];

    /**
     * Relation avec les entreprises qui utilisent ce module
     *
     * @return HasMany
     */
    public function enterpriseModules(): HasMany
    {
        return $this->hasMany(EnterpriseModule::class, 'module_uuid', 'uuid');
    }
}