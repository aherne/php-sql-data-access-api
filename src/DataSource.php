<?php
namespace Lucinda\SQL;

/**
 * Encapsulates connection information to an SQL server
*/
class DataSource
{
    private $driverName;
    private $driverOptions=[];
    private $host;
    private $port;
    private $userName;
    private $password;
    private $schema;
    private $charset;

    private $autoCommit;
    private $persistent;
    private $timeout;

    /**
     * Detects data source information.
     *
     * @param \SimpleXMLElement $databaseInfo
     * @throws ConfigurationException
     */
    public function __construct(\SimpleXMLElement $databaseInfo)
    {
        $this->driverName = (string) $databaseInfo["driver"];
        $this->host = (string) $databaseInfo["host"];
        $this->port = (int) $databaseInfo["port"];
        $this->userName = (string) $databaseInfo["username"];
        $this->password = (string) $databaseInfo["password"];
        $this->schema = (string) $databaseInfo["schema"];
        $this->charset = (string) $databaseInfo["charset"];

        if (!$this->driverName || !$this->host || !$this->port || !$this->userName || !$this->password)
        {
            throw new ConfigurationException("Attributes are mandatory: driver, host, port, username, password!");
        }

        if (isset($databaseInfo["autocommit"])) {
            $this->autoCommit = ((string) $databaseInfo["autocommit"]?true:false);
        }
        if (isset($databaseInfo["persistent"])) {
            $this->persistent = ((string) $databaseInfo["persistent"]?true:false);
        }
        if (isset($databaseInfo["timeout"])) {
            $this->timeout = (int) $databaseInfo["timeout"];
        }
    }

    /**
     * Gets database server vendor.
     *
     * @return string
     */
    public function getDriverName(): string
    {
        return $this->driverName;
    }

    /**
     * Gets database server vendor PDO connection options
     *
     * @return array
     */
    public function getDriverOptions(): array
    {
        return $this->driverOptions;
    }

    /**
     * Gets database server host name
     *
     * @return string
     */
    public function getHost(): string
    {
        return $this->host;
    }

    /**
     * Gets database server port
     *
     * @return integer
     */
    public function getPort(): int
    {
        return $this->port;
    }

    /**
     * Gets database server user name
     *
     * @return string
     */
    public function getUserName(): string
    {
        return $this->userName;
    }

    /**
     * Gets database server user password
     *
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * Gets database server default schema
     *
     * @return string
     */
    public function getSchema(): string
    {
        return $this->schema;
    }

    /**
     * Gets database server default charset.
     *
     * @return string
     */
    public function getCharset(): string
    {
        return $this->charset;
    }

    /**
     * Gets if autocommit is on/off/default.
     *
     * @return bool|null
     */
    public function getAutoCommit(): ?bool
    {
        return $this->autoCommit;
    }

    /**
     * Get if persistent connections are on/off/default.
     *
     * @return bool|null
     */
    public function getPersistent(): ?bool
    {
        return $this->persistent;
    }

    /**
     * Gets connection timeout (null if default)
     *
     * @return int|null
     */
    public function getTimeout(): ?int
    {
        return $this->timeout;
    }
}
