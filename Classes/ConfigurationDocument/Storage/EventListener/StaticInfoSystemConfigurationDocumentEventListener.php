<?php

namespace DigitalMarketingFramework\Typo3\StaticInfoTables\ConfigurationDocument\Storage\EventListener;

use DigitalMarketingFramework\Core\Utility\ConfigurationUtility;
use DigitalMarketingFramework\Typo3\Core\ConfigurationDocument\Storage\EventListener\AbstractSystemConfigurationDocumentEventListener;
use DigitalMarketingFramework\Typo3\Core\Registry\RegistryCollection;
use DigitalMarketingFramework\Typo3\StaticInfoTables\Service\StaticInfoService;

class StaticInfoSystemConfigurationDocumentEventListener extends AbstractSystemConfigurationDocumentEventListener
{
    /**
     * @var string
     */
    public const MAP_NAME_ALL = 'all';

    public function __construct(
        RegistryCollection $registryCollection,
        protected StaticInfoService $staticInfoService,
    ) {
        parent::__construct($registryCollection);
    }

    protected function getDocumentIdentifier(string $name): string
    {
        return 'SYS:' . ConfigurationUtility::generateUuidForPackage('static-info-tables', $name);
    }

    /**
     * @return array<string>
     */
    protected function getIdentifiers(): array
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

    protected function getDocument(string $documentIdentifier, bool $metaDataOnly = false): ?string
    {
        $configuration = null;
        $metaData = null;
        if ($documentIdentifier === $this->getDocumentIdentifier(static::MAP_NAME_ALL)) {
            $metaData = $this->buildMetaData('Static Info Tables (' . static::MAP_NAME_ALL . ')');
            if (!$metaDataOnly) {
                $configuration = $this->staticInfoService->getMapConfigurationDocument();
            }
        } else {
            $mapNames = $this->staticInfoService->getAvailableMapNames();
            foreach ($mapNames as $mapName) {
                if ($documentIdentifier === $this->getDocumentIdentifier($mapName)) {
                    $metaData = $this->buildMetaData('Static Info Tables (' . $mapName . ')');
                    $configuration = $metaDataOnly ? null : $this->staticInfoService->getMapConfigurationDocument($mapName);
                    break;
                }
            }
        }

        if ($metaData !== null) {
            return $this->buildDocument($metaData, $configuration);
        }

        return null;
    }
}
