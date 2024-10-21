<?php

namespace DigitalMarketingFramework\Typo3\StaticInfoTables\Service;

use DigitalMarketingFramework\Core\Model\Configuration\ConfigurationInterface;
use DigitalMarketingFramework\Core\Utility\ConfigurationUtility;
use DigitalMarketingFramework\Core\Utility\MapUtility;
use DigitalMarketingFramework\Typo3\StaticInfoTables\Domain\Repository\StaticInfoRepository;
use DigitalMarketingFramework\Typo3\StaticInfoTables\Service\Event\StaticTableDataUpdateEvent;
use Psr\EventDispatcher\EventDispatcherInterface;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Configuration\Exception\InvalidConfigurationTypeException;

class StaticInfoService
{
    public const MAPS = [
        'countryIso2ToName' => ['table' => 'static_countries', 'from' => 'cn_iso_2', 'to' => 'cn_official_name_en'],
        'countryIso3ToName' => ['table' => 'static_countries', 'from' => 'cn_iso_3', 'to' => 'cn_official_name_en'],
        'countryIso2ToLocalName' => ['table' => 'static_countries', 'from' => 'cn_iso_2', 'to' => 'cn_official_name_local'],
        'countryIso3ToLocalName' => ['table' => 'static_countries', 'from' => 'cn_iso_3', 'to' => 'cn_official_name_local'],
        'countryNameToLocalName' => ['table' => 'static_countries', 'from' => 'cn_official_name_en', 'to' => 'cn_official_name_local'],

        'countryIso2ToShortName' => ['table' => 'static_countries', 'from' => 'cn_iso_2', 'to' => 'cn_short_en'],
        'countryIso3ToShortName' => ['table' => 'static_countries', 'from' => 'cn_iso_3', 'to' => 'cn_short_en'],
        'countryIso2ToLocalShortName' => ['table' => 'static_countries', 'from' => 'cn_iso_2', 'to' => 'cn_short_local'],
        'countryIso3ToLocalShortName' => ['table' => 'static_countries', 'from' => 'cn_iso_3', 'to' => 'cn_short_local'],
        'countryNameToLocalShortName' => ['table' => 'static_countries', 'from' => 'cn_short_en', 'to' => 'cn_short_local'],

        'countryIso2ToEuMember' => ['table' => 'static_countries', 'from' => 'cn_iso_2', 'to' => 'cn_eu_member'],
    ];

    public function __construct(
        protected StaticInfoRepository $repository,
        protected EventDispatcherInterface $eventDispatcher,
        protected ExtensionConfiguration $extensionConfiguration,
        protected ConfigurationManager $configurationManager,
    ) {
    }

    /**
     * @return array<string>
     */
    protected function getFieldsForTable(string $table): array
    {
        $fields = [];
        foreach (static::MAPS as $config) {
            if ($config['table'] !== $table) {
                continue;
            }

            $fields[] = $config['from'];
            $fields[] = $config['to'];
        }

        return array_unique($fields);
    }

    /**
     * @return array<string, array<int, string>>
     *
     * @throws InvalidConfigurationTypeException
     */
    protected function getWhitelistFromConfig(string $tableName): array
    {
        $result = [];
        $config = $this->configurationManager->getConfiguration(ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT)['plugin.']['tx_staticinfotables.']['settings.'];
        if ($tableName === 'static_countries') {
            $whitelist = explode(',', $config['countriesAllowed'] ?? '');
            if ($whitelist !== ['']) {
                $result['cn_iso_3'] = $whitelist;
            }
        }

        return $result;
    }

    /**
     * @return array<array<string,mixed>>
     */
    public function getStaticInfoTable(string $tableName, string $orderBy): array
    {
        $fields = $this->getFieldsForTable($tableName);
        $whitelist = $this->getWhitelistFromConfig($tableName);
        $table = $this->repository->findStaticInfo($tableName, $fields, $whitelist, $orderBy);

        $event = new StaticTableDataUpdateEvent($tableName, $table);
        $this->eventDispatcher->dispatch($event);

        return $event->getTable();
    }

    /**
     * @return array<string,string>
     */
    public function getStaticInfoTableMap(string $name): array
    {
        $config = static::MAPS[$name] ?? false;
        if ($config === false) {
            return [];
        }

        $table = $this->getStaticInfoTable($config['table'], $config['from']);

        $map = [];
        foreach ($table as $row) {
            $map[$row[$config['from']]] = $row[$config['to']];
        }

        return $map;
    }

    /**
     * @return array<string>
     */
    protected function getAllowedMapNames(): array
    {
        try {
            $config = $this->extensionConfiguration->get('dmf_static_info_tables');
            $allowedMapsString = isset($config['enabledValueMaps']) ? (string)$config['enabledValueMaps'] : '';
            $allowedMaps = $allowedMapsString === '' ? [] : explode(',', $allowedMapsString);

            return array_map('trim', $allowedMaps);
        } catch (ExtensionConfigurationExtensionNotConfiguredException|ExtensionConfigurationPathDoesNotExistException) {
            return [];
        }
    }

    /**
     * @return array<string>
     */
    public function getAvailableMapNames(): array
    {
        $maps = array_keys(static::MAPS);
        $allowedMaps = $this->getAllowedMapNames();
        if ($allowedMaps !== []) {
            $maps = array_intersect($maps, $allowedMaps);
        }

        return $maps;
    }

    /**
     * @return array<string,array{uuid:string,weight:int,key:string,value:string}>
     */
    public function getMapConfiguration(string $name): array
    {
        $simpleMap = $this->getStaticInfoTableMap($name);
        $map = [];
        $weight = MapUtility::WEIGHT_START;
        foreach ($simpleMap as $key => $value) {
            $id = ConfigurationUtility::generateUuidForPackage('static-info-tables', $name, [$key]);
            $map[$id] = MapUtility::createItem($value, $key, $weight, $id);
            $weight += MapUtility::WEIGHT_DELTA;
        }

        return $map;
    }

    /**
     * @return array{dataProcessing:array{valueMaps:array<string,array{uuid:string,weight:int,key:string,value:array<string,array{uuid:string,weight:int,key:string,value:string}>}>}}
     */
    public function getMapConfigurationDocument(?string $name = null): array
    {
        $names = $name === null ? $this->getAvailableMapNames() : [$name];
        $document = [
            ConfigurationInterface::KEY_DATA_PROCESSING => [
                ConfigurationInterface::KEY_VALUE_MAPS => [],
            ],
        ];

        $weight = MapUtility::WEIGHT_START;
        foreach ($names as $mapName) {
            $map = $this->getMapConfiguration($mapName);
            if ($map === []) {
                continue;
            }

            $id = ConfigurationUtility::generateUuidForPackage('static-info-tables', $mapName);
            $document[ConfigurationInterface::KEY_DATA_PROCESSING][ConfigurationInterface::KEY_VALUE_MAPS][$id] = MapUtility::createItem($map, $mapName, $weight, $id);
            $weight += MapUtility::WEIGHT_DELTA;
        }

        return $document;
    }
}
