<?php

namespace DigitalMarketingFramework\Typo3\StaticInfoTables\Tests\Unit\Service;

use DigitalMarketingFramework\Typo3\StaticInfoTables\Domain\Repository\StaticInfoRepository;
use DigitalMarketingFramework\Typo3\StaticInfoTables\Service\StaticInfoService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\EventDispatcher\EventDispatcherInterface;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;

class StaticInfoServiceTest extends TestCase
{
    protected StaticInfoService $service;

    /**
     * @var StaticInfoRepository|(StaticInfoRepository&object&MockObject)|(StaticInfoRepository&MockObject)|(object&MockObject)|MockObject
     */
    protected $repositoryMock;

    /**
     * @var (object&MockObject)|MockObject|EventDispatcherInterface|(EventDispatcherInterface&object&MockObject)|(EventDispatcherInterface&MockObject)
     */
    protected $eventDispatcherMock;

    /**
     * @var (object&MockObject)|MockObject|ExtensionConfiguration|(ExtensionConfiguration&object&MockObject)|(ExtensionConfiguration&MockObject)
     */
    protected $extensionConfigurationMock;

    /**
     * @var (object&MockObject)|MockObject|ConfigurationManager|(ConfigurationManager&object&MockObject)|(ConfigurationManager&MockObject)
     */
    protected $configurationManagerMock;

    protected function setUp(): void
    {
        $this->repositoryMock = $this->createMock(StaticInfoRepository::class);
        $this->eventDispatcherMock = $this->createMock(EventDispatcherInterface::class);
        $this->extensionConfigurationMock = $this->createMock(ExtensionConfiguration::class);
        $this->configurationManagerMock = $this->createMock(ConfigurationManager::class);

        $this->service = new StaticInfoService(
            $this->repositoryMock,
            $this->eventDispatcherMock,
            $this->extensionConfigurationMock,
            $this->configurationManagerMock
        );
    }

    public function testGetStaticInfoTableReturnsTableDataTest(): void
    {
        // Set up the configurationManagerMock to return a specific value
        $this->configurationManagerMock->method('getConfiguration')->willReturn([
            'plugin.' => [
                'tx_staticinfotables.' => [
                    'settings.' => [
                        'countriesAllowed' => 'USA,CAN',
                    ],
                ],
            ],
        ]);
        $this->repositoryMock->method('findStaticInfo')->with(
            'static_countries',
            [
                0 => 'cn_iso_2',
                1 => 'cn_official_name_en',
                2 => 'cn_iso_3',
                5 => 'cn_official_name_local',
                11 => 'cn_short_en',
                15 => 'cn_short_local',
                21 => 'cn_eu_member',
            ],
            [
                'cn_iso_3' => [
                    'USA',
                    'CAN',
                ],
            ],
            'cn_iso_2'
        )->willReturn(
            [
                ['cn_iso_2' => 'US', 'cn_official_name_en' => 'United States'],
                ['cn_iso_2' => 'CA', 'cn_official_name_en' => 'Canada'],
            ]
        );
        $result = $this->service->getStaticInfoTable('static_countries', 'cn_iso_2');
        self::assertCount(2, $result);
        self::assertEquals('US', $result[0]['cn_iso_2']);
        self::assertEquals('United States', $result[0]['cn_official_name_en']);
    }
}
