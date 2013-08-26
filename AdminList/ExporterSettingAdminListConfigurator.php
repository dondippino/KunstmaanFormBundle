<?php

namespace Kunstmaan\FormBundle\AdminList;

use Doctrine\ORM\EntityManager;

use Kunstmaan\FormBundle\Form\ExpoterSettingAdminType;
use Kunstmaan\AdminListBundle\AdminList\FilterType\ORM;
use Kunstmaan\AdminListBundle\AdminList\Configurator\AbstractDoctrineORMAdminListConfigurator;
use Kunstmaan\AdminBundle\Helper\Security\Acl\AclHelper;

/**
 * The admin list configurator for ExporterSetting
 */
class ExporterSettingAdminListConfigurator extends AbstractDoctrineORMAdminListConfigurator
{

    /**
     * @param EntityManager $em        The entity manager
     * @param AclHelper     $aclHelper The acl helper
     */
    public function __construct(EntityManager $em, AclHelper $aclHelper = null)
    {
        parent::__construct($em, $aclHelper);
        $this->setAdminType(new ExpoterSettingAdminType());
    }

    /**
     * Configure the visible columns
     */
    public function buildFields()
    {
        $this->addField('exporterName', 'exporterName', true);
        $this->addField('apiLimitTriggeredAt', 'apiLimitTriggeredAt', true);
        $this->addField('enableApiAt', 'enableApiAt', true);
    }

    /**
     * Build filters for admin list
     */
    public function buildFilters()
    {
        $this->addFilter('exporterName', new ORM\StringFilterType('exporterName'), 'Exportername');
        $this->addFilter('apiLimitTriggeredAt', new ORM\DateFilterType('apiLimitTriggeredAt'), 'Apilimittriggeredat');
        $this->addFilter('enableApiAt', new ORM\DateFilterType('enableApiAt'), 'Enableapiat');
    }

    /**
     * Get bundle name
     *
     * @return string
     */
    public function getBundleName()
    {
        return 'KunstmaanFormBundle';
    }

    /**
     * Get entity name
     *
     * @return string
     */
    public function getEntityName()
    {
        return 'ExpoterSetting';
    }

}