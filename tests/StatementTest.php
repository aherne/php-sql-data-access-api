<?php
namespace Test\Lucinda\SQL;

use Lucinda\SQL\Connection;
use Lucinda\SQL\DataSource;
use Lucinda\UnitTest\Validator\Strings;

class StatementTest
{
    private Connection $connection;

    public function __construct()
    {
        $xml = \simplexml_load_file(dirname(__DIR__)."/unit-tests.xml");
        $this->connection = new Connection();
        $this->connection->connect(new DataSource($xml->sql->server));
    }

    public function quote()
    {
        return (new Strings($this->connection->statement()->quote("John")))->assertEquals("'John'");
    }

    public function execute()
    {
        return (new Strings($this->connection->statement()->execute("SELECT first_name FROM users WHERE id=1")->toValue()))->assertEquals("John");
    }
}
