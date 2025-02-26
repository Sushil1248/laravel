<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\Device;
use App\Models\PasswordReset;
use App\Models\State;
use App\Models\User;
use App\Models\UserVehicles;
use App\Traits\AutoResponderTrait;
use App\Traits\SendResponseTrait;use Carbon\Carbon;use Config;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Passport\Token;

class ApiController extends Controller
{
    use SendResponseTrait, AutoResponderTrait;

    public function activateDevice(Request $request)
    {
        $device_code = $request->activation_code;
        $device_token = $request->has('device_token') ? $request->device_token : null;
        $device_exist = Device::where('device_activation_code', $device_code)->first();
        if (($device_exist)) {
            $is_activated = Device::where('device_activation_code', $device_code)->pluck('is_activate')->first();
            $user_id = $device_exist->user_id;

            $user_exists = User::where('id', $user_id)
                ->where('deleted_at', null)
                ->where('status', 1)
                ->exists();
            if (!$user_exists) {
                return $this->apiResponse('error', '404', "Oops! user might be deleted or is inactive");
            }

            if (!is_null($device_token)) {
                Device::where('device_activation_code', $device_code)->update(['device_token' => $device_token, 'device_platform' => "android"]);
            }
            $user_data = User::with('device_data')->where('id', $user_id)->get(['first_name', 'last_name', 'email', 'status'])->toArray();
            $user_devices = Device::where('user_id', $user_id)->select(['device_token', 'is_activate'])->get();

            $user_data[0]['device_data'] = $user_devices;
            unset($user_data[0]['user_detail']);
            unset($user_data[0]['status_label']);
            unset($user_data[0]['profile_completed']);
            unset($user_data[0]['full_name']);
            if ($is_activated) {
                return $this->apiResponse('success', '200', 'Your device has been activated successfully', $user_data);
            }

            $activation_sent = Device::where('device_activation_code', $device_code)->update(['activation_request_sent' => 1, 'is_activate' => 1, 'device_platform' => "android"]);
            $user_devices = Device::where('user_id', $user_id)->select(['device_token', 'is_activate'])->get();

            $user_data[0]['device_data'] = $user_devices;
            return $this->apiResponse('success', '200', 'Your device has been activated successfully', $user_data);
        } else {
            return $this->apiResponse('error', '404', "Incorrect Activation Code");
        }
    }

