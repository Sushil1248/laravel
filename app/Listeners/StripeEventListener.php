<?php
 
namespace App\Listeners;
 
use Laravel\Cashier\Events\WebhookReceived;
use Illuminate\Support\Facades\Log;
use App\Models\{User,Payment};
use Laravel\Cashier\Subscription;

class StripeEventListener
{
    /**
     * Handle received Stripe webhooks.
     *
     * @param  \Laravel\Cashier\Events\WebhookReceived  $event
     * @return void
     */
    public function handle(WebhookReceived $event)
    {
        try{
            if ($event->payload['type'] === 'invoice.payment_succeeded') {
                $stripeResponse = $event->payload['data']['object'];
                if( !empty($stripeResponse['subscription']) && ($subscription = Subscription::where('stripe_id',$stripeResponse['subscription'])->first() ) ){
                    Log::channel('custom')->info('<pre>' . print_r($subscription->toArray(),1) . '</pre>');
                    Payment::create([
                        'subscription_id'   =>  $subscription->id,
                        'amount_paid'       =>  $stripeResponse['amount_paid'] / 100,
                        'status'            =>  $stripeResponse['status'],
                        'raw_response'      =>  $event
                    ]);
                }
            }
        }catch(\Exception $e){
            Log::channel('custom')->info("<br><hr>ERROR --- " . $e->getMessage());
        }
        
    }
}