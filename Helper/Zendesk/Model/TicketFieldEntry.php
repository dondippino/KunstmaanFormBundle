<?php

namespace Kunstmaan\FormBundle\Helper\Zendesk\Model;

use JMS\Serializer\Annotation as Serializer;

/**
 * Actual filled in field.
 */
class TicketFieldEntry extends BaseModel
{

    /**
     * The actual value.
     *
     * @var string $value
     *
     * @Serializer\Type("string")
     */
    private $value;

    /**
     * @param $value
     * @return $this
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    public function getValue()
    {
        return $this->value;
    }
}