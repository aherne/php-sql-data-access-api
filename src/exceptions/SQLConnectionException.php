<?php
class SQLConnectionException extends Exception {
	protected $hostName="";
	
	public function __construct($message, $errorCode, $hostName) {
		$this->message = $message;
		$this->code = $errorCode;
		$this->host = $hostName;
	}
	
	public function getHostName() {
		return $this->hostName;
	}
}