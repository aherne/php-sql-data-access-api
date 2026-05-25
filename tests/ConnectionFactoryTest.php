<?php
namespace Test\Lucinda\SQL;

use Lucinda\SQL\Connection;
use Lucinda\SQL\ConnectionFactory;
use Lucinda\SQL\DataSource;
use Lucinda\UnitTest\Validator\Objects;

class ConnectionFactoryTest
{
    private DataSource $dataSource;

    public function __construct()
    {
        $xml = \simplexml_load_file(dirname(__DIR__)."/unit-tests.xml");
        $this->dataSource = new DataSource($xml->sql->local->server);
    }

    public function setDataSource()
    {
        ConnectionFactory::setDataSource("factory_set", $this->dataSource);
        return (new Objects(ConnectionFactory::getInstance("factory_set")))->assertInstanceOf(Connection::class);
    }

    public function getInstance()
    {
        ConnectionFactory::setDataSource("factory_get", $this->dataSource);
        return (new Objects(ConnectionFactory::getInstance("factory_get")))->assertInstanceOf(Connection::class);
    }
}
