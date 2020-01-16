<?php
namespace Test\Lucinda\SQL;

use Lucinda\SQL\DataSourceDetection;
use Lucinda\SQL\Connection;
use Lucinda\UnitTest\Result;

class TransactionTest
{
    private $connection;
    
    public function __construct()
    {
        $detector = new DataSourceDetection(\simplexml_load_file(dirname(__DIR__)."/unit-tests.xml")->sql->local->server);
        $connection = new Connection();
        $connection->connect($detector->getDataSource());
        $this->connection = $connection;
    }

    public function begin()
    {
        return new Result(true); // begin can only be tested through commit/rollback
    }
        

    public function commit()
    {
        $value = microtime(true);
        $this->connection->transaction()->begin();
        $this->connection->statement()->execute("INSERT INTO dump (value) VALUES ('".$value."')");
        $this->connection->transaction()->commit();
        return new Result($this->connection->statement()->execute("SELECT id FROM dump WHERE value='".$value."'")->toValue());
    }
        

    public function rollback()
    {
        $value = microtime(true)."X";
        $this->connection->transaction()->begin();
        $this->connection->statement()->execute("INSERT INTO dump (value) VALUES ('".$value."')");
        $this->connection->transaction()->rollback();
        return new Result($this->connection->statement()->execute("SELECT id FROM dump WHERE value='".$value."'")->toValue()?false:true);
    }
}
