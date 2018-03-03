<?php
/**
 * Implements a data source.
*/
class SQLDataSource {
	private $driverName;
	private $driverOptions;
	private $host;
	private $port;
	private $userName;
	private $password;
	private $schema;
    private $charset;

	/**
	 * Sets database server driver name.
	 *
	 * @param string $driverName
	 * @return void
	 */
	public function setDriverName($driverName) {
		$this->driverName = $driverName;
	}

	/**
	 * Gets database server vendor.
	 *
	 * @return string
	 */
	public function getDriverName() {
		return $this->driverName;
	}

	/**
	 * Sets database server vendor options
	 *
	 * @param array $driverOptions
	 * @return void
	 */
	public function setDriverOptions($driverOptions) {
		$this->driverOptions = $driverOptions;
	}

	/**
	 * Gets driver options
	 *
	 * @return array
	 */
	public function getDriverOptions() {
		return $this->driverOptions;
	}

	/**
	 * Sets database server host name
	 *
	 * @param string $host
	 * @return void
	 */
	public function setHost($host) {
		$this->host = $host;
	}

	/**
	 * Gets database server host name
	 *
	 * @return string
	 */
	public function getHost() {
		return $this->host;
	}

	/**
	 * Sets database server port
	 *
	 * @param integer $port
	 * @return void
	 */
	public function setPort($port) {
		$this->port = $port;
	}

	/**
	 * Gets database server port
	 *
	 * @return integer
	 */
	public function getPort() {
		return $this->port;
	}

	/**
	 * Sets database server user name
	 *
	 * @param string $userName
	 * @return void
	 */
	public function setUserName($userName){
		$this->userName = $userName;
	}

	/**
	 * Gets database server user name
	 *
	 * @return string
	 */
	public function getUserName() {
		return $this->userName;
	}

	/**
	 * Sets database server user password
	 *
	 * @param string $password
	 * @return void
	 */
	public function setPassword($password) {
		$this->password = $password;
	}

	/**
	 * Gets database server user password
	 *
	 * @return string
	 */
	public function getPassword() {
		return $this->password;
	}

	/**
	 * Sets database server default schema
	 *
	 * @param string $schema
	 * @return void
	 */
	public function setSchema($schema) {
		$this->schema = $schema;
	}

	/**
	 * Gets database server default schema
	 *
	 * @return string
	 */
	public function getSchema() {
		return $this->schema;
	}

    /**
     * Sets database server default charset
     *
     * @param string $charset
     */
	public function setCharset($charset) {
	    $this->charset = $charset;
    }

    /**
     * Gets database server default charset.
     *
     * @return string
     */
    public function getCharset() {
	    return $this->charset;
    }
}