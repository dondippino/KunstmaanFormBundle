<?php

namespace Kunstmaan\FormBundle\Helper\Services;


use Kunstmaan\FormBundle\Entity\FormSubmission;

// TODO: How to implement a mechanism for monitoring if an export has already happened or not.


/**
 * Interface that can export the data of a Form to any service/mechanism.
 *
 * Class FormExporterInterface
 * @package Kunstmaan\FormBundle\Helper\Services
 */
interface FormExporterInterface
{
    /**
     * Do the actual exporting.
     *
     * @param FormSubmission $submission
     *
     * @return void
     */
    public function export(FormSubmission $submission);
}