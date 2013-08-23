<?php

namespace Kunstmaan\FormBundle\Helper\Export;

/**
 * Simple exporter that exports to a logger.
 */
class LogFormExporter implements FormExporterInterface
{
    public function export(FormExportableInterface $submission)
    {
        var_dump('LogFormExporter::export');

        return true;
    }

    public function getName()
    {
        return 'log';
    }
}
