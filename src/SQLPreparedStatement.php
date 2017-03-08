<?php
/**
 * Implements a database prepared statement on top of PDO.
 */
class SQLPreparedStatement {
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
	 * Statement to be prepared.
	 * 
	 * @var string $strPendingStatement
	 */
	protected $strPendingStatement;
	
	/**
	 * Creates a SQL prepared statement object automatically.
	 * 
	 * @param PDO $PDO
	 */
	public function __construct($PDO) {
		$this->PDO = $PDO;
	}
	
	/**
	 * Prepares a statement for execution.
	 * 
	 * @param string $strQuery
	 */
	public function prepare($strQuery) {
		$this->strPendingStatement=$strQuery;
		$this->PDOStatement = $this->PDO->prepare($strQuery);
	}

	/**
	 * Binds a value to a prepared statement.
	 *
	 * @param string $strParameter
	 * @param mixed $mixValue
	 * @param integer $intDataType
	 * @throws SQLException
	 */
	public function bind($strParameter, $mixValue, $intDataType=PDO::PARAM_STR) {
		if(!$this->strPendingStatement) throw new SQLException("Cannot bind anything on a statement that hasn't been prepared!");
		$this->PDOStatement->bindValue($strParameter, $mixValue, $intDataType);
	}
	
	/**
	 * Executes a prepared statement.
	 * 
	 * @return SQLStatementResults
	 * @throws SQLException, SQLStatementException
	 */
	public function execute() {
		if(!$this->strPendingStatement) throw new SQLException("Cannot execute a statement that hasn't been prepared!");
		try {
			$this->PDOStatement->execute();
		} catch(PDOException $e) {
			throw new SQLStatementException($e->getMessage(), $e->getCode(), $this->strPendingStatement);
		}
		return new SQLStatementResults($this->PDO, $this->PDOStatement);
	}
}