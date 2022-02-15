<?php

namespace App;
use HTTP_Request2;

class Util
{
    public static function webhookResponse($req, $endpoint){
        $request = new HTTP_Request2();

        $request->setUrl($endpoint);
        $request->setMethod(HTTP_Request2::METHOD_POST);
        $request->setConfig(array(
          'follow_redirects' => TRUE
        ));
        $request->setHeader(array('Content-Type' => 'application/json'));
        $request->setBody(json_encode($req));
        $response = $request->send();
    }

    public static function generateRequestId($sender, $receiver, $body){
        $string = $_SERVER['REQUEST_TIME'].':'.$sender.':'.$receiver.':'.$body;
        return hash('sha256',$string);
    }

    # 1% chance
    public static function isServerDown(){
        return rand(0 , 1) > 0.98;
    }

    public static function sendRequestToMailDeliveryService($req, $endpoint){
        $request = new HTTP_Request2();

        $request->setUrl($endpoint);
        $request->setMethod(HTTP_Request2::METHOD_POST);
        $request->setConfig(array(
          'follow_redirects' => TRUE
        ));
        $request->setHeader(array('Content-Type' => 'application/json'));
        $request->setBody(json_encode($req));
        $response = $request->send();

        $res = new class{};
        $res->httpCode = $response->getStatus();
        $res->body = $response->getBody();

        return $res;
    }
}
