<?php

namespace DigitalMarketingFramework\Typo3\StaticInfoTables\ConfigurationDocument\Discovery;

use DigitalMarketingFramework\Core\ConfigurationDocument\Discovery\StaticSystemConfigurationDocumentDiscovery;
use DigitalMarketingFramework\Core\Registry\RegistryInterface;
use DigitalMarketingFramework\Core\SchemaDocument\SchemaDocument;
use DigitalMarketingFramework\Core\Utility\ConfigurationUtility;
use DigitalMarketingFramework\Typo3\StaticInfoTables\Service\StaticInfoService;

class StaticInfoSystemConfigurationDocumentDiscovery extends StaticSystemConfigurationDocumentDiscovery
{
    /**
     * @var string
     */
    public const MAP_NAME_ALL = 'all';

    public function __construct(
        RegistryInterface $registry,
        protected StaticInfoService $staticInfoService
    ) {
        parent::__construct($registry);
    }

    protected function getDocumentIdentifier(string $name): string
    {
        return 'SYS:' . ConfigurationUtility::generateUuidForPackage('static-info-tables', $name);
    }

    protected function getNameFromIdentifier(string $identifier): string
    {
        $mapNames = $this->staticInfoService->getAvailableMapNames();
        $mapNames[] = static::MAP_NAME_ALL;

        foreach ($mapNames as $mapName) {
            if ($this->getDocumentIdentifier($mapName) === $identifier) {
                return $mapName;
            }
        }

        return 'UNKNOWN';
    }

    public function getIdentifiers(): array
    {
        $mapNames = $this->staticInfoService->getAvailableMapNames();
        $ids = [];
        if (count($mapNames) > 1) {
            $ids[] = $this->getDocumentIdentifier(static::MAP_NAME_ALL);
        }

        foreach ($mapNames as $mapName) {
            $ids[] = $this->getDocumentIdentifier($mapName);
        }

        return $ids;
    }

    protected function getConfigurationDocumentName(string $identifier): string
    {
        return 'Static Info Tables (' . $this->getNameFromIdentifier($identifier) . ')';
    }

    protected function buildContent(string $identifier, SchemaDocument $schemaDocument): array
    {
        $mapName = $this->getNameFromIdentifier($identifier);

        return $this->staticInfoService->getMapConfigurationDocument($mapName === static::MAP_NAME_ALL ? null : $mapName);
    }
}
