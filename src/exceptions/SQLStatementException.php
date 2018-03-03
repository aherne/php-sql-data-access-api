<?php
class SQLStatementException extends Exception {
	protected $query;
	 
	public function __construct($errorMessage, $errorId, $query) {
		$this->message = $errorMessage;
		$this->code = $errorId;
		$this->query = $query;
	}
	
	public function getQuery() {
		return $this->query;
	}
}