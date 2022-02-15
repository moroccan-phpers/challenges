<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Mail;
use App\Util;
use HTTP_Request2;

class DeliveryServiceHandler extends Controller
{


    # this is a mocking demo for the mail delivery service endpoint /api/emails
    # I will be a little lazy and will not validate the data for this endpoint
    # and asume that all the validation are done on the microservice
    public static function receiver(Request $req){

        # there is always a chance of server is down
        # to mock that state
        if(Util::isServerDown()){
            return response("{\"STATUS\": \"ERROR\"}", 500)->header('Content-Type', 'application/json');
        }

        # convert json data from a post request into object
        $data = $req->json()->all();

        # check for the priority option
            # priority with value equal to NOW means that mail delivery have to deal with them now
            # when priority with value equal to NORMAL means the delivery mail service can add the request to other requests
            # and deal with them when possible
        if($data['priority'] == "NOW"){
            # since there are three posibilities
            # "DELIVERED" | "REJECTED" | "FAILED"
            # I will go with change or wither to get rejected or fail to 2% each
            # and 96% to deliver
            $randomValue = rand(0,1);

            # REJECTED
            if($randomValue < 0.02){
                Mail::saveMail($data['request_id'], $data['sender'], $data['recipient'],$data['priority'], $data['message'], "REJECTED");
                $response = new class{};
                $response->ID = $data['request_id'];
                $response->STATUS = "REJECTED";
                return response(json_encode($response), 200)->header('Content-Type', 'application/json');
            }else
            # FAILED
            if($randomValue < 0.04){
                Mail::saveMail($data['request_id'], $data['sender'], $data['recipient'],$data['priority'], $data['message'], "FAILED");
                $response = new class{};
                $response->ID = $data['request_id'];
                $response->STATUS = "FAILED";
                return response(json_encode($response), 200)->header('Content-Type', 'application/json');
            # DELIVERED
            }else{
                Mail::saveMail($data['request_id'], $data['sender'], $data['recipient'],$data['priority'], $data['message'], "DELIVERED");
                $response = new class{};
                $response->ID = $data['request_id'];
                $response->STATUS = "DELIVERED";
                return response(json_encode($response), 200)->header('Content-Type', 'application/json');
            }
        # PRIORITY NORMAL
        }else{
            Mail::saveMail($data['request_id'], $data['sender'], $data['recipient'],$data['priority'], $data['message'], "ACCEPTED");
            return response(json_encode(['status' => 'ACCEPTED']), 200)->header('Content-Type', 'application/json');
        }
    }

    # this function is called by the schedule every minute to check if there are un processed emails,
    # and start processing them
    # then hit the callback endpoint of the app
    # to update the state of each mail request
    # i will go with the same chances for delivered | failed | rejected as in receiver function
    public static function webhook(){
        $mails = Mail::where('state','ACCEPTED')->get();

        foreach($mails as $mail){
            $randomValue = rand(0,1);

            if($randomValue < 0.02){
                $mail->state = "REJECTED";
                $mail->save();

                $response = new class{};
                $response->ID = $mail->request_id;
                $response->STATUS = "REJECTED";

                Util::webhookResponse($response, Config('app.url').'/callback');
            }else
            # FAILED
            if($randomValue < 0.04){
                $mail->state = "FAILED";
                $mail->save();

                $response = new class{};
                $response->ID = $mail->request_id;
                $response->STATUS = "FAILED";

                Util::webhookResponse($response, Config('app.url').'/callback');
            # DELIVERED
            }else{
                $mail->state = "DELIVERED";
                $mail->save();

                $response = new class{};
                $response->ID = $mail->request_id;
                $response->STATUS = "DELIVERED";

                Util::webhookResponse($response, Config('app.url').'/callback');
            }
        }
    }
}
