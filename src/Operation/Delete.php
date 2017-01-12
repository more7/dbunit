<?php
/*
 * This file is part of DBUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPUnit\DbUnit\Operation;

use PHPUnit\DbUnit\Database\IConnection;
use PHPUnit\DbUnit\DataSet\ITable;
use PHPUnit\DbUnit\DataSet\ITableMetadata;
use PHPUnit_Extensions_Database_Operation_RowBased;

/**
 * Deletes the rows in a given dataset using primary key columns.
 */
class Delete extends PHPUnit_Extensions_Database_Operation_RowBased
{
    protected $operationName = 'DELETE';

    protected $iteratorDirection = self::ITERATOR_TYPE_REVERSE;

    protected function buildOperationQuery(ITableMetadata $databaseTableMetaData, ITable $table, IConnection $connection)
    {
        $keys = $databaseTableMetaData->getPrimaryKeys();

        $whereStatement = 'WHERE ' . implode(' AND ', $this->buildPreparedColumnArray($keys, $connection));

        $query = "
            DELETE FROM {$connection->quoteSchemaObject($table->getTableMetaData()->getTableName())}
            {$whereStatement}
        ";

        return $query;
    }

    protected function buildOperationArguments(ITableMetadata $databaseTableMetaData, ITable $table, $row)
    {
        $args = [];
        foreach ($databaseTableMetaData->getPrimaryKeys() as $columnName) {
            $args[] = $table->getValue($row, $columnName);
        }

        return $args;
    }
}
