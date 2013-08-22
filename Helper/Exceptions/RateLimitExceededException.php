<?php

namespace Kunstmaan\FormBundle\Helper\Exceptions;

class RateLimitExceededException extends \Exception
{
    protected $cooldownSeconds = 3600;
    protected $service;

    public function __construct($message, $service, $cooldownSeconds)
    {
        parent::__construct($message);
        $this->service = $service;
        $this->cooldownSeconds = $cooldownSeconds;
    }
}