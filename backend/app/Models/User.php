<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasUuids, Notifiable;

    /**
     * La clé primaire du modèle.
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
        'firstname',
        'lastname',
        'email',
        'password',
        'avatar',
        'enterprise_uuid',
    ];

    /**
     * Les attributs qui devraient être cachés pour la sérialisation.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Les attributs qui devraient être convertis.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Relation avec l'entreprise de l'utilisateur.
     */
    public function enterprise()
    {
        return $this->belongsTo(Enterprise::class, 'enterprise_uuid', 'uuid');
    }

    /**
     * Relation avec les entreprises dont l'utilisateur est propriétaire.
     */
    public function ownedEnterprise()
    {
        return $this->hasOne(Enterprise::class, 'owner_uuid', 'uuid');
    }

    /**
     * Vérifie si l'utilisateur est propriétaire d'une entreprise.
     */
    public function isOwner()
    {
        return $this->ownedEnterprise()->exists();
    }

    /**
     * Obtenir le nom complet de l'utilisateur.
     */
    public function getFullNameAttribute()
    {
        return "{$this->firstname} {$this->lastname}";
    }
}