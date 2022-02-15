<?php

require '../vendor/autoload.php';

$connection = \Bondif\MailDeliveryService\DBConnection::getConnection();
$mailDeliveryService = new \Bondif\MailDeliveryService\MailDeliveryService($connection);

$mailDeliveryService->run();