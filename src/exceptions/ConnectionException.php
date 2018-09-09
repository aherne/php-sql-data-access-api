<?php
namespace Lucinda\SQL;

/**
 * Exception thrown when connection to an SQL server fails
 */
class ConnectionException extends \Exception {
	protected $hostName="";
	
	/**
	 * Constructor.
	 * 
	 * @param string $message Error message
	 * @param mixed $errorCode Vendor-specific error code
	 * @param string $hostName Server host name in which error occurred (useful when app connects to multiple servers)
	 */
	public function __construct($message, $errorCode, $hostName) {
		$this->message = $message;
		$this->code = $errorCode;
		$this->host = $hostName;
	}
	
	/**
	 * Gets sql server host name in which error has occurred.
	 * 
	 * @return string
	 */
	public function getHostName() {
		return $this->hostName;
	}
}