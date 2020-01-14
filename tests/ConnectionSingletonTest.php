<?php
namespace Test\Lucinda\SQL;

use Lucinda\SQL\ConnectionSingleton;
use Lucinda\UnitTest\Result;
use Lucinda\SQL\DataSourceDetection;

class ConnectionSingletonTest
{
    public function setDataSource()
    {
        $detector = new DataSourceDetection(\simplexml_load_file(dirname(__DIR__)."/unit-tests.xml")->servers->sql->local->server);
        ConnectionSingleton::setDataSource($detector->getDataSource());
        return new Result(true);
    }
    
    
    public function getInstance()
    {
        $connection = ConnectionSingleton::getInstance();
        return new Result($connection->createStatement()->execute("SELECT first_name FROM users WHERE id=1")->toValue()=="John");
    }
}
