<?php

namespace Bondif\MailDeliveryService;


use Rakit\Validation\Validator;
use Ramsey\Uuid\Uuid;

class MailDeliveryService
{

    private $connection;

    private $status;

    const HIGH_PRIORITY_SHIFT = 3000;

    public function __construct(\mysqli $connection)
    {
        $this->connection = $connection;
        $this->status[] = 'Failed';
        $this->status[] = 'Sent';
        $this->status[] = 'Processing';
    }

    public function run()
    {
        $requestURI = $_SERVER['REQUEST_URI'];

        if ($requestURI[-1] == '/') {
            header('Location: ' . substr($requestURI, 0, -1));
            http_response_code(301);
            exit();
        }

        if ($requestURI == "/send") {
            $validator = new Validator();

            $validation = $validator->validate($_POST, [
                'sender' => 'required|email',
                'recipient' => 'required|email',
                'message' => 'required',
            ]);

            if ($validation->fails()) {
                $errors = $validation->errors();
                $response = [];
                $response['status'] = 'denied';
                $response['reason'] = $errors->all();
                header('Content-type: text/json');
                http_response_code(400);
                echo json_encode($response);
                exit;
            } else {
                $data = $validation->getValidatedData();
                $sender = $data['sender'];
                $recipient = $data['recipient'];
                $message = $data['message'];
                $requestId = Uuid::uuid4()->toString();

                $sql = "insert into mails(id, request_id, sender, recipient, message) 
                       values(null, '$requestId', '$sender', '$recipient', '$message')";

                if (!$this->connection->query($sql)) {
                    http_response_code(400);
                    echo "Error description: " . $this->connection->error;
                    exit();
                }

                $priority = time();
                if (isset($_POST['priority']) && $_POST['priority'] === 'high') {
                    $priority -= static::HIGH_PRIORITY_SHIFT;
                }
                $queue = QueueManager::getQueue();
                $queue->push($requestId, $priority);

                $response['status'] = 'accepted';
                $response['request_id'] = $requestId;
                header('Content-type: text/json');
                http_response_code(201);
                echo json_encode($response);
                exit();
            }
        }

        if (preg_match("/^\/status*/", $requestURI) === 1) {
            if (isset($_GET['request_id'])) {
                $requestId = $_GET['request_id'];

                $sql = "select status from mails where request_id = ?";
                $statement = $this->connection->prepare($sql);
                $statement->bind_param('s', $requestId);
                $statement->execute();
                $result = $statement->get_result();

                if ($result && $result->num_rows === 1) {
                    $record = $result->fetch_assoc();

                    $response['status'] = $this->status[$record['status']];
                    $response['request_id'] = $requestId;

                    header('Content-type: text/json');
                    echo json_encode($response);
                    exit();
                } else {
                    http_response_code(404);
                    echo "Request Id not found";
                    exit();
                }
            } else {
                $response['error'] = 'request_id is required';
                header('Content-type: text/json');
                http_response_code(400);
                echo json_encode($response);
                exit();
            }
        }
    }
}