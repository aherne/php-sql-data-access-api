<?php
namespace Test\Lucinda\SQL;

use Lucinda\SQL\Connection;
use Lucinda\SQL\DataSource;
use Lucinda\UnitTest\Validator\Arrays;
use Lucinda\UnitTest\Validator\Integers;
use Lucinda\UnitTest\Validator\Strings;

class StatementResultsTest
{
    private Connection $connection;

    public function __construct()
    {
        $xml = \simplexml_load_file(dirname(__DIR__)."/unit-tests.xml");
        $this->connection = new Connection();
        $this->connection->connect(new DataSource($xml->sql->server));
    }

    public function getInsertId()
    {
        $statement = $this->connection->preparedStatement();
        $statement->prepare("INSERT INTO dump (value) VALUES (:value)");
        return (new Integers($statement->execute([":value"=>(string) \microtime(true)])->getInsertId()))->assertGreater(0);
    }

    public function getAffectedRows()
    {
        $insert = $this->connection->preparedStatement();
        $insert->prepare("INSERT INTO dump (value) VALUES (:value)");
        $id = $insert->execute([":value"=>(string) \microtime(true)])->getInsertId();

        $update = $this->connection->preparedStatement();
        $update->prepare("UPDATE dump SET value=:value WHERE id=:id");
        return (new Integers($update->execute([":value"=>(string) \microtime(true), ":id"=>$id])->getAffectedRows()))->assertEquals(1);
    }

    public function toValue()
    {
        $statement = $this->connection->preparedStatement();
        $statement->prepare("SELECT first_name FROM users WHERE id=:id");
        return (new Strings($statement->execute([":id"=>1])->toValue()))->assertEquals("John");
    }

    public function toRow()
    {
        $statement = $this->connection->preparedStatement();
        $statement->prepare("SELECT * FROM users WHERE id=:id");
        return (new Arrays($statement->execute([":id"=>1])->toRow()))->assertEquals(["id"=>1, "first_name"=>"John", "last_name"=>"Doe"]);
    }

    public function toColumn()
    {
        $statement = $this->connection->preparedStatement();
        $statement->prepare("SELECT first_name FROM users WHERE last_name=:last_name ORDER BY id");
        return (new Arrays($statement->execute([":last_name"=>"Doe"])->toColumn()))->assertEquals(["John", "Jane"]);
    }

    public function toMap()
    {
        $statement = $this->connection->preparedStatement();
        $statement->prepare("SELECT first_name, last_name FROM users WHERE last_name=:last_name ORDER BY id");
        return (new Arrays($statement->execute([":last_name"=>"Doe"])->toMap("first_name", "last_name")))->assertEquals(["John"=>"Doe", "Jane"=>"Doe"]);
    }

    public function toList()
    {
        $statement = $this->connection->preparedStatement();
        $statement->prepare("SELECT * FROM users WHERE last_name=:last_name ORDER BY id");
        return (new Arrays($statement->execute([":last_name"=>"Doe"])->toList()))->assertEquals([
            ["id"=>1, "first_name"=>"John", "last_name"=>"Doe"],
            ["id"=>2, "first_name"=>"Jane", "last_name"=>"Doe"]
        ]);
    }
}
