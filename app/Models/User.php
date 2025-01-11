<?php

namespace App\Models;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens; 
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Tymon\JWTAuth\Contracts\JWTSubject; 

class User extends Authenticatable implements JWTSubject 
{
    /**
     * Menentukan data yang digunakan untuk menghasilkan token JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();  
    }

    /**
     * Menentukan data tambahan yang akan disertakan dalam payload token JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
    use HasFactory, SoftDeletes, Notifiable;
    protected $fillable = ['name', 'email', 'password', 'role', 'company_id', 'phone', 'address','phone'];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}

