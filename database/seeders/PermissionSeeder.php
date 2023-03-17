<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $permission = [
            ['name'  =>  'user-list','group_name'    =>  'user'],
            ['name'  =>  'user-delete','group_name'    =>  'user'],
            ['name'  =>  'user-edit','group_name'    =>  'user'],
            ['name'  =>  'user-add','group_name'    =>  'user'],
            ['name' =>  'user-view','group_name'    =>  'user'],
            ['name' =>  'user-status','group_name'    =>  'user'],

            ['name'  =>  'company-list','group_name'    =>  'company'],
            ['name'  =>  'company-delete','group_name'    =>  'company'],
            ['name'  =>  'company-edit','group_name'    =>  'company'],
            ['name'  =>  'company-add','group_name'    =>  'company'],
            ['name' =>  'company-view','group_name'    =>  'company'],
            ['name' =>  'company-status','group_name'    =>  'company'],

            ['name'  =>  'role-list','group_name'    =>  'role'],
            ['name'  =>  'role-delete','group_name'    =>  'role'],
            ['name'  =>  'role-edit','group_name'    =>  'role'],
            ['name'  =>  'role-add','group_name'    =>  'role'],
            ['name' =>  'role-view','group_name'    =>  'role'],
            ['name' =>  'role-status','group_name'    =>  'role'],

            ['name'  =>  'propfile-update','group_name'    =>  'profile'],
            ['name'  =>  'change-password','group_name'    =>  'profile'],

        ];
        foreach( $permission as $singlePermission ){
            Permission::updateOrCreate([
                'name'  =>  $singlePermission['name']
            ],[
                'group_name'  =>  $singlePermission['group_name']
            ]);
        }
    }
}
