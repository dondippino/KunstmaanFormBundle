<?php

namespace Kunstmaan\FormBundle\Helper\Export;


use Doctrine\ORM\EntityManager;
use Kunstmaan\FormBundle\Entity\FormSubmissionField;

/**
 * Interface that exposes all the variables that are necessary for a FormExporterInterface to do its work.
 */
interface FormExportableInterface
{

    /**
     * The identifier that's used to track if the form submission has already been exported or not.
     *
     * @return string
     */
    public function getIdentifier();

    /**
     * @param EntityManager $em Since we need an EntityManager for the FormSubmission this has to be here.
     *                          Perhaps create an object that wraps this so it's not polluting the interface.
     *
     * @return array Array containing the field name in the key and the value for the field as the value.
     */
    public function getFieldsForExport(EntityManager $em);

    /**
     * This data is used to determine if we are dealing with a stale record which
     * might get handled differently to new records. Perhaps you don't want to
     * automatically mail the customer if the record is too old.
     *
     * @return \DateTime|null Return null if date is unknown. Otherwise return the date.
     */
    public function getCreationDate();

}
