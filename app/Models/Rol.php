<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Rol extends Model
{
    //
    use HasFactory;

    // La tabla asociada al modelo (opcional si sigue la convención)
    protected $table = 'roles';

    // Atributos que se pueden asignar masivamente
    protected $fillable = ['nombre_rol'];

    // Relación con los usuarios (un rol tiene muchos usuarios)
    public function usuarios()
    {
        return $this->hasMany(Usuario::class, 'id_rol', 'id_rol');
    }
}