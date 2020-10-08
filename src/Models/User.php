<?php

namespace Cc\Labems\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use  Notifiable;
    protected $table = LABEMS_ENTRY . '_users';
    protected $fillable = [
        'username',
        'password',
    ];
    protected $hidden = [
        'password',
    ];

    public function permission()
    {
        return $this->hasMany(UserPermission::class, 'uid');
    }

    public function routeList()
    {
        if ($this->isSuper()) {
            return ['*'];
        }
        return $this->permission->pluck('route_path')->toArray();
    }

    public function findPath($path)
    {
        return $this->permission->where('route_path', $path)->first();
    }

    public function isSuper()
    {
        if (1 == $this->id || $this->findPath('*')) {
            return true;
        }
        return false;
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
}
