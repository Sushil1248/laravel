<?php

namespace App\Channels;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Channels\DatabaseChannel as IlluminateDatabaseChannel;

class DatabaseChannel extends IlluminateDatabaseChannel
{
    /**
    * Send the given notification.
    *
    * @param mixed $notifiable
    * @param \Illuminate\Notifications\Notification $notification
    * @return \Illuminate\Database\Eloquent\Model
    */
    public function send($notifiable, Notification $notification)
    {
        return $notifiable->routeNotificationFor('database')->create([
            'id'      => $notification->id,
            'type'    => get_class($notification),
            // 'from_user'=> $notification->from_user ? $notification->from_user->id : null,
            'data'    => $this->getData($notifiable, $notification),
            'action_type'   =>  $notification->action ? get_class($notification->action) : null,
            'action_id'     =>  $notification->action ? $notification->action->id : null,
            'read_at' => null,
        ]);
    }
}