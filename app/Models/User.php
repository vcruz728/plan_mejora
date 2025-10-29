<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Auth\Notifications\ResetPassword as ResetPasswordBase;


class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    use Notifiable;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
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
        'password' => 'hashed',
    ];

    public function sendPasswordResetNotification($token)
    {
        // Construye la URL del botón (usa tu ruta)
        $url = url(route('password.reset', [
            'token' => $token,
            'email' => $this->email,
        ], false));

        $this->notify(new ResetPasswordNotification($url));
    }

    public function des()
    {
        return $this->belongsTo(Catalogos\Des::class, 'id_des');
    }
    public function unidadAcademica()
    {
        return $this->belongsTo(Catalogos\UnidadesAcademicas::class, 'id_ua');
    }
    public function sede()
    {
        return $this->belongsTo(Catalogos\Sedes::class, 'id_sede');
    }
    public function programa()
    {
        return $this->belongsTo(Catalogos\ProgramasEducativos::class, 'id_programa');
    }
    public function nivel()
    {
        return $this->belongsTo(Catalogos\NivelesEstudio::class, 'id_nivel');
    }
    public function modalidad()
    {
        return $this->belongsTo(Catalogos\Modalidad::class, 'id_modalidad');
    }

    public function complementosCapturados()
    {
        return $this->hasMany(ComplementoPlan::class, 'id_usuario');
    }
    public function procedenciaRef()
    {
        return $this->belongsTo(\App\Models\Catalogos\Procedencias::class, 'procedencia', 'id');
    }
}
