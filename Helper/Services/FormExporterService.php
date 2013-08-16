<?php

namespace Kunstmaan\FormBundle\Helper\Services;


use Gedmo\Exception\FeatureNotImplementedException;
use Kunstmaan\FormBundle\Entity\FormSubmission;

// TODO: Provide a way for a piece of code to loop all formsubmissions and check which ones aren't sent.
//       This will also need a throttler so you can define how many calls are allowed per service.

/**
 * Does all the needed exporting for a single form.
 *
 * Class FormExporterService
 * @package Kunstmaan\FormBundle\Helper\Services
 */
class FormExporterService
{
    protected $config;

    public function setConfig($config)
    {
        $this->config = $config;
    }

    public function export(FormSubmission $submission)
    {
        $formExporters = $this->findHandlersForSubmission($submission);

        foreach ($formExporters as $formExporter) {
            $formExporter->export($submission);
        }
    }

    /**
     * Wires up the configuration with the exporters.
     *
     * @param FormSubmission $submission
     *
     * @return FormExporterInterface[]
     */
    private function findHandlersForSubmission(FormSubmission $submission)
    {
        $ret = array();

        // TODO: Actually implement this based on the config.
        die($this->config);

        return array(new ZendeskFormExporter());
    }
}
