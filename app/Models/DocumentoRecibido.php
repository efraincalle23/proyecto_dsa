<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentoRecibido extends Model
{
    use HasFactory;

    protected $table = 'documentos_recibidos';  // Especificamos la tabla

    protected $fillable = [
        'numero_oficio',
        'asunto',
        'fecha_recibido',
        'tipo',
        'remitente',//persona remitente
        'observaciones',
        'entidad_id',//entidad remitente
        'eliminado',
        'nombre_doc',
        'formato_documento', // Este es el nuevo campo 'virtual' o 'fisico'
    ];

    // Relación con la entidad (remitente)
    public function entidad()
    {
        return $this->belongsTo(Entidad::class, 'entidad_id');
    }

    // Relación con la entidad remitente (ajusta esto según tu base de datos)
    public function remitente()
    {
        return $this->belongsTo(DocumentoRecibido::class, 'remitente_id');
    }

    public function HistorialDocumento()
    {
        return $this->hasMany(HistorialDocumento::class, 'id_documento');
    }
}