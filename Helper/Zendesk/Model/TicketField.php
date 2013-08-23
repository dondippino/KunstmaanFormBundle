<?php

namespace Kunstmaan\FormBundle\Helper\Zendesk\Model;

use JMS\Serializer\Annotation as Serializer;


/**
 *
 * @Serializer\ExclusionPolicy("none")
 */
class TicketField extends BaseModel
{

    /**
     * The type of the ticket field
     *
     * @var string $type
     *
     * @Serializer\Type("string")
     */
    public $type;

    /**
     * The title of the ticket field
     *
     * @var string $type
     *
     * @Serializer\Type("string")
     */
    public $title;

    /**
     * If it's required for this field to have a value when updated by agents
     *
     * @var string $required
     *
     * @Serializer\Type("boolean")
     */
    public $required;

    /**
     * A tag value to set for checkbox fields when checked
     *
     * @var string $required
     *
     * @Serializer\Type("string")
     */
    public $tag;

    /**
     * Required and presented for a ticket field of type "tagger"
     *
     * @var string $required
     *
     * @Serializer\SerializedName("custom_field_options")
     * @Serializer\Type("array")
     */
    public $customFieldOptions;

    /**
     * The title of the ticket field when shown to end users
     *
     * @var string $type
     *
     * @Serializer\SerializedName("title_in_portal")
     * @Serializer\Type("string")
     */
    public $titleInPortal;
}