<?php
namespace Test\Lucinda\SQL;

use Lucinda\SQL\DataSource;
use Lucinda\SQL\ConnectionFactory;
use Lucinda\UnitTest\Result;

class ConnectionFactoryTest
{
    public function setDataSource()
    {
        ConnectionFactory::setDataSource("local", new DataSource(\simplexml_load_file(dirname(__DIR__)."/unit-tests.xml")->sql->local->server));
        return new Result(true);
    }
        

    public function getInstance()
    {
        $connection = ConnectionFactory::getInstance("local");
        return new Result($connection->statement()->execute("SELECT first_name FROM users WHERE id=1")->toValue()=="John");
    }
}
