<?php

namespace App\Traits;

use Tymon\JWTAuth\Facades\JWTAuth;
use Auth;
use Illuminate\Support\MessageBag;

trait SendResponseTrait {

    public function apiResponse($apiResponse, $statusCode = '404', $message = 'No records Found', $data = [], $want_status=true) {
        $responseArray = [];
        $otherDetail = [];
        if( $data instanceof MessageBag )
            $data = $data->messages();
        if( Auth::check() )
            $otherDetail = ['is_active' => Auth::user()->status ? true : false];
        //$otherDetail = ['header'=>getallheaders()];
        if($apiResponse == 'success') {
            $responseArray['status'] = true;
            $responseArray['message'] = $message;
            $responseArray['data'] = $want_status ? array_merge($data,$otherDetail) : $data;
        } else {
            $responseArray['status'] = false;
            $responseArray['message'] = $message;
            $responseArray['data'] = $want_status ? array_merge($data,$otherDetail) : $data;
        }
        return response()->json($responseArray);
    }

    public function generateOtp(){
        $otp = rand(1000,9999);
        return $otp;
    }
    public function sendOtp($phone, $otp){
        $curl = curl_init();
        curl_setopt_array($curl, array(
        CURLOPT_URL => "http://api.greenweb.com.bd/api.php?token=a3b50fff7d7c6cd752d8140f5badfdb3&to=".$phone."&message=PorashonaOnline: Your code is ".$otp." App ID: FA+9qCX9VSu
        ",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => array(
            "cache-control: no-cache",
            "postman-token: ef19c99c-228c-67e7-a709-41dd60f25bd5"
        ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
        echo "cURL Error #:" . $err;
        } else {
        echo $response;
        }
    }
}
