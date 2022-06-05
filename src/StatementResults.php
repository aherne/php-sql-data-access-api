<?php

namespace Lucinda\SQL;

/**
 * Implements statement results parsiong on top of PDO.
 */
class StatementResults
{
    /**
     * Variable containing an instance of PDO class.
     *
     * @var \PDO PDO
     */
    protected $pdo;

    /**
     * Variable containing an instance of PDOStatement class.
     *
     * @var \PDOStatement PDO
     */
    protected $pdoStatement;

    /**
     * Creates an object of statement results.
     *
     * @param \PDO          $pdo
     * @param \PDOStatement $pdoStatement
     */
    public function __construct(\PDO $pdo, \PDOStatement $pdoStatement)
    {
        $this->pdo = $pdo;
        $this->pdoStatement = $pdoStatement;
    }

    /**
     * Returns autoincremented id following last SQL INSERT statement.
     *
     * @return int
     */
    public function getInsertId(): int
    {
        return (int) $this->pdo->lastInsertId();
    }

    /**
     * Returns the number of rows affected by the last SQL INSERT/UPDATE/DELETE statement
     *
     * @return integer
     */
    public function getAffectedRows(): int
    {
        return $this->pdoStatement->rowCount();
    }

    /**
     * Fetches first value of first row from ResultSet.
     *
     * @return string
     */
    public function toValue(): string
    {
        return (string) $this->pdoStatement->fetchColumn();
    }

    /**
     * Fetches row from ResultSet.
     *
     * @return string[string]|false
     */
    public function toRow()
    {
        return $this->pdoStatement->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * Fetches first column of all rows from ResultSet.
     *
     * @return string[]
     */
    public function toColumn(): array
    {
        return $this->pdoStatement->fetchAll(\PDO::FETCH_COLUMN, 0);
    }

    /**
     * Fetches all rows from Resultset into a mapping that has row value of $columnKeyName as key and row value of
     * $columnValueName as value.
     *
     * @param  string $columnKeyName
     * @param  string $columnValueName
     * @return array<mixed>
     */
    public function toMap(string $columnKeyName, string $columnValueName): array
    {
        $output=array();
        while ($row = $this->pdoStatement->fetch(\PDO::FETCH_ASSOC)) {
            $output[$row[$columnKeyName]]=$row[$columnValueName];
        }
        return $output;
    }

    /**
     * Fetches all rows from Resultset into a numeric array.
     *
     * @return array<mixed>
     */
    public function toList(): array
    {
        return $this->pdoStatement->fetchAll(\PDO::FETCH_ASSOC);
    }
}
