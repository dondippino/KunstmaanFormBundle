<?php

namespace Kunstmaan\FormBundle\Entity;

use DateTime;

use Doctrine\ORM\EntityManager;
use Kunstmaan\FormBundle\Helper\Export\FormExportableInterface;
use Kunstmaan\NodeBundle\Entity\Node;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * The form submission
 *
 * @ORM\Entity
 * @ORM\Table(name="kuma_form_submissions")
 * @ORM\HasLifecycleCallbacks()
 */
class FormSubmission implements FormExportableInterface
{
    /**
     * This id of the form submission
     *
     * @ORM\Id
     * @ORM\Column(type="bigint")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * The ip address which created this form submission
     *
     * @ORM\Column(type="string", name="ip_address")
     */
    protected $ipAddress;

    /**
     * Link to the node of the form which created this form submission
     *
     * @ORM\ManyToOne(targetEntity="Kunstmaan\NodeBundle\Entity\Node")
     * @ORM\JoinColumn(name="node_id", referencedColumnName="id")
     */
    protected $node;

    /**
     * The language of the form submission
     *
     * @ORM\Column(type="string")
     */
    protected $lang;

    /**
     * The date when the form submission was created
     *
     * @ORM\Column(type="datetime")
     */
    protected $created;

    /**
     * The extra fields with their value, which where configured on the form which created this submission
     *
     * @ORM\OneToMany(targetEntity="FormSubmissionField", mappedBy="formSubmission")
     */
    protected $fields;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->fields = new ArrayCollection();
        $this->setCreated(new DateTime());
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set id
     *
     * @param string $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Get the ip address which submitted this form submission
     *
     * @return string
     */
    public function getIpAddress()
    {
        return $this->ipAddress;
    }

    /**
     * Set the ip address
     *
     * @param string $ipAddress
     */
    public function setIpAddress($ipAddress)
    {
        $this->ipAddress = $ipAddress;
    }

    /**
     * Get the node of the form which created this form submission
     *
     * @return Node
     */
    public function getNode()
    {
        return $this->node;
    }

    /**
     * Set the node of the form which created this form submission
     *
     * @param Node $node
     */
    public function setNode($node)
    {
        $this->node = $node;
    }

    /**
     * Sets the language of this form submission
     *
     * @param string $lang
     */
    public function setLang($lang)
    {
        $this->lang = $lang;
    }

    /**
     * Get the language of this form submission
     *
     * @return string
     */
    public function getLang()
    {
        return $this->lang;
    }

    /**
     * Set the date when the form submission was created
     *
     * @param datetime $created
     */
    public function setCreated($created)
    {
        $this->created = $created;
    }

    /**
     * Get the date when this form submission was created
     *
     * @return datetime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Returns the list of fields with their values
     *
     * @return FormSubmissionField[];
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * A string representation of this form submission
     *
     * @return string;
     */
    public function __toString()
    {
        return "FormSubmission";
    }



    public function getIdentifier()
    {
        return $this->getId();
    }

    // TODO: Make a method on the FormPage that'll guess the fields based on the names.
    //       We need this in order to upload the older forms.
    //       Perhaps we need multiple strategies for mapping the FormFields to the regular fields.

    public function getFieldsForExport(EntityManager $em)
    {
        $values = array('language' => $this->getLang());

        // Loop the fields, try getting their value via getValue, otherwise fall back to the regular string conversion.
        foreach ($this->getFields() as $field) {
            $val = (string)$field;
            if (method_exists($field, 'getValue')) {
                $val = $field->getValue();
            }
            $values[$field->getIdentityKey()] = $val;
        }

        // Fetch the keys and values from the form itself.
        /** @var $entity AbstractFormPage */
        $entity = $this->getNode()->getNodeTranslation($this->getLang())->getRef($em);
        $formKeysValues = $entity->getKeysAndValues();

        // Merge with priority for the FormField submission keys.
        $ret = array_merge($formKeysValues, $values);

        return $ret;
    }

}
