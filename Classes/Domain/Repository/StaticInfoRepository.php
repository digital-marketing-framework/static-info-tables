<?php

namespace DigitalMarketingFramework\Typo3\StaticInfoTables\Domain\Repository;

use TYPO3\CMS\Core\Database\ConnectionPool;

class StaticInfoRepository
{
    public function __construct(
        protected ConnectionPool $connectionPool,
    ) {
    }

    /**
     * @param array<string> $fields
     *
     * @return array<array<string,mixed>>
     */
    public function findStaticInfo(string $table, array $fields, ?string $orderBy = null): array
    {
        $query = $this->connectionPool->getQueryBuilderForTable($table);
        $query->select(...$fields)
            ->from($table)
            ->where($query->expr()->eq('deleted', 0))
        ;
        if ($orderBy !== null) {
            $query->orderBy($orderBy);
        }

        return $query->execute()->fetchAllAssociative();
    }
}
