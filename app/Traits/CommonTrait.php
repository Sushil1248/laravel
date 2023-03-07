<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model; 
Use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Image, Auth, Session, Cookie, DB;
use App\Models\{User};

trait CommonTrait
{ 
    /*
    Method Name:    createThumbnail
    Developer:      Shiv K. Agg
    Created Date:   2021-08-09 (yyyy-mm-dd)
    Purpose:        To create Image thumbnail at fly
    Params:         []
    */
    public function createThumbnail($image, $extension, $width = 250, $height = 75){ 
        try{
            $image_resize = Image::make($image)->resize( $width, $height, function ( $constraint ) {
                $constraint->aspectRatio();
            })->encode($extension);
            return $image_resize;
        } catch(\Exception $e) {
            return FALSE;
        }
    }
    /* End Method createThumbnail */
    
    /*
    Method Name:    unserializeData()
    Developer:      Shiv K. Agg
    Created Date:   2021-08-09 (yyyy-mm-dd)
    Purpose:        To unserialize data
    Params:         [serializeddata]
    */
    public function unserializeData($serializeddata = NULL){
        if($serializeddata)
        return $answer_data = unserialize($serializeddata);
        else
        return FALSE;
    }
    /* End Method unserializeData */
     
    /*
    Method Name:    createCookieID()
    Developer:      Shiv K. Agg
    Created Date:   22021-08-09 (yyyy-mm-dd)
    Purpose:        To check if usser have purcased case in his account
    Params:         [id]
    */
    public function createCookieID(){
        $id = strtoupper(substr(md5(request()->ip().Str::random(12)), 5, 10));
		if(request()->cookie('USER_COOKIE_ID')) {
			Session::put('user_cookie_id', request()->cookie('USER_COOKIE_ID'));
		} else {
			Cookie::queue(cookie('USER_COOKIE_ID', $id, $minute = 43200));
			Session::put('user_cookie_id', $id);
        }
        return $id;
    }
    /* End Method createCookieID */

    /*
    Method Name:    getUsers
    Developer:      Shiv K. Agg
    Created Date:   2021-08-09 (yyyy-mm-dd)
    Purpose:        To get list of all users
    Params:         []
    */
    public function getUsers() { 
        $start = session('startDate');
        $end = session('endDate');
        $keyword = session('keyword'); 
        // $deleted = session('deleted'); 
        $users = User::role('Customer')->select(DB::raw('id, first_name, last_name, email, IF(users.status = 1, "Active","Inactive") as status,created_at'))
        ->where(function ($q) use($start,$end) {
            $q->when(!empty($start) && !empty($end) ,function($query) use($start ,$end) {
                $query->whereBetween('created_at', [$start, $end]);
            }); 
        })
        ->where(function ($q) use($keyword ) {
            $q->when(!empty($keyword),function($qu) use($keyword) {
                $qu->where('first_name', 'like', '%'.$keyword.'%')
                ->orWhere('last_name', 'like', '%'.$keyword.'%')
                ->orWhere('email', 'like', '%'.$keyword.'%')
                ->orWhere('id', $keyword);
            });
        });
        // if($deleted == 'deleted') {
        //     $users= $users->where('is_deleted', 1);
        // } else {
        //     $users= $users->where('is_deleted', 0);
        // }
        $users =$users->orderBy('users.id', 'desc')->get()->toArray();
        return $users;
    }
    /* End Method getUsers */

}

