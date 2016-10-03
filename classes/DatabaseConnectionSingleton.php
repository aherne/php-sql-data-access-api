<?php
/**
 * Implements a database connection singleton on top of DatabaseConnection object. Useful when your application works with only one database server.
 */
final class DatabaseConnectionSingleton
{
    /**
     * @var DataSource
     */
    private static $dataSource = null;
    
    /**
     * @var DatabaseConnectionSingleton
     */
    private static $instance = null;
    
    /**
     * @var DatabaseConnection
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
	 * Opens connection to database server (if not already open) according to DataSource and returns a DatabaseConnection object. 
     * 
     * @return DatabaseConnection
     */
    public static function getInstance() 
    {
        if(self::$instance) {
            return self::$instance->getConnection();
        }
        self::$instance = new DatabaseConnectionSingleton();
        return self::$instance->getConnection();
    }
    
    /**
     * Connects to database automatically.
     * 
     * @throws DatabaseException
     */
    private function __construct() {
		if(!self::$dataSource) throw new DatabaseException("Datasource not set!");
        $this->database_connection = new DatabaseConnection();
        $this->database_connection->connect(self::$dataSource);
    }
    
    /**
     * Internal utility to get connection.
     * 
     * @return DatabaseConnection
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
            $this->database_connection->disconnect();
        } catch(Exception $e) {}
    }
}
