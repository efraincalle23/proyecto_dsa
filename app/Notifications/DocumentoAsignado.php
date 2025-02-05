<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\DatabaseMessage;

class DocumentoAsignado extends Notification
{
    // Los datos que quieres pasar con la notificación
    protected $documento;

    public function __construct($documento)
    {
        $this->documento = $documento;
    }

    // Definir los canales que se usarán para enviar la notificación
    public function via($notifiable)
    {
        return ['database'];  // Usamos el canal 'database'
    }

    // Definir cómo se almacenará la notificación en la base de datos
    public function toDatabase($notifiable)
    {
        return [
            'documento_id' => $this->documento->id_documento,
            'numero_oficio' => $this->documento->numero_oficio,
            'remitente' => $this->documento->remitente,
            'descripcion' => $this->documento->descripcion,
            'fecha_recepcion' => $this->documento->fecha_recepcion,
            'mensaje' => "El documento {$this->documento->numero_oficio} ha sido asignado a ti."
        ];
    }
}