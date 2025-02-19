<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Enterprise extends Model
{
    use HasFactory, HasUuids;

    /**
     * La clé primaire pour le modèle.
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
     * Le type de données de la clé primaire.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * Les attributs qui sont assignables en masse.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'key',
        'status',
        'owner_uuid',
    ];

    /**
     * Les attributs qui devraient être convertis.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'status' => 'boolean',
    ];

    /**
     * Relation avec le propriétaire de l'entreprise.
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_uuid', 'uuid');
    }

    /**
     * Relation avec les utilisateurs de l'entreprise.
     */
    public function users()
    {
        return $this->hasMany(User::class, 'enterprise_uuid', 'uuid');
    }

    /**
     * Récupère une entreprise par sa clé de récupération.
     * 
     * @param string $key
     * @return Enterprise|null
     */
    public static function findByKey($key)
    {
        return static::where('key', $key)->first();
    }

    /**
     * Vérifie si l'entreprise est active.
     * 
     * @return bool
     */
    public function isActive()
    {
        return $this->status === true;
    }

    /**
     * Obtenir l'URL de l'entreprise basée sur la clé.
     * 
     * @return string
     */
    public function getUrlAttribute()
    {
        return config('app.url') . '/' . $this->key;
    }
}