    public function deactivateDevice(Request $request)
    {
        $device_code = $request->activation_code;
        $device_exist = Device::where('device_activation_code', $device_code)->first();
        if ($device_exist) {
            $deactivation_sent = Device::where('device_activation_code', $device_code)->update(['is_activate' => 0]);
            return $this->apiResponse('success', '200', 'Your device has been deactivated successfully', [], false);
        } else {
            return $this->apiResponse('error', '404', "Incorrect Activation Code");
        }
    }

// ========================================= AUTHENTICATION FUNCTIONS ===========================================
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users',
            'password' => 'required|string|min:6',
        ], [
            'email.required' => 'We need to know your email address',
            'email.email' => 'Provide a an valid email address',
            'password.required' => 'You can not left password empty.',
            'password.string' => 'Password field must be a string.',
            'email.exists' => 'Email does not exist',
        ]);
        if ($validator->fails()) {
            return $this->apiResponse('error', '422', $validator->errors()->all()[0], null, false);
        }
        try {
            $user = User::where('email', $request->email)->first();

            if (!Hash::check($request->password, $user->password)) {
                return $this->apiResponse('error', '404', "Password is inavlid.");
            }

            if (!$user->status) {
                return $this->apiResponse('error', '404', "Hey, We found that your account is deactivated by the admin! To activate your account, contact the admin!");
            }

            // if( !$user->email_verified_at )
            //     return $this->apiResponse('error', '404', "Email not verified yet.",['is_verified'=>false]);
            $role = $user->getRoleNames()->first();

            if (stripos(strtolower($role), 'driver') == false) {
                return $this->apiResponse('error', '404', "Hey, We found that your account is not permitted for the application!");
            }

            $login_log = [];
            $login_log = [
                'user_id' => $user->id,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'last_login' => Carbon::now()->toDateTimeString(),
            ];

            $log_response = makeCurlRequest(env('USER_LOGS_POST_URL'), 'POST', $login_log);
            if ($request->has('device_token')) {
                $device_platform = request()->has('device_platform') ? request()->get('device_platform') : "android";
                $user->update(['device_token' => $request->device_token, 'device_platform' => $device_platform]);
            }
            $user->tokens()->get()->each(function (Token $token) {
                $token->delete();
            });
            $token = $user->createToken('login')->accessToken;
            // $role = $user->role;
            // dd($role);
            return $this->apiResponse('success', '200', 'Login successfully', [
                'token' => $token,
                'gender' => $user->user_detail->gender,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'email' => $user->email,
                'id' => $user->id,
            ]);

        } catch (\Exception$e) {
            return $this->apiResponse('error', '404', $e->getMessage());
        }
    }

    public function logout($user = null)
    {
        try {
            if (is_null($user)) {
                $user = Auth::user();
            }

            if ($user) {
                $user->tokens->each(function ($token, $key) {
                    $token->delete();
                });
            }
            // $logout_log = [
            //     'user_id' =>  $user->id,
            //     'logged_out_at'=>Carbon::now()->toDateTimeString()
            // ];
            return $this->apiResponse('success', '200', 'Logout successfully', null, $want_status = false);
        } catch (\Exception$e) {
            return $this->apiResponse('error', '404', $e->getMessage());
        }
    }

    public function passwordResetLink(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users',

        ], [
            'email.required' => 'We need to know your email address',
            'email.email' => 'Provide a an valid email address',
        ]);
        if ($validator->fails()) {
            return $this->apiResponse('error', '422', $validator->errors()->all()[0], null, false);
        }
        try {
            $user = User::where('email', $request->email)->first();
            //check user existance
            if (!$user) {
                return $this->apiResponse('error', '404', config('constants.ERROR.NOT_VALID_EMAIL'));
            }

            //Specifying templet to send
            $template = $this->get_template_by_name('FORGOT_PASSWORD');
            PasswordReset::where('email', $user->email)->where('type', 'password-reset')->delete();
            //Creating token email specifically
            $passwordReset = PasswordReset::updateOrCreate(['email' => $user->email, 'type' => 'password-reset'], ['token' => rand(100000, 999999)]);
            $otp = $passwordReset->token;
            $stringToReplace = ['{{$name}}', '{{$token}}'];
            $stringReplaceWith = [$user->full_name, $otp];
            $newval = str_replace($stringToReplace, $stringReplaceWith, $template->template);
            //mail logs
            $result = $this->send_mail($user->email, $template->subject, $newval);
            if ($result) {
                return $this->apiResponse('success', '200', 'OTP sent on email.', null, false);
            }

            return $this->apiResponse('error', '404', 'Unable to send email.', null, false);
        } catch (\Exception$e) {
            return $this->apiResponse('error', '404', $e->getMessage());
        }
    }

    public function updateNewPassword(Request $request)
    {
        //validate incoming request
        $rules = [
            'email' => 'required|email|exists:users',
            'password' => 'required|string|confirmed|min:6',
            'otp' => 'required',
        ];
        $messages = [
            'email.required' => 'We need to know your email address',
            'email.email' => 'Provide a an valid email address',
            'password.required' => 'Password is required',
            'password.confirmed' => 'Confirmed password not matched with password',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return $this->apiResponse('error', '422', $validator->errors()->all()[0], null, false);
        }
        try {
            $passwordReset = PasswordReset::where('email', $request->email)->where('type', 'password-reset')->first();
            if (!$passwordReset) {
                return $this->apiResponse('error', '404', "Please verify OTP first.");
            }

            if ($passwordReset->token != $request->otp) {
                return $this->apiResponse('error', '404', 'Provided OTP is invalid.');
            }

            $record = User::where('email', $request->email)->update([
                'password' => Hash::make($request->password),
            ]);
            PasswordReset::where('email', $passwordReset->email)->where('type', 'password-reset')->delete();
            return $this->apiResponse('success', '200', 'Password updated sucessfully.');

            return $this->loginMethod($passwordReset->email, $request->password);
        } catch (\Exception$e) {
            return $this->apiResponse('error', '404', $e->getMessage());
        }
    }
