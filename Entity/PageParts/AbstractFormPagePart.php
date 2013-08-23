<?php

namespace Kunstmaan\FormBundle\Entity\PageParts;

use Kunstmaan\UtilitiesBundle\Helper\ClassLookup;
use Kunstmaan\FormBundle\Entity\FormAdaptorInterface;
use Kunstmaan\PagePartBundle\Entity\AbstractPagePart;

use Doctrine\ORM\Mapping as ORM;

/**
 * Abstract version of a form page part
 */
abstract class AbstractFormPagePart extends AbstractPagePart implements FormAdaptorInterface
{

    const ERROR_REQUIRED_FIELD = "field.required";

    /**
     * The label
     *
     * @var string $label
     *
     * @ORM\Column(type="string", nullable=true)
     */
    protected $label;

    /**
     * The key. This is optional. Set it when you need an automated tool to know what field this is.
     *
     * @var string $key
     *
     * @ORM\Column(type="string", nullable=true, name="identity_key")
     */
    protected $identityKey;

    /**
     * Returns a unique id for the current page part
     *
     * @return string
     */
    public function getUniqueId()
    {
        return  str_replace('\\', ':', ClassLookup::getClass($this)) . $this->id; //TODO
    }

    /**
     * Set the label used for this page part
     *
     * @param string $label
     */
    public function setLabel($label)
    {
        $this->label = $label;
    }

    /**
     * Get the label used for this page part
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Set the key for this PagePart.
     *
     * @param string $key
     */
    public function setIdentityKey($key)
    {
        $this->identityKey = $key;
    }

    /**
     * Get the key used for this PagePart.
     *
     * @return string
     */
    public function getIdentityKey()
    {
        return $this->identityKey;
    }

    /**
     * Returns the view used in the backend
     *
     * @return string
     */
    public function getAdminView()
    {
        return "KunstmaanFormBundle:AbstractFormPagePart:admin-view.html.twig";
    }

}
