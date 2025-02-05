<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistorialDocumento extends Model
{
    use HasFactory;

    /**
     * La tabla asociada al modelo.
     *
     * @var string
     */
    protected $table = 'historial_documentos';

    /**
     * Los atributos que se pueden asignar masivamente.
     *
     * @var array
     */
    protected $fillable = [
        'id_documento',
        'id_usuario',
        'estado_anterior',
        'estado_nuevo',
        'fecha_cambio',
        'observaciones',
        'destinatario',
        'origen',
    ];

    /**
     * Relación con el modelo Documento.
     */
    public function documento()
    {
        return $this->belongsTo(DocumentoRecibido::class, 'id_documento');
    }

    public function documentoEmitido()
    {
        //return $this->belongsTo(DocumentoEmitido::class, 'id_documento');
        return $this->belongsTo(DocumentoEmitido::class, 'id_documento', 'id');

    }

    /**
     * Relación con el modelo Usuario.
     */
    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }

    /**
     * Relación con el modelo Usuario para el destinatario.
     */
    public function destinatarioUser()
    {
        return $this->belongsTo(User::class, 'destinatario');
    }


}