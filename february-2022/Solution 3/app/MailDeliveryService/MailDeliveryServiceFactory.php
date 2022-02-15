<?php

namespace App\MailDeliveryService;

use App\MailDeliveryService\Exceptions\MailDeliveryServiceException;
use Illuminate\Support\Arr;

class MailDeliveryServiceFactory
{
    private static array $mailDeliveryServiceList = [];

    private function __construct()
    {

    }

    /**
     * @throws MailDeliveryServiceException
     */
    public static function getInstance(string $serviceClassName): MailDeliveryServiceContract
    {
        if (!Arr::exists(class_implements($serviceClassName) ?? [], MailDeliveryServiceContract::class)) {
            throw new MailDeliveryServiceException(sprintf(
                "%s does not implements MailDeliveryServiceContract",
                $serviceClassName
            ));
        }

        if (!Arr::exists(static::$mailDeliveryServiceList, $serviceClassName)) {
            static::$mailDeliveryServiceList[$serviceClassName] = new $serviceClassName;
        }

        return static::$mailDeliveryServiceList[$serviceClassName];
    }
}
