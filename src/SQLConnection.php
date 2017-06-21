<?php
/**
 * Implements a database connection on top of PDO.
*/
class SQLConnection {
	/**
	 * Variable containing an instance of PDO class.
	 *
	 * @var PDO
	 */
	protected $PDO;

	/**
	 * Variable containing an instance of SQLDataSource class saved to be used in keep alive.
	 *
	 * @var SQLDataSource
	 */
	protected $objDataSource;

	/**
	 * Opens connection to database server.
	 *
	 * @param SQLDataSource $objDataSource
	 * @throws SQLConnectionException
	 */
	public function connect($objDataSource) {
		// open connection
		try {
			// defines settings to send to pdo driver
			$settings = ":host=".$objDataSource->getHost();
			if($objDataSource->getPort()) $settings .= ";port=".$objDataSource->getPort();
			if($objDataSource->getSchema()) $settings .= ";dbname=".$objDataSource->getSchema();

			// performs connection to PDO
			$this->PDO = new PDO($objDataSource->getDriverName().$settings, $objDataSource->getUserName(), $objDataSource->getPassword(), $objDataSource->getDriverOptions());
			$this->PDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		} catch(PDOException $e) {
			throw new SQLConnectionException($e->getMessage(), $e->getCode(), $objDataSource->getHost());
		}

		// saves datasource
		$this->objDataSource = $objDataSource;
	}

	/**
	 * Restores connection to database server in case it got closed unexpectedly.
	 */
	public function keepAlive() {
		$objStatement = new SQLStatement($this->PDO);
		try {
			$objStatement->execute("SELECT 1");
		} catch(SQLStatementException $e) {
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
	 * Reconnects to database server.
	 */
	public function reconnect() {
		$this->disconnect();
		$this->connect($this->objDataSource);
	}

	/**
	 * Operates with transactions on current connection.
	 * NOTE: this does not automatically start a transaction. To do that, call begin method.
	 *
	 * @return SQLTransaction
	 */
	public function transaction() {
		return new SQLTransaction($this->PDO);
	}

	/**
	 * Creates a statement on current connection.
	 *
	 * @return SQLStatement
	 */
	public function createStatement() {
		return new SQLStatement($this->PDO);
	}


	/**
	 * Creates a prepared statement on current connection.
	 *
	 * @return SQLPreparedStatement
	 */
	public function createPreparedStatement() {
		return new SQLPreparedStatement($this->PDO);
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