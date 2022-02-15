<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Mail extends Model
{
    public static function saveMail($request_id, $sender, $recepient, $priority = "NORMAL", $message, $state){
        $mail = new Mail();
        $mail->request_id = $request_id;
        $mail->sender = $sender;
        $mail->receiver = $recepient;
        $mail->message = $message;
        $mail->priority = $priority;
        $mail->state = $state;
        $mail->save();
    }
}
