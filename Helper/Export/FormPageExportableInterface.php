<?php

namespace Kunstmaan\FormBundle\Helper\Export;


use Kunstmaan\FormBundle\Entity\FormSubmissionField;

interface FormPageExportableInterface
{
    /**
     * @return array An array containing the keys and their values.
     */
    public function getKeysAndValues();

    /**
     * Only has to be implemented if your FormSubmission does not contain the keys on the fields needed by your exporter.
     *
     * @param string $language The language
     *
     * @return array A map containing the key as the key and a regex for the field that would match with this key.
     */
    public function getKeyGuessFieldNameMap($language);
}
