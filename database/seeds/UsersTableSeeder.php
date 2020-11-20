<?php

use Illuminate\Database\Seeder;
use App\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 获取 Faker 实例
        $faker = app(Faker\Generator::class);
        // 头像假数据
        $avatars = [
            '/uploads/images/avatars/202011/16/11_1605525981_8Hb7Erc5zb.jpg',
            '/uploads/images/avatars/202011/17/11_1605543494_SlAUOwdQCL.jpg',

        ];

        // 生成数据集合
        $users = factory(User::class)
            ->times(10)
            ->make()
            ->each(function ($user, $index)
            use ($faker, $avatars)
            {
                // 从头像数组中随机取出一个并赋值
                $user->avatar = $faker->randomElement($avatars);
            });

        // 让隐藏字段可见，并将数据集合转换为数组
        $user_array = $users->makeVisible(['password', 'remember_token'])->toArray();
        // 插入到数据库中
        User::insert($user_array);
        // 单独处理第一个用户的数据
        $user = User::find(1);
        $user->name = 'jiejia';
        $user->email = 'jiejia2009@gmail.com';
       // $user->avatar = 'https://iocaffcdn.phphub.org/uploads/images/201710/14/1/ZqM7iaP4CR.png';
        $user->save();

        // 初始化用户角色，将 1 号用户指派为『站长』
        $user->assignRole('Founder');

        // 将 2 号用户指派为『管理员』
        $user = User::find(2);
        $user->name = 'zoe';
        $user->email = '314728819@qq.com';
        // $user->avatar = 'https://iocaffcdn.phphub.org/uploads/images/201710/14/1/ZqM7iaP4CR.png';
        $user->save();

        $user->assignRole('Maintainer');
    }
}
