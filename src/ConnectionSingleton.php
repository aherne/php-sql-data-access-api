<?php
namespace Lucinda\SQL;
/**
 * Implements a database connection singleton on top of Connection object. Useful when your application works with only one database server.
 */
class ConnectionSingleton
{
    /**
     * @var DataSource
     */
    private static $dataSource = null;
    
    /**
     * @var ConnectionSingleton
     */
    private static $instance = null;
    
    /**
     * @var Connection
     */
    private $database_connection = null;
    
    /**
     * Registers a data source object encapsulatings connection info.
     * 
     * @param DataSource $dataSource
     */
    public static function setDataSource(DataSource $dataSource)
    {
        self::$dataSource = $dataSource;
    }
        
    /**
	 * Opens connection to database server (if not already open) according to DataSource and returns a Connection object. 
     * 
     * @return Connection
	 * @throws ConnectionException If connection to database server fails.
     */
    public static function getInstance() 
    {
        if(self::$instance) {
            return self::$instance->getConnection();
        }
        self::$instance = new ConnectionSingleton();
        return self::$instance->getConnection();
    }
    
    /**
     * Connects to database automatically.
     * 
	 * @throws ConnectionException If connection to database server fails.
     */
    private function __construct() {
		if(!self::$dataSource) throw new Exception("Datasource not set!");
        $this->database_connection = new Connection();
        $this->database_connection->connect(self::$dataSource);
    }
    
    /**
     * Internal utility to get connection.
     * 
     * @return Connection
     */
    private function getConnection()
    {
        return $this->database_connection;
    }
    
    /**
     * Disconnects from database server automatically.
     */
    public function __destruct() {
        try {
        	if($this->database_connection) {
        		$this->database_connection->disconnect();
        	}
        } catch(\Exception $e) {}
    }
}
