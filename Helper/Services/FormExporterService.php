<?php

namespace Kunstmaan\FormBundle\Helper\Services;


use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Kunstmaan\FormBundle\Entity\Export\LogItem;
use Kunstmaan\FormBundle\Entity\FormSubmission;
use Kunstmaan\FormBundle\Helper\Export\FormExportableInterface;
use Kunstmaan\FormBundle\Helper\Export\FormExporterInterface;
use Kunstmaan\FormBundle\Helper\Export\ZendeskFormExporter;
use Kunstmaan\FormBundle\Helper\Zendesk\ZendeskApiClient;
use Kunstmaan\FormBundle\Repository\Export\LogItemRepository;
use Kunstmaan\UtilitiesBundle\Helper\ClassLookup;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;

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

    /** @var EntityManager */
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
                    // But this would make it very hard to configure
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

    /**
     * @param int $limit The limit to attempt.
     * @param array $exporterNames The list of exporters to try. When empty try all.
     *
     * @return array Array containing the service as the key and the number of handled FormSubmissions as the value.
     */
    public function exportBacklog($limit = 0, array $exporterNames = array())
    {
        /** @var $logItemRepository LogItemRepository */
        $logItemRepository = $this->entityManager->getRepository('Kunstmaan\FormBundle\Entity\Export\LogItem');
        /** @var $formSubmissionRepository EntityRepository  */
        $formSubmissionRepository = $this->entityManager->getRepository('KunstmaanFormBundle:FormSubmission');

        if (!empty($exporters)) {
            $exporters = $this->findExportersByArray($exporterNames);
        } else {
            $exporters = $this->exporters;
        }

        $log = array();
        foreach($exporters as $exporter) {
            $log[$exporter->getName()] = 0;

            $queryBuilder = $formSubmissionRepository->createQueryBuilder('fs');
            $queryBuilder->select('fs');

            $fs = new FormSubmission(); // Silly way to get the class name.
            $queryBuilder = $logItemRepository->modifyQueryBuilderToFilterOutExportedEntities($queryBuilder, 'id', ClassLookup::getClass($fs), $exporter->getName());
            $queryBuilder->orderBy('fs.id', 'asc');

            if ($limit > 0) {
                $queryBuilder->setMaxResults($limit);
            }

            $q = $queryBuilder->getQuery();
            foreach($q->execute() as $formSubmission) {
                $this->exportSingleExportableForExporter($formSubmission, $exporter->getName(), 'command');
            }

            // TODO: For every exporter look for the first $limit FormSubmission records not exported records.

            // TODO: For every FormSubmission call the exporter and check if it worked.
            // $this->createSuccesfulLogForExportable

            // TODO: If it worked, save it as having worked + add to the counter.
            $log[$exporter->getName()] += 1;
        }

        // kuma_form_exporter_log
        // - id
        // - exporter_name
        // - exportable_id
        // - exportable_name
        // - created_at
        // - invoker (backlog or direct)

        // kuma_form_exporter_service_info
        // - contains for every service the timeout info if any.




    }

    private function createSuccesfulLogForExportable(FormExportableInterface $exportableForm, FormExporterInterface $exporter, $invoker)
    {
        $item = (new LogItem())->setExportableName(ClassLookup::getClass($exportableForm))
                               ->setExporterName($exporter->getName())
                               ->setExportableId($exportableForm->getIdentifier())
                               ->setInvoker($invoker);

        $this->entityManager->persist($item);
        $this->entityManager->flush($item);
    }

    public function export(FormExportableInterface $exportableForm)
    {
        // TODO: Check if it's been exported already. (via a custom Entity)

        // TODO LATER: Check if the API limiter has been hit or not. (throws an exception if the limiter is hit)

        foreach ($this->exporters as $formExporter) {
            $this->exportSingleExportableForExporter($exportableForm, $formExporter->getName(), 'direct');
        }
    }


    private function exportSingleExportableForExporter(FormExportableInterface $exportableForm, $exporterName, $invoker)
    {
        $exporter = $this->findExporterByName($exporterName);
        // TODO: Maybe introduce a transaction?
        if ($exporter->export($exportableForm)) {
            $this->createSuccesfulLogForExportable($exportableForm, $exporter, $invoker);
            return true;
        }
        return false;
    }

    /**
     * @param string $exporterName
     * @return FormExporterInterface|null
     */
    private function findExporterByName($exporterName)
    {
        foreach($this->exporters as $name => $service) {
            if ($name  == $exporterName) {
                return $service;
            }
        }

        return null;
    }

    /**
     * @param array $exporterNames
     * @return FormExporterInterface[]
     */
    private function findExportersByArray(array $exporterNames)
    {
        $ret = array();
        foreach($exporterNames as $name) {
            $ret[] = $this->findExporterByName($name);
        }

        return array_filter($ret);
    }

}
