<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use App\Models\{Company, User, Role, CompanyDetail,UserDetails, State,Country, CompanyUsers};
use Spatie\Permission\Models\Permission;
use App\Exports\CompaniesExport;
use Illuminate\Support\Facades\DB;
use Auth;

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
            $query->whereIn('name', ['Company', 'PropertyManager']);
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
        // ->when($request->has('role_filter'), function ($qu) use ($request) {
        //     $qu->whereHas('roles', function ($q) use ($request) {
        //         $q->where('name', $request->role_filter);
        //     });
        // })
        ->where('id', '<>', Auth::id());

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
            Company::where('id',$comapnyId)->delete();
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
            Company::where('id',$userId)->restore();
        	return redirect()->back()->with('status', 'success')->with('message', 'Company details '.Config::get('constants.SUCCESS.RESTORE_DONE'));
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
                return redirect()->route('company.list');
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
                $company->roles()->detach();
                $company->email = $request->company_email;
                $company->status = $request->input('status',0);
                if(auth()->user()->hasRole('Company')){
                    $company->assignRole($request->role);
                }
                $company->save();

                $comapny_detail = CompanyDetail::updateOrCreate(['user_id' =>  $companyId],removeEmptyElements([
                    'address'   =>  $request->address,
                    'city_id'   =>  jsdecode_userdata($request->city),
                    'state_id'  =>  jsdecode_userdata($request->state),
                    'country_id'=>  jsdecode_userdata($request->country),
                    'zipcode'   =>  $request->zipcode,
                    'company_name'   =>  $request->company_name,
                    'contact_person'   =>  $request->contact_person,
                    'contact_number'   =>  $request->contact_number,
                    'zipcode'   =>  $request->zipcode,
                    'fax_no'   =>  $request->fax_no,
                    'establish_date'   =>  $request->establish_date,
                    'gender' => $request->gender
                ]));

                if(auth()->user()->hasRole('Company')){
                    return response()->json(["success" => true, "msg" => "Details " . Config::get('constants.SUCCESS.UPDATE_DONE') ], 200);
                }

                return redirect()->route('company.list')->with('status', 'success')->with('message', 'Comapny details '.Config::get('constants.SUCCESS.UPDATE_DONE'));
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
            $companies = Role::findByName('company')->users()->pluck('company_name', 'id');
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
                    'password' => bcrypt($request->password),
                    'status' => 1
                ];
                DB::beginTransaction();

                $company = User::create($data);
                $company->assignRole($request->role);
                if($company){
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
        return redirect()->route('company.list')->with('status', 'success')->with('message', 'User password '.Config::get('constants.SUCCESS.UPDATE_DONE'));
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
            return redirect()->route('company.list');
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
        $data = User::whereHas('companyUsers', function ($query) use ($companyId) {
            $query->where('company_id', $companyId);
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
        if(auth()->user()->hasRole('Company')){
            $company = User::where('id', Auth()->user()->id)
                    ->with('company_detail:id,user_id,company_name')
                    ->get()
                    ->pluck('company_detail.company_name', 'id');
        }else{
            $company = Role::findByName('company')
                        ->users()
                        ->active()
                        ->with('company_detail:id,user_id,company_name')
                        ->get()
                        ->pluck('company_detail.company_name', 'company_detail.user_id');
        }
        return view('admin.user.list', compact('data', 'deleted', 'country', 'company', 'deletedUsers'));
    }
}
