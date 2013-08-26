<?php

namespace Kunstmaan\FormBundle\Entity\Export;

use Doctrine\ORM\Mapping as ORM;

/**
 * ExpoterSetting
 *
 * @ORM\Table(name="kuma_form_export_exporter_setting")
 * @ORM\Entity()
 */
class ExporterSetting extends \Kunstmaan\AdminBundle\Entity\AbstractEntity
{
    /**
     * @var string
     *
     * @ORM\Column(name="exporter_name", type="string", length=255)
     */
    private $exporterName;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="api_limit_triggered_at", type="datetime", nullable=true)
     */
    private $apiLimitTriggeredAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="enable_api_at", type="datetime", nullable=true)
     */
    private $enableApiAt;


    /**
     * Set exporterName
     *
     * @param string $exporterName
     * @return ExporterSetting
     */
    public function setExporterName($exporterName)
    {
        $this->exporterName = $exporterName;
    
        return $this;
    }

    /**
     * Get exporterName
     *
     * @return string 
     */
    public function getExporterName()
    {
        return $this->exporterName;
    }

    /**
     * Set apiLimitTriggeredAt
     *
     * @param \DateTime $apiLimitTriggeredAt
     * @return ExporterSetting
     */
    public function setApiLimitTriggeredAt($apiLimitTriggeredAt)
    {
        $this->apiLimitTriggeredAt = $apiLimitTriggeredAt;
    
        return $this;
    }

    /**
     * Get apiLimitTriggeredAt
     *
     * @return \DateTime 
     */
    public function getApiLimitTriggeredAt()
    {
        return $this->apiLimitTriggeredAt;
    }

    /**
     * Set enableApiAt
     *
     * @param \DateTime $enableApiAt
     * @return ExporterSetting
     */
    public function setEnableApiAt($enableApiAt)
    {
        $this->enableApiAt = $enableApiAt;
    
        return $this;
    }

    /**
     * Get enableApiAt
     *
     * @return \DateTime 
     */
    public function getEnableApiAt()
    {
        return $this->enableApiAt;
    }
}
