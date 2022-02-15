<?php

namespace Bondif\MailDeliveryService;


class DBConnection
{
    public static function getConnection(): \mysqli
    {
        $host = 'db';
        $port = '3306';
        $username = 'user';
        $password = 'strong_password';
        $database = 'mail_delivery_service';

        $connection = mysqli_connect($host, $username, $password, $database, $port);

        if ($connection->connect_errno) {
            echo "Failed to connect to MySQL: " . $connection->connect_error;
            exit();
        }

        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

        return $connection;
    }
}