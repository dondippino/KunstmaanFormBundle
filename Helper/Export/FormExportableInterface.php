<?php

namespace Kunstmaan\FormBundle\Helper\Export;


use Kunstmaan\FormBundle\Entity\FormSubmissionField;

/**
 * Interface that exposes all the variables that are necessary for a FormExporterInterface to do its work.
 */
interface FormExportableInterface
{

    /**
     * The identifier that's used to track if the form has already been exported or not.
     *
     * @return mixed
     */
    public function getIdentifier();

    /**
     * @return FormSubmissionField[] Array containing the field name in the key and the value for the field as the value.
     */
    public function getFieldsForExport();

}
