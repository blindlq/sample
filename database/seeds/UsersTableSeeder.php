<?php

use Illuminate\Database\Seeder;
use App\Models\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //填充50个数据
        $users = factory(\App\Models\User::class)->times(50)->make();
        User::insert($users->makeVisible(['password','remember_token'])->toArray());

        $user = \App\Models\User::find(1);
        $user->name = 'ZhangCheng';
        $user->email = '906314530@qq.com';
        $user->password = bcrypt('931102');
        $user->is_admin = true;
        $user->save();
    }
}
