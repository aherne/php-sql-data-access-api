<?php
/**
 * Implements statement results parsiong on top of PDO.
 */
class DatabaseStatementResults {
	/**
	 * Variable containing an instance of PDO class.
	 * 
	 * @var PDO PDO
	 */
	protected $PDO;
	
	/**
	 * Variable containing an instance of PDOStatement class.
	 * 
	 * @var PDOStatement PDO
	 */
	protected $PDOStatement;
		
	/**
	 * Creates an object of statement results.
	 * 
	 * @param PDO $PDO
	 * @param PDOStatement $PDOStatement
	 */
	public function __construct($PDO, $PDOStatement) {
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
	 * @return mixed
	 */
	public function toValue() {
		return $this->PDOStatement->fetchColumn();
	}

	/**
	 * Fetches row from ResultSet.
	 *
	 * @return array
	 */
	public function toRow() {
		return $this->PDOStatement->fetch(PDO::FETCH_ASSOC);
	}
	
	/**
	 * Fetches first column of all rows from ResultSet.
	 * 
	 * @return array
	 */
	public function toColumn() {
		return $this->PDOStatement->fetchAll(PDO::FETCH_COLUMN,0);
	}
	
	/**
	 * Fetches all rows from Resultset into a mapping that has row value of $strColumnKeyName as key and row value of $strColumnValueName as value. 
	 * 
	 * @param string $strColumnKeyName
	 * @param string $strColumnValueName
	 * @return array
	 */
	public function toMap($strColumnKeyName, $strColumnValueName) {
		$tblTMP = $this->PDOStatement->fetchAll(PDO::FETCH_ASSOC);
		if(!sizeof($tblTMP)) return array();
		$tblOutput=array();
		foreach($tblTMP as $tblRow) {
			$tblOutput[$tblRow[$strColumnKeyName]]=$tblRow[$strColumnValueName];
		}
		return $tblOutput;
	}
	
	/**
	 * Fetches all rows from Resultset into a numeric array.
	 * 
	 * @return array
	 */
	public function toList() {
		return $this->PDOStatement->fetchAll(PDO::FETCH_ASSOC);
	}
}