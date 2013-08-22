<?php

namespace Kunstmaan\FormBundle\Helper\Buzz\Listener;

use Buzz\Listener\ListenerInterface;
use Buzz\Message\MessageInterface;
use Buzz\Message\RequestInterface;

class TokenAuthListener implements ListenerInterface
{
    private $username;
    private $password;

    public function __construct($username, $password)
    {
        $this->username = $username;
        $this->password = $password;
    }

    public function preSend(RequestInterface $request)
    {
        $request->addHeader('Authorization: Basic '.$this->username.'/token:'.$this->password);
    }

    public function postSend(RequestInterface $request, MessageInterface $response)
    {
    }
}
