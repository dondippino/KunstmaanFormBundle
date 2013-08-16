<?php

namespace Kunstmaan\FormBundle\Helper\Export;

/**
 * Interface that can export the data of a Form to any service/mechanism.
 */
interface FormExporterInterface
{
    /**
     * Do the actual exporting.
     *
     * @param FormExportableInterface $exportableForm
     *
     * @return bool True when OK. False when failed.
     */
    public function export(FormExportableInterface $exportableForm);

    /**
     * @return mixed The name of the service. This is used in the configuration etc.
     */
    public function getName();
}