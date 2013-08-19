<?php

namespace Kunstmaan\FormBundle\Helper\Zendesk\Model;

use JMS\Serializer\Annotation as Serializer;

class Ticket
{

    /**
     * Will be automatically assigned when creating a ticket.
     *
     * @var integer $id
     *
     * @Serializer\Type("integer")
     */
    private $id;

    public function getID()
    {
        return $this->id;
    }


    /**
     * Will be automatically assigned when creating a ticket. The URL where the ticket is visible.
     *
     * @var string $url
     *
     * @Serializer\Type("string")
     */
    private $url;

    public function getURL()
    {
        return $this->url;
    }

    /**
     * A custom reference that can be assigned to a ticket.
     *
     * @var string $externalId
     *
     * @Serializer\SerializedName("external_id")
     * @Serializer\Type("string")
     */
    private $externalId;

    /**
     * @return string
     */
    public function getExternalID()
    {
        return $this->externalId;
    }

    /**
     * @param $value
     *
     * @return $this
     */
    public function setExternalID($value)
    {
        $this->externalId = $value;

        return $this;
    }


}