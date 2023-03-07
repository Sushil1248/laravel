<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use DB;

class RoleController extends Controller
{
    function __construct()
    {
         $this->middleware('permission:role-list|role-create|role-edit|role-delete', ['only' => ['getList']]);
         $this->middleware('permission:role-create', ['only' => ['add_form','create_record']]);
         $this->middleware('permission:role-edit', ['only' => ['edit_form','update_record']]);
         $this->middleware('permission:role-delete', ['only' => ['del_record']]);
    }

    public function getList(Request $request){
        $keyword = '';
 
        if($request->has('search_keyword') && $request->search_keyword != '') {
            $keyword = $request->search_keyword;
        }

        $data = Role::when($request->search_keyword, function($q) use($request){
            $q->where('name', 'like', '%'.$request->search_keyword.'%');
        })->whereNotIn( 'name',['Administrator'])->paginate(Config::get('constants.PAGINATION_NUMBER'));
        return view('admin.roles.list', compact('data','keyword'));
    }

    public function add_form(){
        $permission = Permission::select('group_name')->distinct()->get();
        
        
        return view('admin.roles.add',compact('permission'));
    }

    public function create_record(Request $request){
      
		$request->validate([
            'name' => 'required|unique:roles'
        ]);
    	try {
        	$postData = $request->all();
        	$data = array(
				'name' => $postData['name']
        	);
            $record = Role::create($data);
        //   dd($request->input('permission'));
        	if($record){
                $record->syncPermissions($request->permission);
                
                $routes = ($request->action == 'saveadd') ? 'role.add' : 'roles.list';
        		return redirect()->route($routes)->with('status', 'success')->with('message', 'Role '.Config::get('constants.SUCCESS.CREATE_DONE'));
        	}
        } catch ( \Exception $e ) {
            return redirect()->back()->with('status', 'error')->with('message', $e->getMessage());
        }
    }

    public function edit_form($id){
        $roleId = jsdecode_userdata($id);
        $record = Role::find($roleId);
        $permission = Permission::select('group_name')->distinct()->get();
       
        $rolePermissions = DB::table("role_has_permissions")->where("role_has_permissions.role_id",$id)
            ->pluck('role_has_permissions.permission_id','role_has_permissions.permission_id')
            ->all();
        $rolePermissions = $record->permissions->pluck('name')->toArray();
        
        return view('admin.roles.edit', compact('record','permission','rolePermissions'));
    }

    public function update_record(Request $request){
        $postData = $request->all();
        $roleId = jsdecode_userdata($postData['edit_record_id']);

		// $id =$postData['edit_record_id'];
		$request->validate([
            'name' => 'required|unique:roles,name,'.$roleId,
        ]);
    	try {    	
            
            $role = Role::find($roleId);
            $role->name = $request->input('name');
            $role->save();
            
            $role->syncPermissions($request->input('permission'));
        	return redirect()->route('roles.list')->with('status', 'success')->with('message', 'Role '.Config::get('constants.SUCCESS.UPDATE_DONE'));        	
        } catch ( \Exception $e ) {
            return redirect()->back()->with('status', 'error')->with('message', Config::get('constants.ERROR.TRY_AGAIN_ERROR'));
        }
    }
	
    public function del_record($id){
        try {
            Role::where('id',$id)->delete();
        	return redirect()->back()->with('status', 'success')->with('message', 'Role '.Config::get('constants.SUCCESS.DELETE_DONE'));
        }catch(Exception $ex){
            return redirect()->back()->with('status', 'error')->with('message', Config::get('constants.ERROR.TRY_AGAIN_ERROR'));
        }
    }
}
