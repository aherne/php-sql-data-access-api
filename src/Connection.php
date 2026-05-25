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
     * @param  DataSource $dataSource
     * @throws ConnectionException If connection to SQL server fails
     */
    public function connect(DataSource $dataSource): void
    {
        // open connection
        try {
            // performs connection to PDO
            $this->pdo = new PDO(
                $this->getDSN($dataSource),
                $dataSource->getUserName(),
                $dataSource->getPassword(),
                $dataSource->getDriverOptions()
            );
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
        return new Transaction($this->getPDO());
    }

    /**
     * Creates a statement on current connection.
     *
     * @return Statement
     */
    public function statement(): Statement
    {
        return new Statement($this->getPDO());
    }


    /**
     * Creates a prepared statement on current connection.
     *
     * @return PreparedStatement
     */
    public function preparedStatement(): PreparedStatement
    {
        return new PreparedStatement($this->getPDO());
    }

    /**
     * Restores connection to database server in case it got closed unexpectedly.
     */
    public function keepAlive(): void
    {
        $statement = new Statement($this->getPDO());
        try {
            $statement->execute("SELECT 1");
        } catch (StatementException) {
            $this->connect($this->dataSource);
        }
    }

    /**
     * Gets PDO instance and checks if not disconnected already
     * 
     * @throws ConnectionException
     * @return PDO
     */
    private function getPDO(): PDO
    {
        if ($this->pdo === null) {
            throw new ConnectionException("Connection has already been disconnected!");
        }
        return $this->pdo;
    }

    private function getDSN(DataSource $dataSource): string
    {
        $settings = $dataSource->getDriverName().":host=".$dataSource->getHost();
        if ($port = $dataSource->getPort()) {
            $settings .= ";port=".$port;
        }
        if ($schema = $dataSource->getSchema()) {
            $settings .= ";dbname=".$schema;
        }
        if ($charset = $dataSource->getCharset()) {
            $settings .= ";charset=".$charset;
        }
        if ($file = $dataSource->getFile()) {
            $settings .= ";".$file;
        } else if ($dataSource->getDriverName() === "sqlite") {
            throw new ConfigurationException("Attribute 'file' is mandatory for 'server' tag if valueof 'driver' is: sqlite");
        }
        return $settings;
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
