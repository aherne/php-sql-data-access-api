<?php
namespace Lucinda\SQL;

/**
 * Exception thrown when SQL statement executed is invalid.
 */
class StatementException extends \Exception {
	protected $query;
	 
	/**
	 * Constructor.
	 * 
	 * @param string $errorMessage Error message
	 * @param mixed $errorId Vendor-specific error code
	 * @param string $query Value of SQL statement that failed
	 */
	public function __construct($errorMessage, $errorId, $query) {
		$this->message = $errorMessage;
		$this->code = $errorId;
		$this->query = $query;
	}
	
	/**
	 * Gets value of sql statement that failed
	 *
	 * @return string
	 */
	public function getQuery() {
		return $this->query;
	}
}