<?php

namespace DigitalMarketingFramework\Typo3\StaticInfoTables\Service\Event;

class StaticTableDataUpdateEvent
{
    /**
     * @param array<array<string,mixed>> $table
     */
    public function __construct(
        protected string $tableName,
        protected array $table,
    ) {
    }

    public function getTableName(): string
    {
        return $this->tableName;
    }

    /**
     * @return array<array<string,mixed>>
     */
    public function getTable(): array
    {
        return $this->table;
    }

    /**
     * @param array<array<string,mixed>> $table
     */
    public function setTable(array $table): void
    {
        $this->table = $table;
    }

    /**
     * @param array<string> $columnNames
     *
     * @return array<array<string,mixed>>
     */
    public function getColumns(array $columnNames): array
    {
        return array_map(static function (array $row) use ($columnNames) {
            $filteredRow = [];
            foreach ($columnNames as $column) {
                $filteredRow[$column] = $row[$column] ?? null;
            }

            return $filteredRow;
        }, $this->table);
    }

    /**
     * @param array<array<string,mixed>> $columns
     */
    public function setColumns(array $columns): void
    {
        foreach ($columns as $index => $row) {
            foreach ($row as $column => $value) {
                $this->table[$index][$column] = $value;
            }
        }
    }
}
