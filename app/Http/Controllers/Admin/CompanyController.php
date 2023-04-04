<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use App\Models\{Company, User, Role, CompanyDetail,UserDetails, State,Country, CompanyUsers, Vehicle};
use Spatie\Permission\Models\Permission;
use App\Exports\CompaniesExport;
use Illuminate\Support\Facades\DB;
use Auth;
use Illuminate\Support\Str;
class CompanyController extends Controller
{
    function __construct()
    {
        // $this->middleware("permission:user-list|user-create|user-edit|user-delete|user-view", ['only' => ['getList']]);
        // $this->middleware("permission:user-create", ['only' => ['add']]);
        // $this->middleware("permission:user-update-password", ['only' => ['updatePassword']]);
        // $this->middleware("permission:user-edit", ['only' => ['edit']]);
        // $this->middleware("permission:user-delete", ['only' => ['del_record']]);
        // $this->middleware("permission:user-view", ['only' => ['view_detail']]);
    }

    /*
    Method Name:    getList
    Developer:      Sushil
    Purpose:        To get list of all companies
    Params:
    */
    public function getList(Request $request , $deleted=""){
        $start = $end = "";
        if( $request->filled('daterange_filter') ) {
            $daterange = $request->daterange_filter;
            $daterang = explode(' - ',$daterange);
            $start = $daterang[0].' 00:00:00';
            $end = $daterang[1].' 23:05:59';
        }

        $data = User::whereHas('roles', function ($query) {
            $query->whereIn('name', ['1_Company', 'PropertyManager']);
        })
        ->when(!empty($start) && !empty($end), function ($q, $from) use ($start, $end) {
            $q->whereBetween('users.created_at', [$start, $end]);
        })
        ->when($request->search, function ($qu, $keyword) {
            $qu->where(function ($q) use ($keyword) {

                $q->where('users.first_name', 'like', '%' . $keyword . '%')
                    // ->orWhere('company_name', 'like', '%' . $keyword . '%')
                    ->orWhere('users.last_name', 'like', '%' . $keyword . '%')
                    ->orWhere('users.email', 'like', '%' . $keyword . '%')
                    ->orWhere('users.id', $keyword);

                    // check fields from CompanyDetails table
                    $q->orWhereHas('company_detail', function($qu) use($keyword) {
                          $qu->where('company_name', 'like', '%' . $keyword . '%');

                     });
            });
        })
        ->when($request->filled('status'), function ($qu) {
            $qu->where('users.status', request('status'));
        })
        ->when(jsdecode_userdata($request->user_id), function ($query, $user_id) {
            $query->where('users.id', $user_id);
        })
        ->where('users.id', '<>', Auth::id());

        $deletedCompanies = (clone $data)->onlyTrashed()->sortable(['id' => 'desc'])
        ->paginate(Config::get('constants.PAGINATION_NUMBER'),'*','dpage');
        $data = $data->sortable(['id' => 'desc'])->paginate(Config::get('constants.PAGINATION_NUMBER'));
        $country = Country::pluck('name','id');
        return view('admin.company.list', compact('data','deleted','country','deletedCompanies'));
    }
    /* End Method getList */

    /*
    Method Name:    del_record
    Developer:      Sushil
    Purpose:        To delete any user by id
    Params:         [id]
    */
    public function del_record($id){
        try {
            $comapnyId = jsdecode_userdata($id);
            if(count(CompanyUsers::where('company_id',$comapnyId)->get()) > 0){
                return redirect()->back()->with('status', 'error')->with('message', "Oops, it seems user (s) are associated with this company, Please delete the users first.");
            }
            User::where('id',$comapnyId)->delete();
        	return redirect()->back()->with('status', 'success')->with('message', 'Company details '.Config::get('constants.SUCCESS.DELETE_DONE'));
        } catch(Exception $ex) {
            return redirect()->back()->with('status', 'error')->with('message', $ex->getMessage());
        }
    }
    /* End Method del_record */

