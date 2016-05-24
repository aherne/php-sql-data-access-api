<?php
class DatabaseStatementException extends Exception {
	protected $query;
	 
	public function __construct($strErrorMessage, $intErrorId, $strQuery) {
		$this->message = $strErrorMessage;
		$this->code = $intErrorId;
		$this->query = $strQuery;
	}
	
	public function getQuery() {
		return $this->query;
	}
}