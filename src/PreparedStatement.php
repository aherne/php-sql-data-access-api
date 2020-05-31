<?php
namespace Lucinda\SQL;

/**
 * Implements a database prepared statement on top of PDO.
 */
class PreparedStatement
{
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
    public function __construct(\PDO $PDO)
    {
        $this->PDO = $PDO;
    }
    
    /**
     * Prepares a statement for execution.
     *
     * @param string $query
     */
    public function prepare(string $query): void
    {
        $this->pendingStatement=$query;
        $this->PDOStatement = $this->PDO->prepare($query);
    }

    /**
     * Binds a value to a prepared statement.
     *
     * @param string $parameter
     * @param string $value
     * @param integer $dataType
     * @throws Exception If developer tries to bind a parameter to a query that wasn't prepared.
     */
    public function bind(string $parameter, $value, int $dataType=\PDO::PARAM_STR): void
    {
        if (!$this->pendingStatement) {
            throw new Exception("Cannot bind anything on a statement that hasn't been prepared!");
        }
        $this->PDOStatement->bindValue($parameter, $value, \PDO::PARAM_STR);
    }
    
    /**
     * Executes a prepared statement.
     *
     * @param string[string] $boundParameters An array of values with as many elements as there are bound parameters in the SQL statement being executed.
     * @return StatementResults
     * @throws Exception If developer tries to execute a query that wasn't prepared.
     * @throws StatementException If query execution fails.
     */
    public function execute(array $boundParameters = array()): StatementResults
    {
        if (!$this->pendingStatement) {
            throw new Exception("Cannot execute a statement that hasn't been prepared!");
        }
        try {
            if (!empty($boundParameters)) {
                $this->PDOStatement->execute($boundParameters);
            } else {
                $this->PDOStatement->execute();
            }
        } catch (\PDOException $e) {
            $exception = new StatementException($e->getMessage(), (int) $e->getCode());
            $exception->setQuery($this->pendingStatement);
            throw $exception;
        }
        return new StatementResults($this->PDO, $this->PDOStatement);
    }
}
