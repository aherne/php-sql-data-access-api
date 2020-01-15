<?php
namespace Test\Lucinda\SQL;

use Lucinda\SQL\Connection;
use Lucinda\SQL\DataSourceDetection;
use Lucinda\UnitTest\Result;

class PreparedStatementTest
{
    private $object;
    
    public function __construct()
    {
        $detector = new DataSourceDetection(\simplexml_load_file(dirname(__DIR__)."/unit-tests.xml")->sql->local->server);
        $connection = new Connection();
        $connection->connect($detector->getDataSource());
        $this->object = $connection->createPreparedStatement();
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