    /*
    Method Name:    del_restore
    Developer:      Sushil
    Purpose:        To restore deleted user by id
    Params:         [id]
    */
    public function del_restore($id){
        try {
            $userId = jsdecode_userdata($id);
            User::where('id',$userId)->restore();
        	return redirect()->back()->with('status', 'success')->with('message', 'Company '.Config::get('constants.SUCCESS.RESTORE_DONE'));
        } catch(Exception $ex) {
            return redirect()->back()->with('status', 'error')->with('message', $ex->getMessage());
        }
    }
    /* End Method del_restore */

    /*
    Method Name:    edit_form
    Developer:      Shiv K. Agg
    Purpose:        Form to update user details
    Params:         [id]
    */
    public function edit($id , Request $request){

        if ($request->isMethod('get')){
            $companyId = jsdecode_userdata($id);

            $companyDetail = User::with('company_detail')->find($companyId);
            $states = State::where('country_id',1)->get();
            if(!$companyDetail)
                return redirect()->back()->with('status', 'error')->with('message', "Something went wrong");
            $country = Country::pluck('name','id');
            return view('admin.company.edit',compact('companyDetail','states','country'));

        }else{
            $companyId = jsdecode_userdata($id);

            $request->validate([
                'company_name' => 'required|string|max:100',
                'company_email' => 'required|unique:users,email,'.$companyId,
                'address' => '',
                'contact_number' => 'nullable|numeric',
                'city' => '',
                'state' => '',
                'establish_date'       =>  'date'
            ]);

            try {
                $status = 0;
                if($request->status == "on") {
                    $status = 1;
                }
                $company = User::findOrFail($companyId);
                $company->email = $request->company_email;
                $company->status = $request->input('status',0);
                $company->save();

                $comapny_detail = CompanyDetail::updateOrCreate(['user_id' =>  $companyId],removeEmptyElements([
                    'address'   =>  $request->address,
                    'city_id'   =>  jsdecode_userdata($request->city),
                    'state_id'  =>  jsdecode_userdata($request->state),
                    'country_id'=>  jsdecode_userdata($request->country),
                    'zipcode'   =>  $request->zipcode,
                    'company_name'   =>  $request->company_name,
                    'contact_person'   =>  $request->contact_person,
                    'contact_person_email'   =>  $request->contact_person_email,
                    'contact_number'   =>  $request->contact_number,
                    'zipcode'   =>  $request->zipcode,
                    'fax_no'   =>  $request->fax_no,
                    'establish_date'   =>  $request->establish_date,
                    'gender' => $request->gender
                ]));

                if(auth()->user()->hasRole('1_Company')){
                    return response()->json(["success" => true, "msg" => "Details " . Config::get('constants.SUCCESS.UPDATE_DONE') ], 200);
                }
                return redirect()->back()->with('status', 'success')->with('status', 'success')->with('message', 'Comapny details '.Config::get('constants.SUCCESS.UPDATE_DONE'));

            } catch ( \Exception $e ) {
                return redirect()->back()->withInput()->with('status', 'error')->with('message', $e->getMessage());
            }
        }
    }
    /* End Method edit_form */


