<?php

namespace Lucinda\SQL;

/**
 * Encapsulates connection information to an SQL server
 */
class DataSource
{
    private string $driverName;
    private string $host;
    private int $port;
    private string $userName;
    private string $password;
    private string $schema;
    private string $charset;
    /**
     * @var array<int,int>
     */
    private array $driverOptions=[];

    /**
     * Detects data source information.
     *
     * @param  \SimpleXMLElement $databaseInfo
     * @throws ConfigurationException
     */
    public function __construct(\SimpleXMLElement $databaseInfo)
    {
        $this->setDriverName($databaseInfo);
        $this->setDriverOptions($databaseInfo);
        $this->setHost($databaseInfo);
        $this->setPort($databaseInfo);
        $this->setUserName($databaseInfo);
        $this->setPassword($databaseInfo);
        $this->setSchema($databaseInfo);
        $this->setCharset($databaseInfo);

        if (!$this->driverName || !$this->host || !$this->userName || !$this->password) {
            throw new ConfigurationException("Attributes are mandatory: driver, host, port, username, password!");
        }
    }

    /**
     * Sets database server vendor.
     *
     * @param  \SimpleXMLElement $databaseInfo
     * @return void
     */
    private function setDriverName(\SimpleXMLElement $databaseInfo): void
    {
        $this->driverName = (string) $databaseInfo["driver"];
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
     * Sets database server vendor PDO connection options
     *
     * @param  \SimpleXMLElement $databaseInfo
     * @return void
     */
    private function setDriverOptions(\SimpleXMLElement $databaseInfo): void
    {
        if (isset($databaseInfo["autocommit"])) {
            $this->driverOptions[\PDO::ATTR_AUTOCOMMIT] = ((string) $databaseInfo["autocommit"] ? 1 : 0);
        }
        if (isset($databaseInfo["persistent"])) {
            $this->driverOptions[\PDO::ATTR_PERSISTENT] = ((string) $databaseInfo["persistent"] ? 1 : 0);
        }
        if (!empty($databaseInfo["timeout"])) {
            $this->driverOptions[\PDO::ATTR_TIMEOUT] = (int) $databaseInfo["timeout"];
        }
    }

    /**
     * Gets database server vendor PDO connection options
     *
     * @return array<int,int>
     */
    public function getDriverOptions(): array
    {
        return $this->driverOptions;
    }

    /**
     * Sets database server host name
     *
     * @param  \SimpleXMLElement $databaseInfo
     * @return void
     */
    private function setHost(\SimpleXMLElement $databaseInfo): void
    {
        $this->host = (string) $databaseInfo["host"];
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
     * Sets database server port
     *
     * @param  \SimpleXMLElement $databaseInfo
     * @return void
     */
    private function setPort(\SimpleXMLElement $databaseInfo): void
    {
        $this->port = (int) $databaseInfo["port"];
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
     * Sets database server user name
     *
     * @param  \SimpleXMLElement $databaseInfo
     * @return void
     */
    private function setUserName(\SimpleXMLElement $databaseInfo): void
    {
        $this->userName = (string) $databaseInfo["username"];
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
     * Sets database server password
     *
     * @param  \SimpleXMLElement $databaseInfo
     * @return void
     */
    private function setPassword(\SimpleXMLElement $databaseInfo): void
    {
        $this->password = (string) $databaseInfo["password"];
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
     * Sets database server default schema
     *
     * @param  \SimpleXMLElement $databaseInfo
     * @return void
     */
    private function setSchema(\SimpleXMLElement $databaseInfo): void
    {
        $this->schema = (string) $databaseInfo["schema"];
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
     * Sets database server default charset
     *
     * @param  \SimpleXMLElement $databaseInfo
     * @return void
     */
    private function setCharset(\SimpleXMLElement $databaseInfo): void
    {
        $this->charset = (string) $databaseInfo["charset"];
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
