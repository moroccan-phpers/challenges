<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Util;
use Validator;
use App\Request as MailRequest;
use App\DeliveryService;

class RequestsHandler extends Controller
{

    # provide the statu of a specified request
    public static function status(Request $req, $request_id){
        $request = MailRequest::where('request_id',$request_id)->first();
        if($request){
            $response = new class{};
            $response->status = $request->state;
            $response->request_id = $request_id;
            return response(json_encode($response), 200)->header('Content-Type', 'application/json');
        }else{
            return response("{ \"ERROR\": \"INVALIDE REQUEST ID\" }", 500)->header('Content-Type', 'application/json');
        }
    }

    # re-send a request to the mail delivery service
    public static function send(Request $req){
        # receive json data
        $data = $req->json()->all();
        # set the required fields
        if(isset($data['sender']) && isset($data['message']) && isset($data['recipient']))
        {
            $validator = Validator::make(['sender' => $data['sender'],'message' => $data['message'], 'recipient' => $data['recipient']],[
                'sender' => 'required|email',
                'recipient' => 'required|email',
                'message' => 'required'
            ]);

            # check if the required fields are provided
            if($validator->passes()){

                # generate a unique id
                # instead of UID, I like to use sha256
                $request_id = Util::generateRequestId($data['sender'], $data['recipient'], $data['message']);
                if(isset($data['priority']) && $data['priority'] == "NOW"){
                    $response = DeliveryService::sendRequest($request_id, $data['sender'], $data['recipient'], $data['message'], $data['priority']);
                    if($response->httpCode == 200){
                        $res = json_decode($response->body);
                        $responseObj = new class{};
                        $responseObj->request_id = $request_id;
                        $responseObj->status = $res->STATUS;
                        # save to our database
                        MailRequest::saveRequest($request_id, $data['sender'], $data['recipient'], $data['message'], $res->STATUS, $data['priority']);
                        # return response
                        return response(json_encode($responseObj), 200)->header('Content-Type', 'application/json');
                    }else{
                        $res = json_decode($response->body);
                        $responseObj = new class{};
                        $responseObj->request_id = $request_id;
                        $responseObj->status = "FAILED";
                        # save to our database
                        MailRequest::saveRequest($request_id, $data['sender'], $data['recipient'], $data['message'], "FAILED", $data['priority']);
                        # return response
                        return response(json_encode($responseObj), 500)->header('Content-Type', 'application/json');
                    }
                }else{
                    $response = DeliveryService::sendRequest($request_id, $data['sender'], $data['recipient'], $data['message']);
                    if($response->httpCode == 200){
                        # save to our database
                        MailRequest::saveRequest($request_id, $data['sender'], $data['recipient'], $data['message'], "ACCEPTED");
                        # return response
                        return response("{ \"STATUS\": \"ACCEPTED\", \"REQUEST_ID\": \"$request_id\" }", 200)->header('Content-Type', 'application/json');
                    }else{
                        # save to our database
                        MailRequest::saveRequest($request_id, $data['sender'], $data['recipient'], $data['message'], "NOT ACCEPTED");
                        # return response
                        return response("{ \"STATUS\": \"NOT ACCEPTED\", \"REQUEST_ID\": \"$request_id\", \"MSG\": \"MAIL DELIVERY SERVICE IS DOWN,WE WILL RESEND THE REQUEST AUTOMATICALLY WHEN IT GOES UP AGAIN\" }", 200)->header('Content-Type', 'application/json');
                    }
                }
            }else{
                return response('{"STATUS": "DENIED" ,"ERROR": "EMAILS ARE NOT VALID"}', 500)->header('Content-Type', 'application/json');
            }
        }
        else{
            return response('{"STATUS": "DENIED", "ERROR": "MISSING FIELDS"}', 500)->header('Content-Type', 'application/json');
        }
    }

    # callback from delivery service to update the statu of a request
    public static function callback(Request $req){
        # receive json data
        $data = $req->json()->all();

        # validate the inputs
        if(isset($data['ID']) && isset($data['STATUS'])){
            $request = MailRequest::where('request_id',$data['ID'])->first();
            if($request){
                $request->state = $data['STATUS'];
                $request->save();
                return response("{}", 200)->header('Content-Type', 'application/json');
            }else{
                return response("{ \"ERROR\": \"INVALIDE REQUEST ID\" }", 500)->header('Content-Type', 'application/json');
            }
        }else{
            return response("{ \"ERROR\": \"INVALIDE REQUEST\" }", 500)->header('Content-Type', 'application/json');
        }
    }


    public static function reSendRequests(){
        $notAcceptedRequests =  MailRequest::where('state', 'NOT ACCEPTED')->get();
        foreach($notAcceptedRequests as $notAcceptedRequest){
            $response = DeliveryService::sendRequest($notAcceptedRequest->request_id, $notAcceptedRequest->sender, $notAcceptedRequest->receiver, $notAcceptedRequest->message);
            echo $response->body;
            if($response->httpCode == 200){
                $notAcceptedRequest->state = "ACCEPTED";
                $notAcceptedRequest->save();
            }else{
                # if the server is down, there is no point in sending more request
                # which may be the reason for the server to stay down.
                break;
            }
        }
    }
}
