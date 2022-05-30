<?php

namespace Lucinda\SQL;

use PDO;

/**
 * Implements a database connection on top of PDO.
*/
class Connection
{
    /**
     * Variable containing an instance of PDO class.
     *
     * @var ?PDO
     */
    protected ?PDO $pdo = null;

    /**
     * Variable containing an instance of DataSource class saved to be used in keep alive.
     *
     * @var DataSource
     */
    protected DataSource $dataSource;

    /**
     * Opens connection to database server.
     *
     * @param DataSource $dataSource
     * @throws ConnectionException If connection to SQL server fails
     */
    public function connect(DataSource $dataSource): void
    {
        // open connection
        try {
            // defines settings to send to pdo driver
            $settings = ":host=".$dataSource->getHost();
            if ($dataSource->getPort()) {
                $settings .= ";port=".$dataSource->getPort();
            }
            if ($dataSource->getSchema()) {
                $settings .= ";dbname=".$dataSource->getSchema();
            }
            if ($dataSource->getCharset()) {
                $settings .= ";charset=".$dataSource->getCharset();
            }

            // performs connection to PDO
            $this->pdo = new PDO(
                $dataSource->getDriverName().$settings,
                $dataSource->getUserName(),
                $dataSource->getPassword(),
                $dataSource->getDriverOptions()
            );
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (\PDOException $e) {
            $exception = new ConnectionException($e->getMessage(), $e->getCode());
            $exception->setHostName($dataSource->getHost());
            throw $exception;
        }

        // saves datasource
        $this->dataSource = $dataSource;
    }

    /**
     * Operates with transactions on current connection.
     * NOTE: this does not automatically start a transaction. To do that, call begin method.
     *
     * @return Transaction
     */
    public function transaction(): Transaction
    {
        return new Transaction($this->pdo);
    }

    /**
     * Creates a statement on current connection.
     *
     * @return Statement
     */
    public function statement(): Statement
    {
        return new Statement($this->pdo);
    }


    /**
     * Creates a prepared statement on current connection.
     *
     * @return PreparedStatement
     */
    public function preparedStatement(): PreparedStatement
    {
        return new PreparedStatement($this->pdo);
    }

    /**
     * Restores connection to database server in case it got closed unexpectedly.
     */
    public function keepAlive(): void
    {
        $statement = new Statement($this->pdo);
        try {
            $statement->execute("SELECT 1");
        } catch (StatementException $e) {
            $this->connect($this->dataSource);
        }
    }

    /**
     * Reconnects to database server.
     */
    public function reconnect(): void
    {
        $this->disconnect();
        $this->connect($this->dataSource);
    }

    /**
     * Closes connection to database server.
     */
    public function disconnect(): void
    {
        $this->pdo = null;
    }
}
