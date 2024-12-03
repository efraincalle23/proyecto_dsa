<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Usuario extends Model
{
    //
    use HasFactory;

    protected $table = 'usuarios'; // Especifica la tabla si no sigue la convención
    protected $primaryKey = 'ID_Usuario'; // Define la clave primaria si es diferente de 'id'

    // Si los timestamps no se llaman 'created_at' y 'updated_at', puedes deshabilitarlos o configurarlos
    public $timestamps = true;

    // Define los atributos que se pueden asignar en masa
    protected $fillable = [
        'Nombre',
        'Apellido',
        'Email',
        'Contrasena',
        // Agrega otros campos si es necesario
    ];

    // Si quieres ocultar campos al serializar el modelo (por ejemplo, la contraseña)
    protected $hidden = [
        'Contrasena',
    ];

    // **Relaciones con otros modelos** (si las hay)
    // public function rol()
    // {
    //     return $this->belongsTo(Rol::class, 'ID_Rol');
    // }
}