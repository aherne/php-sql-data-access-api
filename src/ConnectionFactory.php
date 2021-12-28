<?php
namespace Lucinda\SQL;

/**
 * Implements a singleton factory for multiple SQL servers connection.
 */
class ConnectionFactory
{
    /**
     * Stores open connections.
     *
     * @var array
     */
    private static array $instances = [];
    
    /**
     * Stores registered data sources.
     * @var array
     */
    private static array $dataSources = [];
    
    /**
     * @var Connection
     */
    private Connection $database_connection;
    
    /**
     * Registers a data source object encapsulatings connection info based on unique server identifier.
     *
     * @param string $serverName Unique identifier of server you will be connecting to.
     * @param DataSource $dataSource
     */
    public static function setDataSource(string $serverName, DataSource $dataSource): void
    {
        self::$dataSources[$serverName] = $dataSource;
    }
    
    /**
     * Opens connection to database server (if not already open) according to DataSource and
     * returns an object of that connection to delegate operations to.
     *
     * @param string $serverName Unique identifier of server you will be connecting to.
     * @throws ConnectionException If connection to database server fails.
     * @return Connection
     */
    public static function getInstance(string $serverName): Connection
    {
        if (!isset(self::$instances[$serverName])) {
            self::$instances[$serverName] = new ConnectionFactory($serverName);
        }
        return self::$instances[$serverName]->getConnection();
    }


    /**
     * Connects to database automatically.
     *
     * @param string $serverName Unique identifier of server you will be connecting to.
     * @throws ConnectionException|Exception If connection to database server fails.
     */
    private function __construct(string $serverName)
    {
        if (!isset(self::$dataSources[$serverName])) {
            throw new Exception("Datasource not set for: ".$serverName);
        }
        $this->database_connection = new Connection();
        $this->database_connection->connect(self::$dataSources[$serverName]);
    }
    
    /**
     * Internal utility to get connection.
     *
     * @return Connection
     */
    private function getConnection(): Connection
    {
        return $this->database_connection;
    }
    
    /**
     * Disconnects from database server automatically.
     */
    public function __destruct()
    {
        try {
            if ($this->database_connection) {
                $this->database_connection->disconnect();
            }
        } catch (\Exception $e) {
        }
    }
}
