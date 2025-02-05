<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Historico extends Model
{
    // Campos rellenables
    protected $fillable = [
        'id_documento',
        'id_usuario',
        'destinatario',
        'estado_anterior',
        'estado_nuevo',
        'fecha_cambio',
        'observaciones',
    ];
    // Relación con la tabla documentos
    public function documento()
    {
        return $this->belongsTo(Documento::class, 'id_documento');
    }

    // Relación con la tabla usuarios
    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }
    // Relación con el destinatario (otro usuario)
    public function destinatarioUser()
    {
        return $this->belongsTo(User::class, 'destinatario');
    }

    
}