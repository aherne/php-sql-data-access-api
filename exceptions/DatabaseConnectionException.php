<?php
class DatabaseConnectionException extends Exception {
	protected $strHostName="";
	
	public function __construct($strMessage, $intErrorCode, $strHostName) {
		$this->message = $strMessage;
		$this->code = $intErrorCode;
		$this->host = $strHostName;
	}
	
	public function getHostName() {
		return $this->strHostName;
	}
}