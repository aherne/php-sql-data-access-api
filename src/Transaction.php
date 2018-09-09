<?php
namespace Lucinda\SQL;

/**
 * Encapsulates transaction operations on top of a PDO object
 */
class Transaction {
	/**
	 * Variable containing a PDO instance this class relies on.
	 * 
	 * @var \PDO $PDO
	 */
	protected $PDO;
	
	/**
	 * Sets up a database transaction.
	 * 
	 * @param \PDO $PDO
	 */
	public function __construct(\PDO $PDO) {
		$this->PDO=$PDO;
	}
	
	/**
	 * Starts a transaction
	 */
	public function begin() {
		$this->PDO->beginTransaction();
	}
	
	/**
	 * Commits transaction.
	 */
	public function commit() {
		$this->PDO->commit();
	}
	
	/**
	 * Rolls back transaction.
	 */
	public function rollback() {
		$this->PDO->rollBack();
	}
}