     /*
     Method Name:    add_form
     Developer:      Sushil
     Created Date:   2022-06-23 (yyyy-mm-dd)
     Purpose:        Form to add user details
    Params:         [id]
     */
    public function add(Request $request ){
        if ($request->isMethod('get')){
            $roles = Role::whereNotIn( 'name' , ['Customer','Administrator'] );
            /** When company user is login **/
            if( Auth::user()->hasRole(['HR','Employee','Company']) )
                $roles->whereIn('name',['HR','Employee']);

            $roles = $roles->get();
            $states = State::where('country_id',1)->get();
            $companies = Role::findByName('1_Company')->users()->pluck('company_name', 'id');
            return view('admin.company.add',compact('roles','companies','states'));
        }else{
            $validationRules = [
                'company_name' => 'required|string|max:100',
                'company_email' => 'required|unique:users,email',
                'address' => '',
                'contact_number' => 'nullable|numeric',
                'contact_person' => 'required',
                'city' => '',
                'state' => '',
                'establish_date'       =>  'date'
            ];
            $request->validate($validationRules, [
                'password.required' => 'Password is required'
            ]);

            try {
                $data = [
                    'email' =>$request->company_email,
                    'unique_id'    => Str::random(6),
                    'password' => bcrypt($request->password),
                    'status' => 1
                ];
                DB::beginTransaction();

                $company = User::create($data);
                $company->assignRole($request->role);

                if($company){

                    $role_data = array(
                        'name' =>  $company->id.'_Driver',
                        'guard_name' =>'web',
                        'created_by'=>$company->id
                    );
                    $record = Role::create($role_data);
                    if ($record) {
                        $record->syncPermissions($request->permission);
                    }
                    $details = [
                        'user_id' => $company->id,
                        'contact_person' =>$request->contact_person,
                        'contact_person_email' =>$request->contact_person_email,
                        'contact_number' =>$request->contact_number,
                        'company_name'   => $request->company_name,
                        'address' => $request->address,
                        'country_id'    =>  jsdecode_userdata($request->country),
                        'city_id' => jsdecode_userdata($request->city),
                        'state_id' => jsdecode_userdata($request->state),
                        'zipcode' => $request->zipcode,
                    ];
                    $result = CompanyDetail::create( removeEmptyElements($details) );

                    if($result) {
                        DB::commit();
                        return [
                            'success'    =>  true,
                            'msg'       =>  'Company created successfully.'
                        ];
                    }
                }
                DB::rollBack();
                return [
                    'success'    =>  false,
                    'msg'       =>  Config::get('constants.ERROR.OOPS_ERROR')
                ];
            } catch ( \Exception $e ) {
                DB::rollBack();
                return [
                    'success'    =>  false,
                    'msg'       =>      $e->getMessage()
                ];
            }
        }
    }

    /*
    Method Name:   updatePassword
    Developer:     Shiv K. Agg
    Created Date:  2022-07-20 (yyyy-mm-dd)
    Purpose:       Form to add user details
    Params:         [id]
    */
    public function updatePassword(Request $request  , $id){
        if ($request->isMethod('get')){
            return view('admin.user.password',compact('userType','id') );
        }
        $request->validate([
            'password' => 'required|confirmed|min:6'
        ]);
        $userId = jsdecode_userdata($id);
        Company::where('id',$userId)->update([
            'password'  =>  bcrypt($request->password)
        ]);


        return redirect()->back()->with('status', 'success')->with('message', 'User password '.Config::get('constants.SUCCESS.UPDATE_DONE'));
    }

    /*
    Method Name:    view_detail
    Developer:      Shiv K. Agg
    Purpose:        To get detail of users
    Params:         [id]
    */
    public function view_detail($id,Request $request){
        $userId = jsdecode_userdata($id);
        $companyDetail = User::withTrashed()->find($userId);

        $response = [
            'company_name'    =>  $companyDetail->company_detail->company_name,
            'contact_person'     =>  $companyDetail->company_detail->contact_person,
            'contact_person_email'     =>  $companyDetail->company_detail->contact_person_email,
            'company_email'         =>  $companyDetail->email,
            'contact_number'        =>  $companyDetail ? $companyDetail->company_detail->contact_number : '',
            'address'        =>  $companyDetail->company_detail ? $companyDetail->company_detail->address : '',
            'country'        =>  $companyDetail->company_detail && $companyDetail->company_detail->country ? $companyDetail->company_detail->country->name : '',
            'state'        =>  $companyDetail->company_detail && $companyDetail->company_detail->state ? $companyDetail->company_detail->state->name : '',
            'city'        =>  $companyDetail->company_detail && $companyDetail->company_detail->city ? $companyDetail->company_detail->city->city_name : '',
            'edit_user'     =>  route('company.edit',['id'=>jsencode_userdata($companyDetail->id)]),
        ];
        return [
            'status'    =>  'true',
            'data'      =>  $response
        ];
        if(!$userDetail)
            return redirect()->back();
        return view('admin.company.view_detail',compact('userDetail','userType'));
    }
    /* End Method view_detail */

