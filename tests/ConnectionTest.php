<?php
namespace Test\Lucinda\SQL;

use Lucinda\SQL\Connection;
use Lucinda\SQL\DataSource;
use Lucinda\SQL\PreparedStatement;
use Lucinda\SQL\Statement;
use Lucinda\SQL\Transaction;
use Lucinda\UnitTest\Validator\Booleans;
use Lucinda\UnitTest\Validator\Objects;
use Lucinda\UnitTest\Validator\Strings;

class ConnectionTest
{
    private Connection $connection;
    private DataSource $dataSource;

    public function __construct()
    {
        $xml = \simplexml_load_file(dirname(__DIR__)."/unit-tests.xml");
        $this->dataSource = new DataSource($xml->sql->server);
        $this->connection = new Connection();
        $this->connection->connect($this->dataSource);
    }

    public function connect()
    {
        $connection = new Connection();
        $connection->connect($this->dataSource);
        return (new Strings($connection->statement()->execute("SELECT first_name FROM users WHERE id=1")->toValue()))->assertEquals("John");
    }

    public function transaction()
    {
        return (new Objects($this->connection->transaction()))->assertInstanceOf(Transaction::class);
    }

    public function statement()
    {
        return (new Objects($this->connection->statement()))->assertInstanceOf(Statement::class);
    }

    public function preparedStatement()
    {
        return (new Objects($this->connection->preparedStatement()))->assertInstanceOf(PreparedStatement::class);
    }

    public function keepAlive()
    {
        $this->connection->keepAlive();
        return (new Booleans(true))->assertTrue();
    }

    public function reconnect()
    {
        $this->connection->reconnect();
        return (new Strings($this->connection->statement()->execute("SELECT first_name FROM users WHERE id=1")->toValue()))->assertEquals("John");
    }

    public function disconnect()
    {
        $this->connection->disconnect();
        try {
            $this->connection->statement();
            $disconnected = false;
        } catch (\Lucinda\SQL\ConnectionException) {
            $disconnected = true;
        }
        $this->connection->connect($this->dataSource);
        return (new Booleans($disconnected))->assertTrue();
    }
}
