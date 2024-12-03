<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Documento extends Model
{
    use HasFactory;
    // Definir la tabla y las columnas que serán asignadas masivamente
    // Definir la tabla y las columnas que serán asignadas masivamente
    protected $table = 'documentos';

    // Definir la clave primaria
    protected $primaryKey = 'id_documento';

    protected $fillable = [
        'numero_oficio',
        'fecha_recepcion',
        'remitente',
        'tipo',
        'descripcion',
        'observaciones',
        'archivo',
    ];
}