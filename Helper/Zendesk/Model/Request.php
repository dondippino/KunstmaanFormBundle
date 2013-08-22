<?php

namespace Kunstmaan\FormBundle\Helper\Zendesk\Model;

use JMS\Serializer\Annotation as Serializer;

class Request extends BaseModel
{

    /**
     * The value of the subject field for this request
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