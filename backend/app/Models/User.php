<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasUuids, Notifiable, HasFactory;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'uuid';

    /**
     * Indicates if the primary key is auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The data type of the primary key.
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
        'firstname',
        'lastname',
        'email',
        'password',
        'avatar',
        'enterprise_uuid',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Relationship with the user's enterprise.
     */
    public function enterprise()
    {
        return $this->belongsTo(Enterprise::class, 'enterprise_uuid', 'uuid');
    }

    /**
     * Relationship with enterprises owned by the user.
     */
    public function ownedEnterprise()
    {
        return $this->hasOne(Enterprise::class, 'owner_uuid', 'uuid');
    }

    /**
     * Checks if the user is an owner of an enterprise.
     */
    public function isOwner()
    {
        return $this->ownedEnterprise()->exists();
    }

    /**
     * Get the user's full name.
     */
    public function getFullNameAttribute()
    {
        return "{$this->firstname} {$this->lastname}";
    }
}