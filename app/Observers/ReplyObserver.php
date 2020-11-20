<?php

namespace App\Observers;

use App\Models\Reply;
use App\Notifications\TopicReplied;

// creating, created, updating, updated, saving,
// saved,  deleting, deleted, restoring, restored

class ReplyObserver
{
    public function creating(Reply $reply)
    {
        //
        $reply->content = clean($reply->content, 'user_topic_body');

    }

    public function updating(Reply $reply)
    {
        //
    }

    public function created(Reply $reply)
    {
        $reply->topic->reply_count = $reply->topic->replies->count();
        $reply->topic->save();

        $reply->topic->user->notify(new TopicReplied($reply));
    }

    /**
     * @param Reply $reply
     * @version  2020-11-20 15:15
     * @author   jiejia <jiejia2009@gmail.com>
     * @license  PHP Version 7.2.9
     */
    public function deleted(Reply $reply)
    {
        $reply->topic->updateReplyCount();
    }
}
