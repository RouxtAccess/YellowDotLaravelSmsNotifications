<?php

namespace RouxtAccess\YellowDotNotifications\Dto;

class YellowDotSmsPayloadDto
{
    public function __construct
    (
        public string $msisdn,
        public string $serviceId,
        public string $text,
        public ?string $transactionId = null,
    )
    {}
}