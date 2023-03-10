<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Traits\AutoResponderTrait;
use App\Traits\SendResponseTrait;
use App\Models\{User, Device};

class ApiController extends Controller
{
    use SendResponseTrait, AutoResponderTrait;

    public function activateDevice(Request $request)
    {
        $device_code = $request->activation_code;
        $device_token = $request->has('device_token') ? $request->device_token :null;
        $device_exist = Device::where('device_activation_code', $device_code)->first();
        if (($device_exist)) {
            $is_activated = Device::where('device_activation_code', $device_code)->pluck('is_activate')->first();
            $user_id = $device_exist->user_id;
            $user_data = User::where('id', $user_id)->get()->toArray();

            if ($is_activated) {
                return $this->apiResponse('success', '200', 'Device Already activated', $user_data);
            }

            $activation_sent = Device::where('device_activation_code', $device_code)->update(['activation_request_sent' => 1, 'is_activate' =>1]);
            if(!is_null($device_token)){
                    Device::where('device_activation_code', $device_code)->update(['device_token' => $device_token]);
            }
            return $this->apiResponse('success', '200', 'Activation Request Sent successfully.', $user_data);
        } else {
            return $this->apiResponse('error', '404', "Incorrect Activation Code");
        }
    }
}
