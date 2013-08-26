<?php

namespace Kunstmaan\FormBundle\Helper\Export;


use Kunstmaan\FormBundle\Entity\FormSubmissionField;

interface FormPageExportableInterface
{
    /**
     * Return the keys and values that are present on the FormPage instance.
     *
     * An exporter can contain keys and values that are present on the form page itself.
     * For example you could return the administrative email subject as the subject for all formsubmissions here.
     * You could also return the title of the page so you have an idea from which FormPage the FormSubmission originated.
     *
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

    /**
     * You can tweak certain values before they are exported.
     *
     * This is called when all fields and values have been extracted from the FormSubmission and FormPage.
     *
     * @param array $fields
     * @return array
     */
    public function tweakAllKeysAndValues(array $fields);
}
