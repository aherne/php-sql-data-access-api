<?php
namespace Test\Lucinda\SQL;

use Lucinda\SQL\Connection;
use Lucinda\SQL\DataSource;
use Lucinda\UnitTest\Result;

class PreparedStatementTest
{
    private $object;
    
    public function __construct()
    {
        $connection = new Connection();
        $connection->connect(new DataSource(\simplexml_load_file(dirname(__DIR__)."/unit-tests.xml")->sql->local->server));
        $this->object = $connection->preparedStatement();
    }

    public function prepare()
    {
        $this->object->prepare("SELECT first_name FROM users WHERE id=:id");
        return new Result(true);
    }
        

    public function bind()
    {
        $this->object->bind(":id", 1, \PDO::PARAM_INT);
        return new Result(true);
    }
        

    public function execute()
    {
        return new Result($this->object->execute()->toValue()=="John");
    }
}
