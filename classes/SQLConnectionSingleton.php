<?php
/**
 * Implements a database connection singleton on top of SQLConnection object. Useful when your application works with only one database server.
 */
final class SQLConnectionSingleton
{
    /**
     * @var DataSource
     */
    private static $dataSource = null;
    
    /**
     * @var SQLConnectionSingleton
     */
    private static $instance = null;
    
    /**
     * @var SQLConnection
     */
    private $database_connection = null;
    
    /**
     * Registers a data source object encapsulatings connection info.
     * 
     * @param SQLDataSource $dataSource
     */
    public static function setDataSource(SQLDataSource $dataSource)
    {
        self::$dataSource = $dataSource;
    }
        
    /**
	 * Opens connection to database server (if not already open) according to SQLDataSource and returns a SQLConnection object. 
     * 
     * @return SQLConnection
     */
    public static function getInstance() 
    {
        if(self::$instance) {
            return self::$instance->getConnection();
        }
        self::$instance = new SQLConnectionSingleton();
        return self::$instance->getConnection();
    }
    
    /**
     * Connects to database automatically.
     * 
     * @throws SQLException
     */
    private function __construct() {
		if(!self::$dataSource) throw new SQLException("Datasource not set!");
        $this->database_connection = new SQLConnection();
        $this->database_connection->connect(self::$dataSource);
    }
    
    /**
     * Internal utility to get connection.
     * 
     * @return SQLConnection
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
