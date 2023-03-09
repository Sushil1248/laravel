<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\CmsPage;

class CmsPageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        CmsPage::insert([
            ['name' => 'Help & Support'],
            ['name' => 'Contact us details'],
            ['name' => 'Terms and conditions'],
            ['name' => 'Privacy policies'],
            ['name' => 'About Us'],
            ['name' => 'How it works']
        ]);
    }
}
