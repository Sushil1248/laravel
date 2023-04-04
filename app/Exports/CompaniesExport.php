<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\{FromQuery,WithMapping,WithHeadings,ShouldAutoSize};

class CompaniesExport implements FromQuery,WithMapping,WithHeadings,ShouldAutoSize
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
            $query->where('name', '=', '1_Company');
        })->when( !empty($start) && !empty($end) , function($q , $from) use( $start , $end ) {
            $q->whereBetween( 'created_at' , [$start , $end] );
        })->when($request->search ,function($qu , $keyword ) {
            $qu->where(function ($q) use( $keyword ) {
                $q->where('company_name', 'like', '%'.$keyword.'%')
                ->orWhere('id', $keyword);
            });
        })
        ->when( $request->filled('status') , function($qu){
            $qu->where('status',request('status'));
        })->when( jsdecode_userdata($request->company_id) , function( $query , $company_id ){
            $query->where('id',$company_id);
        });
        return $data->with('company_detail.country');
    }

    public function map($company): array
    {
        return [
            $company->company_detail->company_name,
            $company->email,
            $company ? $company->company_detail->contact_person : '',
            $company ? ucfirst($company->company_detail->contact_number) : '',
            $company->company_detail ? $company->company_detail->address : '',
            $company->created_at
        ];
    }
    public function headings(): array
    {
        return ["Company Name", "Company Email","Contact Person","Contct Number","Address","Created at"];
    }
}
