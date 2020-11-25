<?php

namespace App\Models\Traits;

use Illuminate\Support\Facades\Redis;
use Carbon\Carbon;

trait LastActivedAtHelper
{
    // 缓存相关
    protected $hash_prefix = 'larabbs_last_actived_at_';

    protected $field_prefix = 'user_';

    /**
     * 记录用户最后活动时间
     *
     * @version  2020-11-25 11:45
     * @author   jiejia <jiejia2009@gmail.com>
     * @license  PHP Version 7.2.9
     */
    public function recordLastActivedAt()
    {
        // 获取今天的日期
        $date = Carbon::now()->toDateString();

        // Redis 哈希表的命名，如：larabbs_last_actived_at_2017-10-21
        $hash = $this->getHashFromDateString($date);

        // 字段名称，如：user_1
        $field = $this->getHashField();

        //dd(Redis::hGetAll($hash));

        // 当前时间，如：2017-10-21 08:35:15
        $now = Carbon::now()->toDateTimeString();

        // 数据写入 Redis ，字段已存在会被更新
        Redis::hSet($hash, $field, $now);
    }

    /**
     * 同步用户活动时间到数据库
     *
     * @version  2020-11-25 11:50
     * @author   jiejia <jiejia2009@gmail.com>
     * @license  PHP Version 7.2.9
     */
    public function syncUserActivedAt()
    {
        // 获取昨天的日期，格式如：2017-10-21
        $yesterday_date = Carbon::yesterday()->toDateString();

        // Redis 哈希表的命名，如：larabbs_last_actived_at_2017-10-21
        $hash = $this->getHashFromDateString($yesterday_date);

        // 从 Redis 中获取所有哈希表里的数据
        $dates = Redis::hGetAll($hash);

        // 遍历，并同步到数据库中
        foreach ($dates as $user_id => $actived_at) {
            // 会将 `user_1` 转换为 1
            $user_id = str_replace($this->field_prefix, '', $user_id);

            // 只有当用户存在时才更新到数据库中
            if ($user = $this->find($user_id)) {
                $user->last_actived_at = $actived_at;
                $user->save();
            }
        }

        // 以数据库为中心的存储，既已同步，即可删除
        Redis::del($hash);
    }

    /**
     * 获取用户最后活动时间
     *
     * @param $value
     * @return Carbon
     * @version  2020-11-25 11:59
     * @author   jiejia <jiejia2009@gmail.com>
     * @license  PHP Version 7.2.9
     */
    public function getLastActivedAtAttribute($value)
    {
        // 获取今天的日期
        $date = Carbon::now()->toDateString();

        // Redis 哈希表的命名，如：larabbs_last_actived_at_2017-10-21
        $hash = $this->getHashFromDateString($date);

        // 字段名称，如：user_1
        $field = $this->getHashField();

        // 三元运算符，优先选择 Redis 的数据，否则使用数据库中
        $datetime = Redis::hGet($hash, $field) ? : $value;

        // 如果存在的话，返回时间对应的 Carbon 实体
        if ($datetime) {
            return new Carbon($datetime);
        } else {
            // 否则使用用户注册时间
            return $this->created_at;
        }
    }

    public function getHashFromDateString($date)
    {
        // Redis 哈希表的命名，如：larabbs_last_actived_at_2017-10-21
        return $this->hash_prefix . $date;
    }

    public function getHashField()
    {
        // 字段名称，如：user_1
        return $this->field_prefix . $this->id;
    }
}
