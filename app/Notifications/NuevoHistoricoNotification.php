<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\DatabaseMessage;

class NuevoHistoricoNotification extends Notification
{
    protected $historico;

    public function __construct($historico)
    {
        $this->historico = $historico;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];  // Usamos base de datos como canal
    }

    /**
     * Get the data for the database notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toDatabase($notifiable)
    {
        return [
            'documento_id' => $this->historico->id_documento,
            'estado_nuevo' => $this->historico->estado_nuevo,
            'observaciones' => $this->historico->observaciones,
            'mensaje' => 'Tienes un nuevo documento asignado',
        ];
    }
}