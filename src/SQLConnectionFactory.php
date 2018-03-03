<?php
/**
 * Implements a singleton factory for multiple SQL servers connection.
 */
final class SQLConnectionFactory {
	/**
	 * Stores open connections.
	 * 
	 * @var array
	 */
	private static $instances;
	
	/**
	 * Stores registered data sources.
	 * @var array
	 */
	private static $dataSources;
	
    /**
     * @var SQLConnection
     */
    private $database_connection = null;
	
	/**
	 * Registers a data source object encapsulatings connection info based on unique server identifier.
	 * 
	 * @param string $serverName Unique identifier of server you will be connecting to.
	 * @param SQLDataSource $dataSource
	 */
	public static function setDataSource($serverName, SQLDataSource $dataSource){
		self::$dataSources[$serverName] = $dataSource;
	}
	
	/**
	 * Opens connection to database server (if not already open) according to SQLDataSource and 
	 * returns an object of that connection to delegate operations to.
	 * 
	 * @param string $serverName Unique identifier of server you will be connecting to.
	 * @throws SQLConnectionException
	 * @return SQLConnection
	 */
	public static function getInstance($serverName){
        if(isset(self::$instances[$serverName])) {
            return self::$instances[$serverName];
        }
        self::$instances[$serverName] = new SQLConnectionFactory($serverName);
		return self::$instances[$serverName];
	}


	/**
	 * Connects to database automatically.
	 *
	 * @throws SQLException
	 */
	private function __construct($serverName) {
		if(!isset(self::$dataSources[$serverName])) throw new SQLException("Datasource not set for: ".$serverName);
		$this->database_connection = new SQLConnection();
		$this->database_connection->connect(self::$dataSources[$serverName]);
	}
	
	/**
	 * Internal utility to get connection.
	 *
	 * @return SQLConnection
	 */
	private function getConnection() {
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
		} catch(Exception $e) {}
	}
	
}