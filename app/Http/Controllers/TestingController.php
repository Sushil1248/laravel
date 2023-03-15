<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Response;
use Imdhemy\AppStore\ClientFactory;
use Imdhemy\AppStore\Exceptions\InvalidReceiptException;
use Imdhemy\Purchases\Facades\Subscription;
use JsonException;
use App\Notifications\CommonNotification;
use App\Models\{ForumAnswer,ForumAnswerLike,User};

class TestingController extends Controller
{
    //
    public function testing(){

        // Create the expected body
        $responseBody = [
            'environment' => 'Sandbox',
            'status' => 0,
            'latest_receipt_info' => [
                [
                    'product_id' => 'fake_product_id',
                    'quantity' => '1',
                    'transaction_id' => 'fake_transaction_id',
                    // other fields omitted
                ],
            ],
            // other fields omitted
        ];

        // Create the response instance. It requires to JSON encode the body.
        $responseMock = new Response(200, [], json_encode($responseBody, JSON_THROW_ON_ERROR));

        // Use the client factory to mock the response.
        $client = ClientFactory::mock($responseMock);

        // --------------------------------------------------------------
        // The created client could be injected into a service
        // --------------------------------------------------------------
        // The part is up to you as a developer.
        //
        // Inside that service you can use the client as follows
        $verifyResponse = Subscription::appStore($client)->receiptData('fake_receipt_data')->verifyReceipt();
        // The returned response will contain the data from the response body you provided in the first line.

        dd( $verifyResponse );
    }

    public function notificationTesting(){
        // On answer
        $answer = ForumAnswer::find(1);
        $answer->forumQuestion->user->notify( new CommonNotification("answered_forum",$answer) );

        // On Reply
        $answer = ForumAnswer::find(3);
        $answer->forumAnswer->user->notify( new CommonNotification("replied_answer",$answer) );

        // on link
        $like = ForumAnswerLike::find( 1 );
        $like->forumAnswer->user->notify( new CommonNotification("liked_answer",$like) );

        User::find(2)->notify( new CommonNotification("This is from admin") );
    }
}
