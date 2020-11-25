<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RecordLastActivedTime
{

    /**
     * @param $request
     * @param Closure $next
     * @return mixed
     * @version  2020-11-25 11:34
     * @author   jiejia <jiejia2009@gmail.com>
     * @license  PHP Version 7.2.9
     */
    public function handle($request, Closure $next)
    {
        // 如果是登录用户的话
        if (Auth::check()) {
            // 记录最后登录时间
            Auth::user()->recordLastActivedAt();
        }

        return $next($request);
    }
}
