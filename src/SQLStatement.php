<?php
/**
 * Implements a database statement on top of PDO.
 */
class SQLStatement {
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
	 * @param mixed $value
	 * @return string
	 */
	public function quote($value) {
		return $this->PDO->quote($value);
	}
	
	/**
	 * Executes a query.
	 * 
	 * @param string $query
	 * @throws SQLStatementException
	 * @return SQLStatementResults
	 */
	public function execute($query) {
		$stmt=null;
		try {
			$stmt = $this->PDO->query($query);
		} catch(PDOException $e) {
			throw new SQLStatementException($e->getMessage(), $e->getCode(), $query);
		}
		return new SQLStatementResults($this->PDO, $stmt);
	}
}