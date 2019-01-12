<?php
namespace Lucinda\SQL;
/**
 * Implements a database prepared statement on top of PDO.
 */
class PreparedStatement {
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
	 * Statement to be prepared.
	 * 
	 * @var string $pendingStatement
	 */
	protected $pendingStatement;
	
	/**
	 * Creates a SQL prepared statement object automatically.
	 * 
	 * @param \PDO $PDO
	 */
	public function __construct(\PDO $PDO) {
		$this->PDO = $PDO;
	}
	
	/**
	 * Prepares a statement for execution.
	 * 
	 * @param string $query
	 */
	public function prepare($query) {
		$this->pendingStatement=$query;
		$this->PDOStatement = $this->PDO->prepare($query);
	}

	/**
	 * Binds a value to a prepared statement.
	 *
	 * @param string $parameter
	 * @param mixed $value
	 * @param integer $dataType
	 * @throws Exception If developer tries to bind a parameter to a query that wasn't prepared.
	 */
	public function bind($parameter, $value, $dataType=\PDO::PARAM_STR) {
		if(!$this->pendingStatement) throw new Exception("Cannot bind anything on a statement that hasn't been prepared!");
		$this->PDOStatement->bindValue($parameter, $value, $dataType);
	}
	
	/**
	 * Executes a prepared statement.
	 * 
	 * @param string[string] $boundParameters An array of values with as many elements as there are bound parameters in the SQL statement being executed.
	 * @return StatementResults
	 * @throws Exception If developer tries to execute a query that wasn't prepared.
	 * @throws StatementException If query execution fails.
	 */
	public function execute($boundParameters = array()) {
		if(!$this->pendingStatement) throw new Exception("Cannot execute a statement that hasn't been prepared!");
		try {
			if(!empty($boundParameters)) {
				$this->PDOStatement->execute($boundParameters);
			} else {
				$this->PDOStatement->execute();
			}			
		} catch(\PDOException $e) {
			throw new StatementException($e->getMessage(), $e->getCode(), $this->pendingStatement);
		}
		return new StatementResults($this->PDO, $this->PDOStatement);
	}
}