<?php

namespace Kunstmaan\FormBundle\Helper\Zendesk\Model;

use JMS\Serializer\Annotation as Serializer;

class BaseModel
{
    /**
     * Automatically assigned when creating requests.
     *
     * @var integer $id
     *
     * @Serializer\Type("integer")
     */
    protected $id;

    /**
     * The API url of this request.
     *
     * @var string $url
     *
     * @Serializer\Type("string")
     */
    protected $url;

    /**
     * @return number the $id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param number $id
     *
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string The $url
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param string $url
     *
     * @return $this
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }
}