<?php

declare(strict_types=1);

namespace DigitalMarketingFramework\Typo3\StaticInfoTables\Tests\Functional\Domain\Repository;

use DigitalMarketingFramework\Typo3\StaticInfoTables\Domain\Repository\StaticInfoRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

class StaticInfoRepositoryTest extends FunctionalTestCase
{
    protected StaticInfoRepository $staticInfoRepository;

    protected array $testExtensionsToLoad = ['typo3conf/ext/static_info_tables', 'typo3conf/ext/dmf_static_info_tables'];

    protected function setUp(): void
    {
        parent::setUp();
        $this->staticInfoRepository = GeneralUtility::makeInstance(StaticInfoRepository::class);
    }

    /**
     * Test that the function returns correct results when whitelist is valid
     */
    public function testFindStaticInfoWithValidWhitelist(): void
    {
        $table = 'static_countries';
        $fields = ['cn_iso_3', 'cn_short_en'];
        $whitelist = ['cn_iso_3' => ['CAN', 'USA']];
        $orderBy = 'cn_iso_3';

        $queryResult = $this->staticInfoRepository->findStaticInfo($table, $fields, $whitelist, $orderBy);

        // Assert the correct countries are returned
        self::assertCount(2, $queryResult);
        self::assertEquals('CAN', $queryResult[0]['cn_iso_3']);
        self::assertEquals('USA', $queryResult[1]['cn_iso_3']);
    }

    /**
     * Test that the function returns correct results when whitelist is valid
     */
    public function testFindStaticInfoWithEmptyWhitelist(): void
    {
        $table = 'static_countries';
        $fields = ['cn_iso_3', 'cn_short_en'];
        $whitelist = [];
        $orderBy = 'cn_iso_3';

        $queryResult = $this->staticInfoRepository->findStaticInfo($table, $fields, $whitelist, $orderBy);

        // Assert the correct countries are returned
        self::assertGreaterThan(230, $queryResult);
    }

    /**
     * Test that the function returns correct results when whitelist is valid
     */
    public function testFindStaticInfoWithInvalidWhitelist(): void
    {
        $table = 'static_countries';
        $fields = ['cn_iso_3', 'cn_short_en'];
        $whitelist = ['cn_iso_3' => ['CA', 'US']];
        $orderBy = 'cn_iso_3';

        $queryResult = $this->staticInfoRepository->findStaticInfo($table, $fields, $whitelist, $orderBy);

        // Assert the correct countries are returned
        self::assertCount(0, $queryResult);
    }
}
