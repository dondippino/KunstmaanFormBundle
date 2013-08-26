<?php

namespace Kunstmaan\FormBundle\Helper\Export;


use Doctrine\ORM\EntityManager;
use Kunstmaan\FormBundle\Entity\Export\ExporterSetting;

abstract class TimeoutableBase implements TimeoutableInterface
{
    /** @var EntityManager */
    protected $entityManager;

    public function setEntityManager($entityManager)
    {
        $this->entityManager = $entityManager;

        return $this;
    }


    public function removeTimeout()
    {
        $setting = $this->findOrCreateSetting();

        $setting->setApiLimitTriggeredAt(null);
        $setting->setEnableApiAt(null);

        $this->entityManager->flush($setting);
    }

    public function setTimeoutPeriodInSeconds($periodInSeconds)
    {
        $setting = $this->findOrCreateSetting();

        $now = new \DateTime('now');
        $setting->setApiLimitTriggeredAt($now);
        $setting->setEnableApiAt(new \DateTime('+'.$periodInSeconds.' seconds'));

        $this->entityManager->flush($setting);
    }

    public function isInTimeout()
    {
        $now = new \DateTime('now');
        $setting = $this->findOrCreateSetting();

        if (is_null($setting->getEnableApiAt())) {
            return false;
        }

        if ($setting->getEnableApiAt() >= $now) {
            return true;
        } else {
            $this->removeTimeout();
            return false;
        }
    }

    private function findOrCreateSetting()
    {
        $exportLogItemRepo = $this->entityManager->getRepository('KunstmaanFormBundle:Export\ExporterSetting');
        $setting = $exportLogItemRepo->findOneBy(array('exporterName' => $this->getName()));

        if (!is_null($setting)) {
            return $setting;
        }

        $new = (new ExporterSetting())->setExporterName($this->getName());
        $this->entityManager->persist($new);

        return $new;
    }

    abstract function getName();

}