<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Enterprise extends Model
{
    use HasFactory, HasUuids;

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
        'name',
        'key',
        'status',
        'owner_uuid',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'status' => 'boolean',
    ];

    /**
     * Relationship with the enterprise's owner.
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_uuid', 'uuid');
    }

    /**
     * Relationship with the enterprise's users.
     */
    public function users()
    {
        return $this->hasMany(User::class, 'enterprise_uuid', 'uuid');
    }

    /**
     * Retrieves an enterprise by its lookup key.
     * 
     * @param string $key
     * @return Enterprise|null
     */
    public static function findByKey($key)
    {
        return static::where('key', $key)->first();
    }

    /**
     * Checks if the enterprise is active.
     * 
     * @return bool
     */
    public function isActive()
    {
        return $this->status === true;
    }

    /**
     * Get the enterprise URL based on its key.
     * 
     * @return string
     */
    public function getUrlAttribute()
    {
        return config('app.url') . '/' . $this->key;
    }

    public function enterpriseModules()
    {
        return $this->hasMany(EnterpriseModule::class, 'enterprise_uuid', 'uuid');
    }
}