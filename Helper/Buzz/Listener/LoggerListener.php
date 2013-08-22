<?php

namespace Kunstmaan\FormBundle\Helper\Buzz\Listener;

use Buzz\Listener\ListenerInterface;
use Buzz\Message\MessageInterface;
use Buzz\Message\RequestInterface;

class LoggerListener implements ListenerInterface
{
    private $logger;
    private $prefix;
    private $startTime;

    public function __construct($logger, $prefix = null)
    {
        if (!is_callable($logger)) {
            throw new \InvalidArgumentException('The logger must be a callable.');
        }

        $this->logger = $logger;
        $this->prefix = $prefix;
    }

    public function preSend(RequestInterface $request)
    {
        $this->startTime = microtime(true);
    }

    public function postSend(RequestInterface $request, MessageInterface $response)
    {
        $seconds = microtime(true) - $this->startTime;

        $this->log('REQUEST:');
        $this->log(sprintf('Sent "%s %s%s" in %dms', $request->getMethod(), $request->getHost(), $request->getResource(), round($seconds * 1000)));
        $this->log(sprintf('Headers %s', $this->headersToString($request->getHeaders())));
        $this->log($request->getContent());

        $this->log('RESPONSE:');
        $this->log(sprintf('Headers %s', $this->headersToString($response->getHeaders())));
        $this->log($response->getContent());
    }

    private function log($message)
    {
        call_user_func($this->logger, $this->prefix . $message);
    }

    private function headersToString($headers)
    {
        return implode(', ', $headers);
    }
}