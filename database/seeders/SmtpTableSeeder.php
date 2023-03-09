<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SmtpInformation;

class SmtpTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $usercount = SmtpInformation::where('from_email', 'jasjit.shinedezign@gmail.com')->count();
        if($usercount == 0){
			SmtpInformation::updateOrCreate(['id' => 1],[
				'host' => 'smtp.gmail.com',
				'port' => 587,
				'username' => 'jasjit.shinedezign@gmail.com',
				'from_email' => 'jasjit.shinedezign@gmail.com',
				'from_name' => 'LiftIt',
				'password' => 'xwdfrbjhszezuebv',
				'encryption' => 'tls',
				'status' => 1,
				// 'created_at' => date('Y-m-d H:i:s'),
			]);
		}
    }
}
