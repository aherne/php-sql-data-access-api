<?php
/**
 * Implements a database connection manager on top of SQLConnection.
 */
final class SQLConnectionFactory {
	/**
	 * Stores open connections in session.
	 * 
	 * @var array
	 */
	private static $connections;
	
	/**
	 * Stores registered data sources in session.
	 * @var array
	 */
	private static $dataSources;
	
	/**
	 * Registers connection information (data source) for later use, based on server name.
	 * 
	 * @param string $strServerName
	 * @param SQLDataSource $objDataSource
	 */
	public static function register($strServerName, $objDataSource){
		self::$dataSources[$strServerName] = $objDataSource;
	}
	
	/**
	 * Opens connection to database server (if not already open) according to saved information on the basis of server name and returns SQLConnection object. 
	 * 
	 * @param string $strServerName
	 * @throws SQLException
	 * @return SQLConnection
	 */
	public static function connect($strServerName){
		if(!isset(self::$connections[$strServerName])) {
			if(!isset(self::$dataSources[$strServerName])) throw new SQLException("Datasource not set for: ".$strServerName);
			$objConnection = new SQLConnection();
			$objConnection->connect(self::$dataSources[$strServerName]);
			self::$connections[$strServerName] = $objConnection;
		}
		return self::$connections[$strServerName];
	}
	
	/**
	 * Closes connection to database server according to server name
	 * 
	 * @param string $strServerName
	 */
	public static function disconnect($strServerName) {
		if(isset(self::$connections[$strServerName])) {
			self::$connections[$strServerName]->disconnect();
			unset(self::$connections[$strServerName]);
		}
	}
}