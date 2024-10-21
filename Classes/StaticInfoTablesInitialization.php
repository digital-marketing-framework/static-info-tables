<?php

namespace DigitalMarketingFramework\Typo3\StaticInfoTables;

use DigitalMarketingFramework\Core\Initialization;
use DigitalMarketingFramework\Core\Registry\RegistryInterface;
use DigitalMarketingFramework\Typo3\StaticInfoTables\ConfigurationDocument\Discovery\StaticInfoSystemConfigurationDocumentDiscovery;
use DigitalMarketingFramework\Typo3\StaticInfoTables\GlobalConfiguration\Schema\StaticInfoTablesGlobalConfigurationSchema;
use DigitalMarketingFramework\Typo3\StaticInfoTables\Service\StaticInfoService;

class StaticInfoTablesInitialization extends Initialization
{
    public function __construct(
        protected StaticInfoService $staticInfoService,
    ) {
        parent::__construct(
            'static-info-tables',
            '1.0.0',
            'dmf_static_info_tables',
            new StaticInfoTablesGlobalConfigurationSchema()
        );
    }

    public function initPlugins(string $domain, RegistryInterface $registry): void
    {
        parent::initPlugins($domain, $registry);

        $registry->registerStaticConfigurationDocumentDiscovery(
            $registry->createObject(StaticInfoSystemConfigurationDocumentDiscovery::class, [$registry, $this->staticInfoService])
        );
    }
}
