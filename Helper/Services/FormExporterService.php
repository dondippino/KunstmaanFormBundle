<?php

namespace Kunstmaan\FormBundle\Helper\Services;


use Kunstmaan\FormBundle\Helper\Export\FormExportableInterface;
use Kunstmaan\FormBundle\Helper\Export\FormExporterInterface;
use Kunstmaan\FormBundle\Helper\Export\ZendeskFormExporter;
use Kunstmaan\FormBundle\Helper\Zendesk\ZendeskApiClient;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;

// TODO: Provide a way for a piece of code to loop all formsubmissions and check which ones aren't sent.
//       This will also need a throttler so you can define how many calls are allowed per service.
// TODO: Write command that finds all FormSubmissions and passes them to this service.
//       This service will check if the form can be submitted or not.
//       It'll throw a ApiRateLimiterHit exception when the rate has been hit.
//       This will cause all FormSubmissions that depend on this service to be tried at a later time.

/**
 * Service will export a single FormExportableInterface if needed.
 */
class FormExporterService
{
    /** @var FormExporterInterface[] */
    protected $exporters;

    protected $serializer;

    public function setSerializer($value)
    {
        $this->serializer = $value;
    }

    protected $logger;
    public function setLogger($value)
    {
        $this->logger = $value;
    }

    protected $entityManager;
    public function setEntityManager($entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function addExporter(FormExporterInterface $exporter)
    {
        if (!isset($this->exporters) or is_null($this->exporters)) {
            $this->exporters = array();
        }

        if (array_key_exists($exporter->getName(), $this->exporters)) {
            throw new \LogicException(
                sprintf('FormExporterService already contains the \'%s\' service. Please check if you aren\'t loading the same exporter twice.',
                    $exporter->getName()
                )
            );
        }

        $this->exporters[$exporter->getName()] = $exporter;
    }

    /**
     * Function is called with the available configuration.
     *
     * If nothing is defined no internal exporter services will be instantiated.
     *
     * @param array $exporterConfiguration
     * @throws \Symfony\Component\DependencyInjection\Exception\InvalidArgumentException when you try to load an exporter that's unknown.
     */
    public function createExporters(array $exporterConfiguration)
    {
        foreach ($exporterConfiguration as $exporterName => $config) {

            switch ($exporterName) {
                case 'zendesk':
                    // TODO: Better way where the container does this work.
                    // I don't want to hardcode the config to the services.
                    // One way is to expose all exporters as services.
                    $zendeskExporter = new ZendeskFormExporter();
                    $apiClient = new ZendeskApiClient();
                    $apiClient->setApiKey($config['api_key']);
                    $apiClient->setDomain($config['domain']);
                    $apiClient->setLogin($config['login']);
                    $apiClient->setSerializer($this->serializer);
                    $apiClient->setLogger($this->logger);
                    $zendeskExporter->setEntityManager($this->entityManager);
                    $zendeskExporter->setApiClient($apiClient);
                    $this->exporters['zendesk'] = $zendeskExporter;
                    break;
                default:
                    throw new InvalidArgumentException(
                        sprintf('Exporter \'%s\' is not known. Custom exporters should be regular services tagged with the \'kunstmaan_form.exporter\' tag.', $exporterName)
                    );
            }
        }
    }


    public function export(FormExportableInterface $exportableForm)
    {
        // TODO: Check if it's been exported already. (via a custom Entity)

        // TODO LATER: Check if the API limiter has been hit or not. (throws an exception if the limiter is hit)

        foreach ($this->exporters as $formExporter) {
            $formExporter->export($exportableForm);
        }
    }

}
