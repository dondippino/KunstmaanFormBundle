<?php

namespace Kunstmaan\FormBundle\Helper\Zendesk;


class ZendeskApiClient
{

    protected $apiKey;

    protected $domain;

    protected $login;

    public function setApiKey($value)
    {
        $this->apiKey = $value;

        return $this;
    }

    public function setDomain($value)
    {
        $this->domain = $value;

        return $this;
    }

    public function setLogin($value)
    {
        $this->login = $value;

        return $this;
    }


    /**
     * Checks if the field has to be created or updated. If so, perform the API call.
     *
     * Keeps an internal storage of the state on the API. This way we don't hit the rate limiter so soon.
     *
     * @param $fieldData
     */
    public function createOrUpdateField($fieldData)
    {

    }

    /**
     * Create the ticket with the data. All fields need to exist already.
     *
     * @param $ticketData
     */
    public function createTicket($ticketData)
    {

    }
}