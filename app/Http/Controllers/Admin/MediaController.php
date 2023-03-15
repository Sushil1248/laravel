<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Config,DB,Validator};
use App\Models\{Media};
use Spatie\Permission\Models\Permission;
use Auth;


class MediaController extends Controller
{

    /*
    Method Name:    getList
    Developer:      Shiv K. Agg
    Purpose:        To get list of all users
    Params:
    */
    public function getList( Request $request ){
        $start = $end = "";
        if( $request->filled('daterange_filter') ) {
            $daterange = $request->daterange_filter;
            $daterang = explode(' - ',$daterange);
            $start = $daterang[0].' 00:05:00';
            $end = $daterang[1].' 23:05:59';
        } 
        $data = Media::when( !empty($start) && !empty($end) , function($q , $from) use( $start , $end ) {
            $q->whereBetween( 'created_at' , [$start , $end] );
        })->when($request->search ,function($qu , $keyword ) {
            $qu->where(function ($q) use( $keyword ) {
                $q->where('name', 'like', '%'.$keyword.'%');
            });
        })
        ->when( $request->filled('status') , function($qu){
            $qu->where('status',request('status'));
        });
        $deletedData = (clone $data)->onlyTrashed()->sortable(['id' => 'desc'])
        ->paginate(Config::get('constants.PAGINATION_NUMBER'),'*','dpage');
        $data = $data->sortable(['id' => 'desc'])->paginate(Config::get('constants.PAGINATION_NUMBER'));
        return view('admin.media.list', compact('data','deletedData'));
    }
    /* End Method getList */

    /*
    Method Name:    del_record
    Developer:      Shiv K. Agg
    Purpose:        To delete any user by id
    Params:         [id]
    */
    public function del_record($id){
        try { 
            $mediaId = jsdecode_userdata($id);
            Media::where('id',$mediaId)->delete();
        	return redirect()->back()->with('status', 'success')->with('message', 'Media detail '.Config::get('constants.SUCCESS.DELETE_DONE'));
        } catch(Exception $ex) {
            return redirect()->back()->with('status', 'error')->with('message', $ex->getMessage());
        }
    }
    /* End Method del_record */

    /*
    Method Name:    del_restore
    Developer:      Shiv K. Agg
    Purpose:        To restore deleted Media by id
    Params:         [id]
    */
    public function del_restore($id){
        try {
            $mediaId = jsdecode_userdata($id);
            Media::where('id',$mediaId)->restore();
        	return redirect()->back()->with('status', 'success')->with('message', 'Media detail '.Config::get('constants.SUCCESS.RESTORE_DONE'));
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
    public function edit(   $id , Request $request){
        $mediaId = jsdecode_userdata($id);
        $mediaDetail = Media::find($mediaId);
        if(!$mediaDetail)
            return redirect()->route('media.list');
        if ($request->isMethod('get')){
            return view('admin.media.edit',compact('mediaDetail'));
        }else{
            $userId = jsdecode_userdata($id);
            $request->validate([
                'file'  =>  'file|max:2048|mimetypes:image/*',
                'name'  =>  'required|max:100'
            ]);
            try {
                $updateData = [
                    'name'  =>  $request->name,
                    'status'    =>  $request->input('status',0)
                ];
                if( $request->hasFile('file') ){
                    $mediaPath = $request->file->store('public/media-files');
                    $updateData = array_merge($updateData,[
                        'path'  =>  str_replace( "public/" , "" , $mediaPath ),
                        'type'  =>  $request->file->getMimeType()
                    ]);
                }
                $mediaDetail->update($updateData);
                return redirect()->route('media.list')->with('status', 'success')->with('message', 'Media detail '.Config::get('constants.SUCCESS.UPDATE_DONE'));
            } catch ( \Exception $e ) { 
                return redirect()->back()->withInput()->with('status', 'error')->with('message', $e->getMessage());
            }
        }
    }
    /* End Method edit_form */


     /*
     Method Name:    add_form
     Developer:      Shiv K. Agg
     Created Date:   2022-06-23 (yyyy-mm-dd)
     Purpose:        Form to add user details
    Params:         [id]
     */
    public function add(Request $request ){
        try {
            $validator = Validator::make($request->all(),[
                'file'  =>  'file|max:2048|mimetypes:image/*'
            ],[
                'file.mimetypes'    =>  'Format of image not valid'
            ]);
            if ( $validator->fails() ) {
                return response()->json([
                    'error' => $validator->errors()->first()
                ],400);
            }
            DB::beginTransaction();
            $mediaPath = $request->file->store('public/media-files');
            $result = Media::create([
                'name'  =>  $request->file->getClientOriginalName(),
                'path'  =>  str_replace( "public/" , "" , $mediaPath ),
                'type'  =>  $request->file->getMimeType(),
                'status'=>  1
            ]);
            if($result) {
                DB::commit();
                return response()->json([
                    'message' => 'File uploaded!!'
                ]);
            }
            DB::rollBack();
            return response()->json([
                'success'    =>  false,
                'msg'       =>  Config::get('constants.ERROR.OOPS_ERROR'),
                'error'     =>  $e->getMessage()
            ],400);
        } catch ( \Exception $e ) {
            DB::rollBack();
            return response()->json([
                'success'    =>  false,
                'msg'       =>  $e->getMessage(),
                'error'     =>  $e->getMessage()
            ],400);
        }
        
    }
    
    

    /* End Method view_detail */
    
    public function changeStatus(Request $request)
    {
        try {
            $media = Media::withTrashed()->find( jsdecode_userdata($request->id) );
            $media->status = $request->status;
            $media->save();
            return response()->json(['success' => 'Status change successfully.','message' => 'Media '.Config::get('constants.SUCCESS.STATUS_UPDATE')]);
        } catch ( \Exception $e ) {
            return redirect()->back()->with('status', 'error')->with('message', $e->getMessage());
        }
    }
}
