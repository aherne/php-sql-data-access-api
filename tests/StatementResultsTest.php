<?php
namespace Test\Lucinda\SQL;

use Lucinda\SQL\DataSource;
use Lucinda\SQL\Connection;
use Lucinda\UnitTest\Result;

class StatementResultsTest
{
    private $connection;
    
    public function __construct()
    {
        $connection = new Connection();
        $connection->connect(new DataSource(\simplexml_load_file(dirname(__DIR__)."/unit-tests.xml")->sql->local->server));
        $this->connection = $connection;
    }

    public function getInsertId()
    {
        $statement = $this->connection->preparedStatement();
        $statement->prepare("INSERT INTO dump (value) VALUES (:value)");
        return new Result($statement->execute([":value"=>rand(0, 1000000)])->getInsertId()>0);
    }
        

    public function getAffectedRows()
    {
        $statement = $this->connection->preparedStatement();
        $statement->prepare("UPDATE dump SET value=:value WHERE id = 1");
        return new Result($statement->execute([":value"=>rand(0, 1000000)])->getAffectedRows()>0);
    }
        

    public function toValue()
    {
        $statement = $this->connection->preparedStatement();
        $statement->prepare("SELECT first_name FROM users WHERE id=:id");
        return new Result($statement->execute([":id"=>1])->toValue()=="John");
    }
        

    public function toRow()
    {
        $statement = $this->connection->preparedStatement();
        $statement->prepare("SELECT * FROM users WHERE id=:id");
        return new Result($statement->execute([":id"=>1])->toRow()==["id"=>1, "first_name"=>"John", "last_name"=>"Doe"]);
    }
        

    public function toColumn()
    {
        $statement = $this->connection->preparedStatement();
        $statement->prepare("SELECT first_name FROM users WHERE last_name=:last_name");
        return new Result($statement->execute([":last_name"=>"Doe"])->toColumn()==["John", "Jane"]);
    }
        

    public function toMap()
    {
        $statement = $this->connection->preparedStatement();
        $statement->prepare("SELECT first_name, last_name FROM users WHERE last_name=:last_name");
        return new Result($statement->execute([":last_name"=>"Doe"])->toMap("first_name", "last_name")==["John"=>"Doe", "Jane"=>"Doe"]);
    }
        

    public function toList()
    {
        $statement = $this->connection->preparedStatement();
        $statement->prepare("SELECT * FROM users WHERE last_name=:last_name");
        return new Result($statement->execute([":last_name"=>"Doe"])->toList()==[
            ["id"=>1, "first_name"=>"John", "last_name"=>"Doe"],
            ["id"=>2, "first_name"=>"Jane", "last_name"=>"Doe"]
        ]);
    }
}
