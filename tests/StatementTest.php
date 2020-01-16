<?php
namespace Test\Lucinda\SQL;

use Lucinda\SQL\DataSourceDetection;
use Lucinda\SQL\Connection;
use Lucinda\UnitTest\Result;

class StatementTest
{
    private $object;
    
    public function __construct()
    {
        $detector = new DataSourceDetection(\simplexml_load_file(dirname(__DIR__)."/unit-tests.xml")->sql->local->server);
        $connection = new Connection();
        $connection->connect($detector->getDataSource());
        $this->object = $connection->statement();
    }

    public function quote()
    {
        return new Result($this->object->quote("asd")=="'asd'");
    }
        

    public function execute()
    {
        return new Result($this->object->execute("SELECT first_name FROM users WHERE id=1")->toValue()=="John");
    }
}
