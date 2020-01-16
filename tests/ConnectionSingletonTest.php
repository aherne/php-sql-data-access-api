<?php
namespace Test\Lucinda\SQL;

use Lucinda\SQL\ConnectionSingleton;
use Lucinda\UnitTest\Result;
use Lucinda\SQL\DataSource;

class ConnectionSingletonTest
{
    public function setDataSource()
    {
        ConnectionSingleton::setDataSource(new DataSource(\simplexml_load_file(dirname(__DIR__)."/unit-tests.xml")->sql->local->server));
        return new Result(true);
    }
    
    
    public function getInstance()
    {
        $connection = ConnectionSingleton::getInstance();
        return new Result($connection->statement()->execute("SELECT first_name FROM users WHERE id=1")->toValue()=="John");
    }
}
