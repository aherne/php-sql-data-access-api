<?php
/**
 * Implements a database connection on top of PDO.
 */
class DatabaseConnection {	
	/**
	 * Variable containing an instance of PDO class.
	 * 
	 * @var PDO
	 */
	protected $PDO;
	
	/**
	 * Variable containing an instance of DataSource class saved to be used in keep alive.
	 * 
	 * @var DataSource
	 */
	protected $objDataSource;
	
	/**
	 * Opens connection to database server.
	 * 
	 * @param DataSource $objDataSource
	 * @throws DatabaseConnectionException
	 */
	public function connect($objDataSource) {
		// open connection
		try {
			$this->PDO = new PDO($objDataSource->getDriverName().":host=".$objDataSource->getHostName(), $objDataSource->getUserName(), $objDataSource->getPassword(), $objDataSource->getDriverOptions());
			$this->PDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		} catch(PDOException $e) {
			throw new DatabaseConnectionException($e->getMessage(), $e->getCode(), $objDataSource->getHostName());
		}
		
		// saves datasource 
		$this->objDataSource = $objDataSource;
	}
	
	/**
	 * Restores connection to database server in case it got closed unexpectedly.
	 */
	public function keepAlive() {
		$objStatement = new DatabaseStatement($this->PDO);
		try {
			$objStatement->execute("SELECT 1");
		} catch(DatabaseStatementException $e) {
			$this->connect($this->objDataSource);
		}
	}
	
	/**
	 * Closes connection to database server.
	 * 
	 * @return void
	 */
	public function disconnect() {
		$this->PDO = null;
	}
	
	/**
	 * Operates with transactions on current connection.
	 * NOTE: this does not automatically start a transaction. To do that, call begin method.
	 * 
	 * @return DatabaseTransaction
	 */
	public function transaction() {
		return new DatabaseTransaction($this->PDO);
	}
	
	/**
	 * Creates a statement on current connection.
	 * 
	 * @return DatabaseStatement
	 */
	public function createStatement() {
		return new DatabaseStatement($this->PDO);
	}


	/**
	 * Creates a prepared statement on current connection.
	 *
	 * @return DatabasePreparedStatement
	 */
	public function createPreparedStatement() {
		return new DatabasePreparedStatement($this->PDO);
	}
	
	/**
	 * Returns whether or not statements executed on server are commited by default.
	 * 
	 * @return boolean
	 */
	public function getAutoCommit() {
		return $this->PDO->getAttribute(PDO::ATTR_AUTOCOMMIT);
	}
	
	/**
	 * Sets whether or not statements executed on server are commited by default.
	 * 
	 * @param boolean $blnValue
	 */
	public function setAutoCommit($blnValue) {
		$this->PDO->setAttribute(PDO::ATTR_AUTOCOMMIT, $blnValue);
	}
	
	/**
	 * Gets connection timeout from database server. (Not supported by all drivers)
	 * 
	 * @return integer
	 */
	public function getConnectionTimeout() {
		return $this->PDO->getAttribute(PDO::ATTR_TIMEOUT);
	}
	
	/**
	 * Sets connection timeout on database server. (Not supported by all drivers)
	 * 
	 * @param integer $intValue
	 */
	public function setConnectionTimeout($intValue) {
		$this->PDO->setAttribute(PDO::ATTR_TIMEOUT, $intValue);
	}
	
	/**
	 * Returns whether or not current connection is persistent.
	 * 
	 * @return boolean
	 */
	public function getPersistent() {
		return $this->PDO->getAttribute(PDO::ATTR_PERSISTENT);
	}
	
	/**
	 * Sets whether or not current connection is persistent.
	 * @param boolean $blnValue
	 */
	public function setPersistent($blnValue) {
		$this->PDO->setAttribute(PDO::ATTR_PERSISTENT, $blnValue);
	}
}