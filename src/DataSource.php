<?php
namespace Lucinda\SQL;

/**
 * Encapsulates connection information to an SQL server
*/
class DataSource
{
    private $driverName;
    private $host;
    private $port;
    private $userName;
    private $password;
    private $schema;
    private $charset;
    private $driverOptions=[];

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

        if (!$this->driverName || !$this->host || !$this->userName || !$this->password)
        {
            throw new ConfigurationException("Attributes are mandatory: driver, host, port, username, password!");
        }

        $this->driverOptions[\PDO::ATTR_ERRMODE] = \PDO::ERRMODE_EXCEPTION;
        if (isset($databaseInfo["autocommit"])) {
            $this->driverOptions[\PDO::ATTR_AUTOCOMMIT] = ((string) $databaseInfo["autocommit"]?1:0);
        }
        if (isset($databaseInfo["persistent"])) {
            $this->driverOptions[\PDO::ATTR_PERSISTENT] = ((string) $databaseInfo["persistent"]?1:0);
        }
        if (!empty($databaseInfo["timeout"])) {
            $this->driverOptions[\PDO::ATTR_TIMEOUT] = (int) $databaseInfo["timeout"];
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
}
