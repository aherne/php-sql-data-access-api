<?php

namespace Lucinda\SQL;

/**
 * Encapsulates transaction operations on top of a PDO object
 */
class Transaction
{
    /**
     * Variable containing a PDO instance this class relies on.
     *
     * @var \PDO $pdo
     */
    protected $pdo;

    /**
     * Sets up a database transaction.
     *
     * @param \PDO $pdo
     */
    public function __construct(\PDO $pdo)
    {
        $this->pdo=$pdo;
    }

    /**
     * Starts a transaction
     */
    public function begin(): void
    {
        $this->pdo->beginTransaction();
    }

    /**
     * Commits transaction.
     */
    public function commit(): void
    {
        $this->pdo->commit();
    }

    /**
     * Rolls back transaction.
     */
    public function rollback(): void
    {
        $this->pdo->rollBack();
    }
}
