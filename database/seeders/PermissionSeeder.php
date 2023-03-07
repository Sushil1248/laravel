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

            ['name'  =>  'equipment-list','group_name'    =>  'equipment'],
            ['name'  =>  'equipment-delete','group_name'    =>  'equipment'],
            ['name'  =>  'equipment-edit','group_name'    =>  'equipment'],
            ['name'  =>  'equipment-add','group_name'    =>  'equipment'],
            ['name'  =>  'equipment-status','group_name'    =>  'equipment'],

            ['name'  =>  'media-list','group_name'    =>  'media'],
            ['name'  =>  'media-delete','group_name'    =>  'media'],
            ['name'  =>  'media-edit','group_name'    =>  'media'],
            ['name'  =>  'media-add','group_name'    =>  'media'],
            ['name'  =>  'media-status','group_name'    =>  'media'],

            ['name'  =>  'exercise-list','group_name'    =>  'exercise'],
            ['name'  =>  'exercise-delete','group_name'    =>  'exercise'],
            ['name'  =>  'exercise-edit','group_name'    =>  'exercise'],
            ['name'  =>  'exercise-add','group_name'    =>  'exercise'],
            ['name'  =>  'exercise-status','group_name'    =>  'exercise'],

            ['name'  =>  'category-list','group_name'    =>  'category'],
            ['name'  =>  'category-delete','group_name'    =>  'category'],
            ['name'  =>  'category-edit','group_name'    =>  'category'],
            ['name'  =>  'category-add','group_name'    =>  'category'],
            ['name'  =>  'category-status','group_name'    =>  'category'],

            ['name'  =>  'steph-workout-list','group_name'    =>  'Steph Workout'],
            ['name'  =>  'steph-workout-delete','group_name'    =>  'Steph Workout'],
            ['name'  =>  'steph-workout-edit','group_name'    =>  'Steph Workout'],
            ['name'  =>  'steph-workout-add','group_name'    =>  'Steph Workout'],
            ['name'  =>  'steph-workout-status','group_name'    =>  'Steph Workout'],

            ['name'  =>  'program-list','group_name'    =>  'Program'],
            ['name'  =>  'program-delete','group_name'    =>  'Program'],
            ['name'  =>  'program-edit','group_name'    =>  'Program'],
            ['name'  =>  'program-add','group_name'    =>  'Program'],
            ['name'  =>  'program-status','group_name'    =>  'Program'],
            ['name'     =>  'program-manage-exercise','group_name'    =>  'Program'],
            ['name'     =>  'program-manage-bonus-exercise','group_name'    =>  'Program'],

            ['name'  =>  'workout-list','group_name'    =>  'Workout'],
            ['name'  =>  'workout-delete','group_name'    =>  'Workout'],
            ['name'  =>  'workout-edit','group_name'    =>  'Workout'],
            ['name'  =>  'workout-add','group_name'    =>  'Workout'],
            ['name'  =>  'workout-status','group_name'    =>  'Workout'],
            ['name'     =>  'workout-manage-exercise','group_name'    =>  'Workout'],
            ['name'     =>  'workout-manage-bonus-exercise','group_name'    =>  'Workout'],
            
            ['name'  =>  'cms-management-list','group_name'    =>  'CMS Management'],
            ['name'  =>  'cms-management-delete','group_name'    =>  'CMS Management'],
            ['name'  =>  'cms-management-edit','group_name'    =>  'CMS Management'],
            ['name'  =>  'cms-management-add','group_name'    =>  'CMS Management'],
            ['name'  =>  'cms-management-status','group_name'    =>  'CMS Management'],
            ['name'     =>  'cms-management-manage-exercise','group_name'    =>  'CMS Management'],
            ['name'     =>  'cms-management-manage-bonus-exercise','group_name'    =>  'CMS Management'],

            ['name'  =>  'subscription-plan-list','group_name'    =>  'Subscription Plan'],
            ['name'  =>  'subscription-plan-delete','group_name'    =>  'Subscription Plan'],
            ['name'  =>  'subscription-plan-edit','group_name'    =>  'Subscription Plan'],
            ['name'  =>  'subscription-plan-add','group_name'    =>  'Subscription Plan'],
            ['name'  =>  'subscription-plan-status','group_name'    =>  'Subscription Plan'],

            ['name'  =>  'forum-question-list','group_name'    =>  'Forum Question'],
            ['name'  =>  'forum-question-delete','group_name'    =>  'Forum Question'],
            ['name'  =>  'forum-question-edit','group_name'    =>  'Forum Question'],
            ['name'  =>  'forum-question-add','group_name'    =>  'Forum Question'],
            ['name'  =>  'forum-question-status','group_name'    =>  'Forum Question'],
            ['name'  =>  'forum-question-answers','group_name'    =>  'Forum Question'],

            ['name'  =>  'payment-list','group_name'    =>  'Payment'],

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
