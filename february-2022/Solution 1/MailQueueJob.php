<?php

require_once 'vendor/autoload.php';

use Bondif\MailDeliveryService\DBConnection;
use Bondif\MailDeliveryService\QueueManager;
use Phive\Queue\NoItemAvailableException;

class MailQueueJob
{
    private $connection;

    public function __construct()
    {
        $this->connection = DBConnection::getConnection();
    }

    public function run()
    {
        $queue = QueueManager::getQueue();

        for (;;) {
            try {
                $requestId = $queue->pop();

                try {
                    $mailRecord = $this->getMailByRequestId($requestId);
                } catch (\Exception $e) {
                    echo "Database : Can't get record with request_id = $requestId\n";
                }

                try {
                    $this->sendEmail($mailRecord['sender'], $mailRecord['recipient'], $mailRecord['message']);
                    $this->updateStatus($requestId, 1);

                    echo "MailQueue : Mail with request_id = $requestId was sent successfully\n";
                } catch (\Exception $e) {
                    echo "MailQueue : Can't send mail with request_id = $requestId\n";
                }

            } catch (NoItemAvailableException $e) {
                echo "No Item Available\n";
                sleep(5);
            }
        }

    }

    private function sendEmail($sender, $recipient, $message)
    {

    }

    private function getMailByRequestId($requestId)
    {
        $sql = "select * from mails where request_id = ?";
        $statement = $this->connection->prepare($sql);
        $statement->bind_param('s', $requestId);
        $statement->execute();
        $result = $statement->get_result();

        if ($result) {
            return $result->fetch_assoc();
        } else {
            throw new \Exception("DB Error: " . $this->connection->error);
        }
    }

    private function updateStatus($requestId, int $newStatus)
    {
        $sql = "update mails set status = $newStatus where request_id = ?";
        $statement = $this->connection->prepare($sql);
        $statement->bind_param('s', $requestId);
        $statement->execute();
    }
}

$mailQueueJob = new MailQueueJob();
$mailQueueJob->run();