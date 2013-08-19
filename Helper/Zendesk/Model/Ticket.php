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
    protected $id;

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
    protected $externalId;

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

    /**
     * The subject of the ticket.
     *
     * @var string $subject
     *
     * @Serializer\Type("string")
     */
    protected $subject;

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setSubject($value)
    {
        $this->subject = $value;

        return $this;
    }

    /**
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * The tags. Array or space separated string.
     *
     * @var string $tags
     *
     * @Serializer\Type("string")
     */
    protected $tags;

    /**
     * @param $value
     *
     * @return $this
     */
    public function setTags($value)
    {
        if (is_array($value)) {
            $this->tags = implode(' ', $value);
        } else {
            $this->tags = $value;
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * First comment on the Ticket. Placed by the
     *
     * @var string $description
     *
     * @Serializer\Type("string")
     */
    protected $description;

    /**
     * @param $value
     *
     * @return $this
     */
    public function setDescription($value)
    {
        $this->description = $value;

        return $this;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * The original recipient e-mail address of the ticket.
     *
     * @var string $description
     *
     * @Serializer\Type("string")
     */
    protected $recipient;

    /**
     * @return string
     */
    public function getRecipient()
    {
        return $this->recipient;
    }

    /**
     * The user who requested this ticket
     *
     * @var string $requesterId
     *
     * @Serializer\SerializedName("requester_id")
     * @Serializer\Type("integer")
     */
    protected $requesterId;

    /**
     * @return string
     */
    public function getRequesterId()
    {
        return $this->requesterId;
    }

    /**
     * @param $value
     *
     * @return $this
     */
    public function setRequesterId($value)
    {
        $this->requesterId = $value;

        return $this;
    }
}