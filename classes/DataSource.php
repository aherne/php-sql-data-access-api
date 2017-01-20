<?php
/**
 * Implements a data source.
*/
class DataSource {
	private $strDriverName;
	private $tblDriverOptions;
	private $strHost;
	private $intPort;
	private $strUserName;
	private $strPassword;
	private $strSchema;

	/**
	 * Sets database server driver name.
	 *
	 * @param string $strDriverName
	 * @return void
	 */
	public function setDriverName($strDriverName) {
		$this->strDriverName = $strDriverName;
	}

	/**
	 * Gets database server vendor.
	 *
	 * @return string
	 */
	public function getDriverName() {
		return $this->strDriverName;
	}

	/**
	 * Sets database server vendor options
	 *
	 * @param array $tblDriverOptions
	 * @return void
	 */
	public function setDriverOptions($tblDriverOptions) {
		$this->tblDriverOptions = $tblDriverOptions;
	}

	/**
	 * Gets driver options
	 *
	 * @return array
	 */
	public function getDriverOptions() {
		return $this->tblDriverOptions;
	}

	/**
	 * Sets database server host name
	 *
	 * @param string $strHost
	 * @return void
	 */
	public function setHost($strHost) {
		$this->strHost = $strHost;
	}

	/**
	 * Gets database server host name
	 *
	 * @return string
	 */
	public function getHost() {
		return $this->strHost;
	}

	/**
	 * Sets database server port
	 *
	 * @param integer $intPort
	 * @return void
	 */
	public function setPort($intPort) {
		$this->intPort = $intPort;
	}

	/**
	 * Gets database server port
	 *
	 * @return integer
	 */
	public function getPort() {
		return $this->intPort;
	}

	/**
	 * Sets database server user name
	 *
	 * @param string $strUserName
	 * @return void
	 */
	public function setUserName($strUserName){
		$this->strUserName = $strUserName;
	}

	/**
	 * Gets database server user name
	 *
	 * @return string
	 */
	public function getUserName() {
		return $this->strUserName;
	}

	/**
	 * Sets database server user password
	 *
	 * @param string $strPassword
	 * @return void
	 */
	public function setPassword($strPassword) {
		$this->strPassword = $strPassword;
	}

	/**
	 * Gets database server user password
	 *
	 * @return string
	 */
	public function getPassword() {
		return $this->strPassword;
	}

	/**
	 * Sets database server default schema
	 *
	 * @param string $strSchema
	 * @return void
	 */
	public function setSchema($strSchema) {
		$this->strSchema = $strSchema;
	}

	/**
	 * Gets database server default schema
	 *
	 * @return string
	 */
	public function getSchema() {
		return $this->strSchema;
	}
}