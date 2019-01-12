<?php
namespace Lucinda\SQL;
/**
 * Implements statement results parsiong on top of PDO.
 */
class StatementResults {
	/**
	 * Variable containing an instance of PDO class.
	 * 
	 * @var \PDO PDO
	 */
	protected $PDO;
	
	/**
	 * Variable containing an instance of PDOStatement class.
	 * 
	 * @var \PDOStatement PDO
	 */
	protected $PDOStatement;
		
	/**
	 * Creates an object of statement results.
	 * 
	 * @param \PDO $PDO
	 * @param \PDOStatement $PDOStatement
	 */
	public function __construct(\PDO $PDO, \PDOStatement $PDOStatement) {
		$this->PDO = $PDO;
		$this->PDOStatement = $PDOStatement;
	}
	
	/**
	 * Returns autoincremented id following last SQL INSERT statement. 
	 * 
	 * @return integer
	 */
	public function getInsertId() {
		return $this->PDO->lastInsertId();
	}
	
	/**
	 * Returns the number of rows affected by the last SQL INSERT/UPDATE/DELETE statement
	 * 
	 * @return integer
	 */
	public function getAffectedRows() {
		return $this->PDOStatement->rowCount();
	}
	
	/**
	 * Fetches first value of first row from ResultSet.
	 * 
	 * @return string
	 */
	public function toValue() {
		return $this->PDOStatement->fetchColumn();
	}

	/**
	 * Fetches row from ResultSet.
	 *
	 * @return string[string]
	 */
	public function toRow() {
		return $this->PDOStatement->fetch(\PDO::FETCH_ASSOC);
	}
	
	/**
	 * Fetches first column of all rows from ResultSet.
	 * 
	 * @return string[]
	 */
	public function toColumn() {
		return $this->PDOStatement->fetchAll(\PDO::FETCH_COLUMN,0);
	}
	
	/**
	 * Fetches all rows from Resultset into a mapping that has row value of $columnKeyName as key and row value of $columnValueName as value. 
	 * 
	 * @param string $columnKeyName
	 * @param string $columnValueName
	 * @return array
	 */
	public function toMap($columnKeyName, $columnValueName) {
		$output=array();
		while($row = $this->PDOStatement->fetch(\PDO::FETCH_ASSOC)) {
			$output[$row[$columnKeyName]]=$row[$columnValueName];
		}
		return $output;
	}
	
	/**
	 * Fetches all rows from Resultset into a numeric array.
	 * 
	 * @return array
	 */
	public function toList() {
		return $this->PDOStatement->fetchAll(\PDO::FETCH_ASSOC);
	}
}
