<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CommonNotification extends Notification
{
    use Queueable;

    public $message,
    $action;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct( $message = null , $action = null )
    {
        $this->message = $message;
        $this->action = $action;
        
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
    }

    

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'message'   =>  $this->message
        ];
    }

    public function shouldSend($notifiable, $channel){
        return $notifiable->id != \Auth::id();
    }
}
