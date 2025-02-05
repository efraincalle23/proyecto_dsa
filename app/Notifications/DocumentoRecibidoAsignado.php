<?php
namespace App\Notifications;

use Illuminate\Notifications\Notification;
use App\Models\DocumentoRecibido;
use App\Models\DocumentoEmitido;
use Carbon\Carbon;

class DocumentoRecibidoAsignado extends Notification
{
    private $historico;

    public function __construct($historico)
    {
        $this->historico = $historico;
    }

    public function via($notifiable)
    {
        return ['database']; // Usamos la base de datos para almacenar las notificaciones
    }

    public function toDatabase2($notifiable)
    {
        // Aquí asignamos los datos que serán guardados en la tabla 'notifications'
        return [
            'message' => 'Nuevo documento asignado con el estado: ' . $this->historico->estado_nuevo,
            'documento_id' => $this->historico->id_documento,
            'destinatario' => $this->historico->destinatario,
            'observaciones' => $this->historico->observaciones,
        ];
    }
    public function toDatabase1($notifiable)
    {
        // Obtener los valores que necesitamos para el mensaje
        $idUsuario = $this->historico->id_usuario; // ID del usuario que realiza la asignación
        $idDocumento = $this->historico->id_documento; // ID del documento
        $estadoNuevo = $this->historico->estado_nuevo; // El nuevo estado del documento

        // Obtener el nombre del usuario asignador
        $usuario = request()->user()->find($idUsuario);
        $nombreUsuario = $usuario ? $usuario->nombre : 'Usuario desconocido'; // Asegúrate de que 'name' sea el campo correcto

        // Obtener el nombre o título del documento
        $documento = DocumentoRecibido::find($idDocumento);
        $nombreDocumento = $documento ? $documento->nombre_doc : 'Documento desconocido'; // Asegúrate de que 'numero_oficio' sea el campo correcto

        // Crear el mensaje personalizado
        $message = "{$nombreUsuario} te ha derivado el {$nombreDocumento} con el estado: {$estadoNuevo}";

        return [
            'message' => $message,
            'estado' => $estadoNuevo,
            'documento_id' => $idDocumento,
            'observaciones' => $this->historico->observaciones,
        ];
    }
    public function toDatabase($notifiable)
    {
        // Obtener los valores necesarios
        $idUsuario = $this->historico->id_usuario;
        $idDocumento = $this->historico->id_documento;
        $estadoNuevo = $this->historico->estado_nuevo;
        $origen = $this->historico->origen;


        // Obtener información del usuario
        $usuario = request()->user()->find($idUsuario);
        $nombreUsuario = $usuario ? $usuario->nombre . ' ' . $usuario->apellido : 'Usuario desconocido';

        // Obtener información del documento

        if ($origen === 'emitido') {
            $documento = DocumentoEmitido::find($idDocumento);
            $destinatario = $documento ? $documento->destino : 'Sin asunto';
        } else {
            $documento = DocumentoRecibido::find($idDocumento);
            $destinatario = $documento ? $documento->remitente : 'Sin asunto';
        }

        //$documento = DocumentoRecibido::find($idDocumento);
        $nombreDocumento = $documento ? $documento->nombre_doc : 'Documento desconocido';
        $numero_oficio = $documento ? $documento->numero_oficio : 'S/N';
        $fechaRecibido = $documento ? $documento->fecha_recibido : null;
        $asunto = $documento ? $documento->asunto : 'Sin asunto';


        // Crear mensaje y detalles adicionales
        $message = "{$nombreUsuario} te ha derivado el {$nombreDocumento}-( {$estadoNuevo})";

        return [
            'message' => $message,
            'estado' => $estadoNuevo,
            'documento_id' => $idDocumento,
            'usuario' => $destinatario,
            'nombre_documento' => $nombreDocumento,
            'fecha_recibido' => $fechaRecibido,
            'asunto' => $asunto,
            'observaciones' => $this->historico->observaciones,
            //'fecha_notificacion' => now(), // Fecha de la notificación
            'fecha_notificacion' => $this->historico->fecha_cambio,
            'origen' => $origen,
            'numero_oficio' => $numero_oficio,

        ];
    }

}