// ========================================= USER CRUD FUNCTIONS ===========================================
    public function profile()
    {
        try {
            $user = Auth::user();
            $userData = $user->toArray();
            unset($userData['user_detail']);
            $userDetail = $user->user_detail;
            $userDevices = $user->devices;
            $devices = $userDevices->map(function ($device) {
                return collect($device->toArray())
                    ->only(['id', 'device_name', 'device_token', 'tracking_radius'])
                    ->all();
            });

            $userData['phone_number'] = $userDetail ? $userDetail->phone_number : '';
            $userData['gender'] = $userDetail ? $userDetail->gender : '';
            $userData['address'] = $userDetail ? $userDetail->address : '';
            $userData['fax'] = $userDetail ? $userDetail->fax : '';
            $userData['zip_code'] = $userDetail ? $userDetail->zip_code : '';
            $userData['state_id'] = $userDetail ? ($userDetail->state_id) : null;
            $userData['country_id'] = $userDetail ? ($userDetail->country_id) : null;
            $userData['country'] = [
                'id' => $userDetail->country ? ($userDetail->country->id) : '',
                'name' => $userDetail->country ? $userDetail->country->name : '',
            ];
            $userData['state'] = [
                'id' => $userDetail->state ? ($userDetail->state->id) : '',
                'name' => $userDetail->state ? $userDetail->state->name : '',
            ];
            $userData['city'] = [
                'id' => $userDetail->city ? ($userDetail->city->id) : '',
                'name' => $userDetail->city ? $userDetail->city->name : '',
            ];
            $userData['dob'] = $userDetail ? $userDetail->dob : null;
            $userData['profile_picture'] = $userDetail ? $userDetail->profile_picture : null;
            $userData['devices'] = $devices;

            return $this->apiResponse('success', '200', 'User profile ', $userData);
        } catch (\Exception$e) {
            return $this->apiResponse('error', '404', $e->getMessage());
        }
    }

    public function updateProfile(Request $request)
    {
        $validations = [
            'first_name' => 'required',
            'profile_image' => 'file|mimetypes:image/*|max:' . config('constants.MAXIMUM_UPLOAD') * 1024,
            'cover_image' => 'file|mimetypes:image/*|max:' . config('constants.MAXIMUM_UPLOAD') * 1024,
            // 'dob' =>  'required|date'
        ];
        if ($request->from_profile) {
            unset($validations['first_name'], $validations['profile_image'], $validations['dob']);
        }

        $validator = Validator::make($request->all(), $validations);

        if ($validator->fails()) {
            return $this->apiResponse('error', '422', $validator->errors()->all()[0], null, false);
        }
        try {
            $inputs = removeEmptyElements($request->all());

            if (!$request->from_profile) {
                $user = [
                    'first_name' => $request->first_name,
                ];
                Auth::user()->update($user);
            }
            if ($request->filled('country')) {
                $inputs['country_id'] = jsdecode_userdata($request->country);
            }

            if ($request->hasFile('profile_image')) {
                $inputs['profile_picture'] = str_replace("public/", "", $request->profile_image->store('public/user-profile-picture'));
            }

            if ($request->hasFile('cover_image')) {
                $inputs['cover_image'] = str_replace("public/", "", $request->cover_image->store('public/user-cover-image'));
            }

            Auth::user()->user_detail->update($inputs);
            return $this->apiResponse('success', '200', 'User profile updated successfully.', (array) $this->getUser($request)->getData()->data);
        } catch (\Exception$e) {
            DB::rollBack();
            return $this->apiResponse('error', '404', $e->getMessage());
        }
    }

    public function getUser(Request $request)
    {
        $user = $request->user();
        $userDetail = $user->user_detail;
        return $this->apiResponse('success', '200', 'Profile fetched successfully.', [
            'full_name' => $user->full_name,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'email' => $user->email,
            'phone_number' => $userDetail->mobile,
            'bio' => $userDetail->bio,
            'gender' => $userDetail->gender,
            'dob' => $userDetail->dob,
            'profile_image' => $userDetail->profile_picture,
            'cover_image' => $userDetail->cover_image,
            'country' => [
                'id' => $userDetail->country ? jsencode_userdata($userDetail->country->id) : '',
                'name' => $userDetail->country ? $userDetail->country->name : '',
            ],
            'state' => [
                'id' => $userDetail->state ? ($userDetail->state->id) : '',
                'name' => $userDetail->state ? $userDetail->state->name : '',
            ],
            'city' => [
                'id' => $userDetail->city ? ($userDetail->city->id) : '',
                'name' => $userDetail->city ? $userDetail->city->name : '',
            ],
        ]);
    }
// ========================================= OTHER ATTRIBUTE FUNCTIONS===========================================

    public function getCountries()
    {
        $countries = Country::pluck('name', 'id');
        $response_countries = [];
        foreach ($countries as $id => $name) {
            $response_countries[] = [
                'id' => ($id),
                'name' => $name,
            ];
        }

        return $this->apiResponse('success', '200', 'Countries fetched successfully.', [
            'countries' => $response_countries,
        ]);
    }

    public function getStates(Country $country)
    {
        $states = $country->states->pluck('id', 'name');
        $response_states = [];
        foreach ($states as $name => $id) {
            $response_states[] = [
                'id' => ($id),
                'name' => $name,
            ];
        }

        return $this->apiResponse('success', '200', 'States fetched successfully.', [
            'states' => $response_states,
        ]);
    }

    public function getCities(State $state)
    {
        $cities = $state->cities->pluck('id', 'city_name');
        $response_cities = [];
        foreach ($cities as $city_name => $id) {
            $response_cities[] = [
                'id' => ($id),
                'name' => $city_name,
            ];
        }

        return $this->apiResponse('success', '200', 'cities fetched successfully.', [
            'cities' => $response_cities,
        ]);
    }

