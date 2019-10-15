<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;

    protected $fillable = [
        'name', 'email', 'password','first_name', 'last_name','phone','share_code'
    ];
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function thirdparty()
    {
        return $this->hasOne(Thirdparty::class);
    }

    public function carriers()
    {
        return $this->belongsToMany('App\Carrier');
    }

    public function carrierUsers()
    {
        return $this->hasMany('App\CarrierUser');
    }

    public function comments()
    {
        return $this->hasMany('App\Comment');
    }

    public function tickets()
    {
        return $this->hasMany('App\Ticket');
    }

    public function transactions()
    {
        return $this->hasMany('App\Transaction');
    }

    public function messages()
    {
        return $this->hasMany('App\Message');
    }

    public function observer()
    {
        return $this->hasOne('App\Observer');
    }

    public function logs()
    {
        return $this->hasMany('App\Log');
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    public function authorizeRoles($roles)
    {
        if (is_array($roles)) {
            return $this->hasAnyRole($roles) ||
                abort(401, 'شما به این بخش دسترسی ندارید ');
        }
        return $this->hasRole($roles) ||
            //abort(401, 'شما به این بخش دسترسی ندارید ');
            abort(404, 'شما اجازه دسترسی به این بخش را ندارید ');
    }

    public function hasAnyRole($roles)
    {
        return null !== $this->roles()->whereIn('name', $roles)->first();
    }

    public function hasRole($role)
    {
        return null !== $this->roles()->where('name', $role)->first();
    }
    public function files(){
        return $this->hasMany(File::class);
    }

    public function notifMessages()
    {
        return $this->hasMany(NotifMessage::class);
    }
    public function registrationIds(){
        return $this->hasMany(FcmRegisterId::class);
    }

    public function alives()
    {
        return $this->hasMany(Alive::class);
    }
}
