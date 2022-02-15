<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use app\Request;
/*
    This class plays the role of the email delivery service
*/

class DeliveryService 
{

    public static function sendRequest($request_id, $sender_email, $receiver_email, $message, $priority = "NORMAL"){
        $request = new class{};
        $request->request_id = $request_id;
        $request->sender = $sender_email;
        $request->recipient = $receiver_email;
        $request->message = $message;
        $request->priority = $priority;
        return Util::sendRequestToMailDeliveryService($request, Config('app.url').'/api/emails');
    }

}
