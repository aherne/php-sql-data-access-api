<?php
namespace Test\Lucinda\SQL;

use Lucinda\SQL\Connection;
use Lucinda\SQL\DataSource;
use Lucinda\UnitTest\Validator\Booleans;
use Lucinda\UnitTest\Validator\Strings;

class TransactionTest
{
    private Connection $connection;

    public function __construct()
    {
        $xml = \simplexml_load_file(dirname(__DIR__)."/unit-tests.xml");
        $this->connection = new Connection();
        $this->connection->connect(new DataSource($xml->sql->local->server));
    }

    public function begin()
    {
        $transaction = $this->connection->transaction();
        $transaction->begin();
        $result = (new Booleans($transaction->isActive()))->assertTrue();
        $transaction->rollback();
        return $result;
    }

    public function commit()
    {
        $value = (string) \microtime(true);
        $transaction = $this->connection->transaction();
        $transaction->begin();
        $this->connection->statement()->execute("INSERT INTO dump (value) VALUES ('".$value."')");
        $transaction->commit();
        return (new Strings($this->connection->statement()->execute("SELECT value FROM dump WHERE value='".$value."'")->toValue()))->assertEquals($value);
    }

    public function rollback()
    {
        $value = \microtime(true)."X";
        $transaction = $this->connection->transaction();
        $transaction->begin();
        $this->connection->statement()->execute("INSERT INTO dump (value) VALUES ('".$value."')");
        $transaction->rollback();
        return (new Strings($this->connection->statement()->execute("SELECT value FROM dump WHERE value='".$value."'")->toValue()))->assertEmpty();
    }

    public function isActive()
    {
        $transaction = $this->connection->transaction();
        $inactive = (new Booleans($transaction->isActive()))->assertFalse("inactive");
        $transaction->begin();
        $active = (new Booleans($transaction->isActive()))->assertTrue("active");
        $transaction->rollback();
        return [$inactive, $active];
    }
}
