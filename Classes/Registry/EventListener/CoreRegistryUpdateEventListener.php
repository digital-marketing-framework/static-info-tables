<?php

namespace DigitalMarketingFramework\Typo3\StaticInfoTables\Registry\EventListener;

use DigitalMarketingFramework\Typo3\Core\Registry\EventListener\AbstractCoreRegistryUpdateEventListener;
use DigitalMarketingFramework\Typo3\StaticInfoTables\Service\StaticInfoService;
use DigitalMarketingFramework\Typo3\StaticInfoTables\StaticInfoTablesInitialization;

class CoreRegistryUpdateEventListener extends AbstractCoreRegistryUpdateEventListener
{
    public function __construct(StaticInfoService $staticInfoService)
    {
        parent::__construct(new StaticInfoTablesInitialization($staticInfoService));
    }
}
