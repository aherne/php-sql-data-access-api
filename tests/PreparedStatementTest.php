<?php
namespace Test\Lucinda\SQL;

use Lucinda\SQL\Connection;
use Lucinda\SQL\DataSource;
use Lucinda\UnitTest\Validator\Booleans;
use Lucinda\UnitTest\Validator\Strings;

class PreparedStatementTest
{
    private Connection $connection;

    public function __construct()
    {
        $xml = \simplexml_load_file(dirname(__DIR__)."/unit-tests.xml");
        $this->connection = new Connection();
        $this->connection->connect(new DataSource($xml->sql->server));
    }

    public function prepare()
    {
        $statement = $this->connection->preparedStatement();
        $statement->prepare("SELECT first_name FROM users WHERE id=:id");
        return (new Booleans(true))->assertTrue();
    }

    public function bind()
    {
        $statement = $this->connection->preparedStatement();
        $statement->prepare("SELECT first_name FROM users WHERE id=:id");
        $statement->bind(":id", 1, \PDO::PARAM_INT);
        return (new Strings($statement->execute()->toValue()))->assertEquals("John");
    }

    public function execute()
    {
        $statement = $this->connection->preparedStatement();
        $statement->prepare("SELECT first_name FROM users WHERE id=:id");
        return (new Strings($statement->execute([":id"=>1])->toValue()))->assertEquals("John");
    }
}
