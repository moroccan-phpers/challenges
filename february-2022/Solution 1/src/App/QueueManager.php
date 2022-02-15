<?php

namespace Bondif\MailDeliveryService;


use Phive\Queue\RedisQueue;
use Redis;

class QueueManager
{
    public static function getQueue(): RedisQueue
    {
        $redis = new Redis();

        $redis->connect('redis');

        return new RedisQueue($redis);
    }
}