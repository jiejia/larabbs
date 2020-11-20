<?php

namespace App\Http\Controllers;

use App\Models\Reply;
use Illuminate\Http\Request;
use App\Http\Requests\ReplyRequest;
use Illuminate\Support\Facades\Auth;

class RepliesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * @param ReplyRequest $request
     * @param Reply $reply
     * @return \Illuminate\Http\RedirectResponse
     * @version  2020-11-20 15:10
     * @author   jiejia <jiejia2009@gmail.com>
     * @license  PHP Version 7.2.9
     */
    public function store(ReplyRequest $request, Reply $reply)
    {
        $reply->content = $request->post('content');
        $reply->user_id = Auth::id();
        $reply->topic_id = $request->topic_id;
        $reply->save();
        return redirect()->to($reply->topic->link())->with('success', '评论创建成功！');
    }

    /**
     * @param Reply $reply
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @version  2020-11-20 15:10
     * @author   jiejia <jiejia2009@gmail.com>
     * @license  PHP Version 7.2.9
     */
	public function destroy(Reply $reply)
	{
        $this->authorize('destroy', $reply);
        $reply->delete();

        return redirect()->to($reply->topic->link())->with('success', '评论删除成功！');
	}
}
