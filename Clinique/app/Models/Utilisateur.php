<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Auth\Passwords\CanResetPassword as CanResetPasswordTrait;
use Laravel\Sanctum\HasApiTokens;

class Utilisateur extends Authenticatable implements CanResetPassword
{
    use HasApiTokens, HasFactory, CanResetPasswordTrait;

    protected $table = 'utilisateurs';

    protected $fillable = [
        'email',
        'password',
        'role',
        'is_active',
    ];

    protected $hidden = [
        'password',
    ];

    protected function casts(): array
    {
        return [
            'password'  => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function patient()
    {
        return $this->hasOne(Patient::class, 'utilisateur_id');
    }

    public function medecin()
    {
        return $this->hasOne(Medecin::class, 'utilisateur_id');
    }

    public function receptionniste()
    {
        return $this->hasOne(Receptionniste::class, 'utilisateur_id');
    }
}
