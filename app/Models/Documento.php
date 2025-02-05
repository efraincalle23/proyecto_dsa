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
        'origen',//emitido o recibido
        'documento_padre_id', // Agregar el nuevo campo al fillable
    ];

    public function historicos()
    {
        return $this->hasMany(Historico::class, 'id_documento');
    }
    // Relación para el documento padre
    public function documentoPadre()
    {
        return $this->belongsTo(Documento::class, 'documento_padre_id');
    }

    // Relación para los documentos hijos (subdocumentos o respuestas)
    public function subDocumentos()
    {
        return $this->hasMany(Documento::class, 'documento_padre_id');
    }

}