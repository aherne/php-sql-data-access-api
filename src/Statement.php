<?php

namespace Lucinda\SQL;

/**
 * Implements a database statement on top of PDO.
 */
class Statement
{
    /**
     * Variable containing an instance of PDO class.
     *
     * @var \PDO pdo
     */
    protected $pdo;

    /**
     * Creates a SQL statement object.
     *
     * @param \PDO $pdo
     */
    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Quotes a string for use in a query.
     *
     * @param string $value
     * @return string
     */
    public function quote($value): string
    {
        return $this->pdo->quote($value);
    }

    /**
     * Executes a query.
     *
     * @param string $query
     * @throws StatementException If query execution fails
     * @return StatementResults
     */
    public function execute(string $query): StatementResults
    {
        $stmt=null;
        try {
            $stmt = $this->pdo->query($query);
        } catch (\PDOException $e) {
            $exception = new StatementException($e->getMessage(), (int) $e->getCode());
            $exception->setQuery($query);
            throw $exception;
        }
        return new StatementResults($this->pdo, $stmt);
    }
}
