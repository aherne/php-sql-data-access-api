<?php
/**
 * Implements a database statement on top of PDO.
 */
class DatabaseStatement {
	/**
	 * Variable containing an instance of PDO class.
	 * 
	 * @var PDO pdo
	 */
	protected $PDO;
	
	/**
	 * Creates a SQL statement object.
	 * 
	 * @param PDO $PDO
	 */
	public function __construct($PDO) {
		$this->PDO = $PDO;
	}
	
	/**
	 * Quotes a string for use in a query.
	 * 
	 * @param mixed $mixValue
	 * @return string
	 */
	public function quote($mixValue) {
		return $this->PDO->quote($mixValue);
	}
	
	/**
	 * Executes a query.
	 * 
	 * @param string $strQuery
	 * @throws DatabaseStatementException
	 * @return DatabaseStatementResults
	 */
	public function execute($strQuery) {
		$stmt=null;
		try {
			$stmt = $this->PDO->query($strQuery);
		} catch(PDOException $e) {
			throw new DatabaseStatementException($e->getMessage(), $e->getCode(), $strQuery);
		}
		return new DatabaseStatementResults($this->PDO, $stmt);
	}
}