//======================================== Device Functions ============================================
    public function getDevices(Request $request)
    {
        $user = $request->user();
        $userDevices = $user->devices;
        $devices = $userDevices->map(function ($device) {
            return collect($device->toArray())
                ->only(['id', 'device_name', 'device_token', 'tracking_radius'])
                ->all();
        });
        return $devices;
    }

    public function device($device)
    {
        $deviceDetails = Device::where('id', $device)->select(['id', 'device_name', 'device_token', 'tracking_radius'])->first();
        return $deviceDetails;
    }

    public function updateDeviceDetails($device_id, Request $request)
    {
        $validations = [
            'device_name' => 'required',
            'tracking_radius' => 'numeric',
            'user_id' => 'required',
        ];

        $validator = Validator::make($request->all(), $validations);

        if ($validator->fails()) {
            return $this->apiResponse('error', '422', $validator->errors()->all()[0], null, false);
        }
        try {

            $device = [
                'device_name' => $request->device_name,
                'device_token' => $request->has('device_token') ? $request->device_token : null,
                'tracking_radius' => $request->tracking_radius,
            ];

            $activated = Device::where('id', $device_id)->select(['status', 'is_activate', 'user_id'])->first();
            if (!$activated['status']) {
                return $this->apiResponse('error', '404', "Oops! Device is in deactivated state", null, false);
            }
            if ($activated['user_id'] != $request->user_id) {
                return $this->apiResponse('error', '404', "Oops! Device does not belongs to you", null, false);
            }
            $update_device = Device::where('id', $device_id)->update($device);
            return $this->apiResponse('success', '200', 'Device updated successfully.', $this->device((int) $device_id)->toArray());

        } catch (\Exception$e) {
            DB::rollBack();
            return $this->apiResponse('error', '404', $e->getMessage());
        }
    }

    public function getUsersVehicles(Request $request)
    {
        try {
            $vehicles = UserVehicles::with('vehicle')->where('user_id', auth()->user()->id)->get()->pluck('vehicle');
            $current_vehicle = UserVehicles::with('vehicle')->where(['user_id'=> auth()->user()->id, 'ride_status' =>1])->get()->pluck('vehicle');

           if(is_null($vehicles) || empty($vehicles) || !count($vehicles) > 0){
            return $this->apiResponse('success', '200', 'No vehicles found!',null, false);
           }

            return $this->apiResponse('success', '200', 'Vehicles fetched successfuly!', [
                'assigned_vehicles' => $vehicles->toArray(),
                'curent_vehicle'    => $current_vehicle->toArray()
            ], false);

        } catch (\Exception$e) {
            return $this->apiResponse('error', '404', $e->getMessage());
        }

    }

    public function makeVehicleActive($id)
    {
        try {
            // Find the user_vehicle by vehicle ID and user ID
            $userId = auth()->user()->id;
            $userVehicle = UserVehicles::where('user_id', $userId)->where('vehicle_id', $id)->firstOrFail();

            $vehicles = UserVehicles::with('vehicle')
            ->where('user_id', auth()->user()->id)
            ->get()
            ->map(function ($userVehicle) {
                return [
                    'vehicle' => $userVehicle->vehicle,
                    'ride_status' => $userVehicle->ride_status
                ];
            })
            ->toArray();


            // Check if the vehicle is already active
            if ($userVehicle->ride_status) {
                return $this->apiResponse('success', '200', 'Vehicles ride already activated!', $vehicles);
            }

            // Deactivate all other vehicles for this user

            UserVehicles::where('user_id', $userId)->update(['ride_status' => 0]);

            // Activate the selected vehicle
            $userVehicle->ride_status = 1;
            $userVehicle->save();
            $vehicles = UserVehicles::with('vehicle')
            ->where('user_id', auth()->user()->id)
            ->get()
            ->map(function ($userVehicle) {
                return [
                    'vehicle' => $userVehicle->vehicle,
                    'ride_status' => $userVehicle->ride_status
                ];
            })
            ->toArray();
            return $this->apiResponse('success', '200', 'Vehicles ride activated successfuly!', $vehicles);
        } catch (\Exception$e) {
            return $this->apiResponse('error', '404', $e->getMessage());
        }
    }

}
