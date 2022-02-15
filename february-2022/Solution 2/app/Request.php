<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Request extends Model
{
    public static function saveRequest($request_id, $sender, $recepient, $message, $state, $priority = "NORMAL"){
        $request = new Request();
        $request->request_id = $request_id;
        $request->sender = $sender;
        $request->receiver = $recepient;
        $request->message = $message;
        $request->priority = $priority;
        $request->state = $state;
        $request->save();
    }
}
