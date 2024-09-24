<?php

namespace DigitalMarketingFramework\Typo3\StaticInfoTables\Domain\Repository;

use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class StaticInfoRepository
{
    /**
     * @param array<string> $fields
     * @param array<mixed|null> $whitelist
     *
     * @return array<array<string,mixed>>
     */
    public function findStaticInfo(string $table, array $fields, array $whitelist = [], ?string $orderBy = null): array
    {
        $connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
        $queryBuilder = $connectionPool->getQueryBuilderForTable($table);
        $queryBuilder->select(...$fields)
            ->from($table)
            ->where($queryBuilder->expr()->eq('deleted', 0))
        ;
        foreach ($whitelist as $key => $value) {
            $queryBuilder->andWhere($queryBuilder->expr()->in($key, $queryBuilder->createNamedParameter($value, Connection::PARAM_STR_ARRAY)));
        }

        if ($orderBy !== null) {
            $queryBuilder->orderBy($orderBy);
        }

        return $queryBuilder->executeQuery()->fetchAllAssociative();
    }
}
