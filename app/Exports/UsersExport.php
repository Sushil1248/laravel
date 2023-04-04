<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\{FromQuery,WithMapping,WithHeadings,ShouldAutoSize};

class UsersExport implements FromQuery,WithMapping,WithHeadings,ShouldAutoSize
{

    public function query()
    {
        $request = request();
        $start = $end = "";
        if( $request->filled('daterange_filter') ) {
            $daterange = $request->daterange_filter;
            $daterang = explode(' - ',$daterange);
            $start = $daterang[0].' 00:00:00';
            $end = $daterang[1].' 23:05:59';
        }
        $data = User::whereHas('roles', function ($query) {
            $query->where('name', '=', '1_User');
        })->when( !empty($start) && !empty($end) , function($q , $from) use( $start , $end ) {
            $q->whereBetween( 'created_at' , [$start , $end] );
        })->when($request->search ,function($qu , $keyword ) {
            $qu->where(function ($q) use( $keyword ) {
                $q->where('first_name', 'like', '%'.$keyword.'%')
                ->orWhere('last_name', 'like', '%'.$keyword.'%')
                ->orWhere('email', 'like', '%'.$keyword.'%')
                ->orWhere('id', $keyword);
            });
        })
        ->when( $request->filled('status') , function($qu){
            $qu->where('status',request('status'));
        })->when( jsdecode_userdata($request->user_id) , function( $query , $user_id ){
            $query->where('id',$user_id);
        });
        return $data->with('user_detail.country');
    }

    public function map($user): array
    {
        return [
            $user->full_name,
            $user->email,
            $user->user_detail ? $user->user_detail->mobile : '',
            $user->user_detail ? ucfirst($user->user_detail->gender) : '',
            $user->user_detail ? $user->user_detail->address : '',
            $user->user_detail && $user->user_detail->country ? $user->user_detail->country->name : '',
            $user->created_at
        ];
    }
    public function headings(): array
    {
        return ["Full name", "Email","Mobile","Gender","Address","Country","Created at"];
    }
}
