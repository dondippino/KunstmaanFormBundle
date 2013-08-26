<?php

namespace Kunstmaan\FormBundle\Entity\Export;

use Doctrine\ORM\Mapping as ORM;

/**
 * LogItem
 *
 * @ORM\Table(name="kuma_form_export_log_item")
 * @ORM\Entity(repositoryClass="Kunstmaan\FormBundle\Repository\Export\LogItemRepository")
 */
class LogItem extends \Kunstmaan\AdminBundle\Entity\AbstractEntity
{
    /**
     * @var string
     *
     * @ORM\Column(name="exporter_name", type="string", length=255)
     */
    private $exporterName;

    /**
     * @var string
     *
     * @ORM\Column(name="exportable_id", type="string", length=30)
     */
    private $exportableId;

    /**
     * @var string
     *
     * @ORM\Column(name="exportable_name", type="string", length=255)
     */
    private $exportableName;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @var string
     *
     * @ORM\Column(name="state", type="string", length=255)
     */
    private $state;

    /**
     * @var string
     *
     * @ORM\Column(name="invoker", type="string", length=15)
     */
    private $invoker;

    public function __construct()
    {
        $this->setCreatedAt(new \DateTime());
    }


    /**
     * Set exporterName
     *
     * @param string $exporterName
     * @return LogItem
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
     * Set exportableId
     *
     * @param string $exportableId
     * @return LogItem
     */
    public function setExportableId($exportableId)
    {
        $this->exportableId = $exportableId;
    
        return $this;
    }

    /**
     * Get exportableId
     *
     * @return string 
     */
    public function getExportableId()
    {
        return $this->exportableId;
    }

    /**
     * Set exportableName
     *
     * @param string $exportableName
     * @return LogItem
     */
    public function setExportableName($exportableName)
    {
        $this->exportableName = $exportableName;
    
        return $this;
    }

    /**
     * Get exportableName
     *
     * @return string 
     */
    public function getExportableName()
    {
        return $this->exportableName;
    }

    /**
     * Set state
     *
     * @param string $state
     * @return LogItem
     */
    public function setState($state)
    {
        $this->state = $state;

        return $this;
    }

    /**
     * Get state
     *
     * @return string
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return LogItem
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    
        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime 
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set invoker
     *
     * @param string $invoker
     * @return LogItem
     */
    public function setInvoker($invoker)
    {
        $this->invoker = $invoker;
    
        return $this;
    }

    /**
     * Get invoker
     *
     * @return string 
     */
    public function getInvoker()
    {
        return $this->invoker;
    }
}