    public function changeStatus(Request $request)
    {
        try {
            $company = User::withTrashed()->find( jsdecode_userdata($request->id) );
            $company->status = $request->status;
            $company->save();

            return response()->json(['success' => 'Status change successfully.','message' => 'Company '.Config::get('constants.SUCCESS.STATUS_UPDATE')]);
        } catch ( \Exception $e ) {
            return redirect()->back()->with('status', 'error')->with('message', $e->getMessage());
        }
    }

    public function export(){
        return \Excel::download(new CompaniesExport , 'company.xlsx'  );
    }

    // Manage User Inside Company
    public function getUserList(Request $request, $deleted = "")
    {
        $start = $end = "";
        if ($request->filled('daterange_filter')) {
            $daterange = $request->daterange_filter;
            $daterang = explode(' - ', $daterange);
            $start = $daterang[0] . ' 00:00:00';
            $end = $daterang[1] . ' 23:05:59';
        }
        $companyId = Auth::user()->id;
        $user_ids = User::whereHas('companyUsers', function ($query) use ($companyId) {
            $query->where('company_id', $companyId);
        })->where('id', '<>', Auth::id())->get()->pluck('id', 'first_name');


        $data = User::whereHas('companyUsers', function ($query) use ($companyId, $user_ids) {
            $query->where('company_id', $companyId);
            $query->orWhereIn('company_id',$user_ids);
        })
        ->when(!empty($start) && !empty($end), function ($q, $from) use ($start, $end) {
            $q->whereBetween('created_at', [$start, $end]);
        })
        ->when($request->search, function ($qu, $keyword) {
            $qu->where(function ($q) use ($keyword) {
                $q->where('first_name', 'like', '%' . $keyword . '%')
                    ->orWhere('last_name', 'like', '%' . $keyword . '%')
                    ->orWhere('email', 'like', '%' . $keyword . '%')
                    ->orWhere('id', $keyword);
            });
        })
        ->when($request->filled('status'), function ($qu) {
            $qu->where('status', request('status'));
        })
        ->when(jsdecode_userdata($request->user_id), function ($query, $user_id) {
            $query->where('id', $user_id);
        })
        ->where('id', '<>', Auth::id());

        $deletedUsers = (clone $data)->onlyTrashed()->sortable(['id' => 'desc'])
            ->paginate(Config::get('constants.PAGINATION_NUMBER'), '*', 'dpage');
        $data = $data->sortable(['id' => 'desc'])->paginate(Config::get('constants.PAGINATION_NUMBER'));
        $country = Country::pluck('name', 'id');

        $user_ids = [];
        $user_ids = User::whereHas('companyUsers', function ($query) use ($companyId) {
            $query->where('company_id', $companyId);
        })->with('vehicles')
        ->where('id', '<>', Auth::id())->get()->pluck('id')->toArray();

        $role = Auth::user()->getRoleNames()->first();

        if (stripos(strtolower($role), 'company') === false) {
            $companyIds = Auth::user()->companyUsers()->pluck('company_id')->first();
            array_push($user_ids, $companyIds);
        }

        $role = Role::where('created_by', Auth::user()->id)
                ->orWhereIn('created_by', $user_ids)
                ->pluck('name', 'id');

        $vehicles=[];


        $vehicles = Vehicle::where(function ($query) use ($user_ids) {
            $query->where('user_id', Auth::user()->id)
                ->orWhereIn('user_id', $user_ids);
        })->get();


        if(! auth()->user()->hasRole('Administrator')){
            $company = User::where('id', Auth()->user()->id)
                    ->with('company_detail:id,user_id,company_name')
                    ->get()
                    ->pluck('company_detail.company_name', 'id');
        }else{
            $company = Role::findByName('1_Company')
                        ->users()
                        ->active()
                        ->with('company_detail:id,user_id,company_name')
                        ->get()
                        ->pluck('company_detail.company_name', 'company_detail.user_id');
        }

        return view('admin.user.list', compact('data', 'deleted', 'country', 'company', 'deletedUsers','vehicles','role'));
    }
}
