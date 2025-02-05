<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentoEmitido extends Model
{
    use HasFactory;

    protected $table = 'documentos_emitidos';  // Nombre de la tabla

    protected $fillable = [
        'numero_oficio',
        'asunto',
        'fecha_recibido',
        'tipo',
        'destino',
        'observaciones',
        'entidad_id',
        'respuesta_a',
        'eliminado',
        'nombre_doc',
        'formato_documento', // Este es el nuevo campo 'virtual' o 'fisico'
        'respondido_con', // Relación con documentos recibidos
    ];

    // Relación con la entidad receptora
    public function entidad()
    {
        return $this->belongsTo(Entidad::class, 'entidad_id');
    }


    // Relación con el documento recibido (si es una respuesta)
    public function respuestaADocumento()
    {
        return $this->belongsTo(DocumentoRecibido::class, 'respuesta_a');
    }
    public function respuestaconDocumento()
    {
        return $this->belongsTo(DocumentoRecibido::class, 'respondido_con');
    }

    // Relación con HistorialDocumento
    public function historialDocumento()
    {
        return $this->hasMany(HistorialDocumento::class, 'id_documento'); // Asegurarse de usar 'id' como clave
    }
    public function historiales()
    {
        return $this->hasMany(HistorialDocumento::class, 'id_documento', 'numero_oficio');
    }
}