<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\{User};
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use DB;
class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call([PermissionSeeder::class]);
        $role = Role::firstOrCreate(['name' => 'Administrator']);
        $permissions = Permission::pluck('id','id')->all();
        $role->syncPermissions($permissions);

        $user = User::role('Administrator')->first();
        if( !$user ){
            $user = User::create([
                'first_name' => 'Admin',
                'last_name'     =>  'Admin',
                'email' => 'admin@gmail.com',
                'password' => bcrypt('pass@admin'),
                'remember_token' => Str::random(10),
                'status' => 1,
                'created_at' => date('Y-m-d H:i:s'),
            ]);
        }
        $user->assignRole($role);
    }
}
