<?php

namespace App\Models;

use Laravel\Cashier\Subscription as BaseSubscription;

class Subscription extends BaseSubscription
{
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}