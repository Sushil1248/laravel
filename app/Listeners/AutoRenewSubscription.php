<?php
namespace App\Listeners;

use Imdhemy\Purchases\Events\GooglePlay\SubscriptionRenewed;

class AutoRenewSubscription
{
    /**
     * Auto-renews the subscription.
    *
    * @param SubscriptionRenewed $event
    * @return void
    */
    public function handle(SubscriptionRenewed $event) {
        // Do something with the subscription
        
    }
}