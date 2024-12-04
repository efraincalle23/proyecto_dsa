<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Usuario extends Model
{
    //
    use HasFactory;

    // Atributos que se pueden asignar masivamente
    protected $fillable = ['nombre', 'apellido', 'email', 'password', 'id_rol'];

    // Relación con el modelo Role (un usuario pertenece a un rol)
    public function role()
    {
        return $this->belongsTo(Rol::class, 'id_rol', 'id_rol');
    }
}