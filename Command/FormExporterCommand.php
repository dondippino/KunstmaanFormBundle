<?php

namespace Kunstmaan\FormBundle\Command;

use Kunstmaan\FormBundle\Helper\Services\FormExporterService;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;


class FormExporterCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        parent::configure();

        $this->setName('kuma:forms:export')
            ->setDescription('Export form submissions through your defined exporters.')
            ->setHelp('Sends out an email to users that there are new codes for their bank.')
            ->addOption('limit', 'l', InputOption::VALUE_NONE, 'Max items sending in one batch (0 for no limits, 1 by default)');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $limit = $input->getOption('limit');

        if (is_null($limit) || !is_numeric($limit) || $limit < 0) {
            $output->writeln(sprintf('<error>No or invalid limit given: %s, use limit=0 to force no limit</error>', $limit));
            return 1;
        }

        /** @var $service FormExporterService */
        $service = $this->getContainer()->get('kunstmaan_form.exporter_service');
        $log = $service->exportBacklog($limit);

        $logString = '';
        foreach ($log as $serviceName => $count) {
            $logString = $logString.$serviceName.":".$count."\n";
        }

        $output->write($logString);

        return 0;
    }
}
