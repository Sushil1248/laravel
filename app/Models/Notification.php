<?php

namespace App\Models;

use Illuminate\Notifications\DatabaseNotification;

class Notification extends DatabaseNotification
{
    
    /* Get action of notification */
    public function action(){
        return $this->morphTo();
    }
}