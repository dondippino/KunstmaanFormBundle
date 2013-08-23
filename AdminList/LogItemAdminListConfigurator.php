<?php

namespace Kunstmaan\FormBundle\AdminList;

use Doctrine\ORM\EntityManager;

use Kunstmaan\FormBundle\Form\LogItemAdminType;
use Kunstmaan\AdminListBundle\AdminList\FilterType\ORM;
use Kunstmaan\AdminListBundle\AdminList\Configurator\AbstractDoctrineORMAdminListConfigurator;
use Kunstmaan\AdminBundle\Helper\Security\Acl\AclHelper;

/**
 * The admin list configurator for LogItem
 */
class LogItemAdminListConfigurator extends AbstractDoctrineORMAdminListConfigurator
{

    /**
     * @param EntityManager $em        The entity manager
     * @param AclHelper     $aclHelper The acl helper
     */
    public function __construct(EntityManager $em, AclHelper $aclHelper = null)
    {
        parent::__construct($em, $aclHelper);
        $this->setAdminType(new LogItemAdminType());
    }

    /**
     * Configure the visible columns
     */
    public function buildFields()
    {
        $this->addField('exporterName', 'exporterName', true);
        $this->addField('exportableId', 'exportableId', true);
        $this->addField('exportableName', 'exportableName', true);
        $this->addField('createdAt', 'createdAt', true);
        $this->addField('invoker', 'invoker', true);
    }

    /**
     * Build filters for admin list
     */
    public function buildFilters()
    {
        $this->addFilter('exporterName', new ORM\StringFilterType('exporterName'), 'Exportername');
        $this->addFilter('exportableId', new ORM\StringFilterType('exportableId'), 'Exportableid');
        $this->addFilter('exportableName', new ORM\StringFilterType('exportableName'), 'Exportablename');
        $this->addFilter('createdAt', new ORM\DateFilterType('createdAt'), 'Createdat');
        $this->addFilter('invoker', new ORM\StringFilterType('invoker'), 'Invoker');
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
        return 'LogItem';
    }

}