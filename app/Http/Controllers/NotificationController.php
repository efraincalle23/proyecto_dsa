<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    //
    // En NotificationController.php
    public function index()
    {
        $notifications = request()->user()->notifications()->paginate(10); // Agregar paginación

        return view('notifications.index', compact('notifications'));
        //return view('notifications.index', ['notifications' => request()->user()->notifications]);
    }

    public function show($id)
    {
        $notification = request()->user()->notifications()->findOrFail($id);
        // Marca la notificación como leída
        $notification->markAsRead();
        return view('notifications.show', ['notification' => $notification]);
    }
    public function markAllAsRead()
    {
        request()->user()->unreadNotifications->markAsRead();
        return redirect()->route('notifications.index')->with('success', 'Todas las notificaciones fueron marcadas como leídas.');
    }

}