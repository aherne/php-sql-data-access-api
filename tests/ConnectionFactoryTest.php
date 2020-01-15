<?php
namespace Test\Lucinda\SQL;

use Lucinda\SQL\DataSourceDetection;
use Lucinda\SQL\ConnectionFactory;
use Lucinda\UnitTest\Result;

class ConnectionFactoryTest
{
    public function setDataSource()
    {
        $detector = new DataSourceDetection(\simplexml_load_file(dirname(__DIR__)."/unit-tests.xml")->sql->local->server);
        ConnectionFactory::setDataSource("local", $detector->getDataSource());
        return new Result(true);
    }
        

    public function getInstance()
    {
        $connection = ConnectionFactory::getInstance("local");
        return new Result($connection->createStatement()->execute("SELECT first_name FROM users WHERE id=1")->toValue()=="John");
    }
}
