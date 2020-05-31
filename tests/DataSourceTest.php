<?php
namespace Test\Lucinda\SQL;

use Lucinda\SQL\DataSource;
use Lucinda\UnitTest\Result;

class DataSourceTest
{
    private $object;
    
    public function __construct()
    {
        $this->object = new DataSource(\simplexml_load_file(dirname(__DIR__)."/unit-tests.xml")->sql->local->server);
    }
        

    public function getDriverName()
    {
        return new Result($this->object->getDriverName()=="mysql");
    }

    public function getDriverOptions()
    {
        return new Result($this->object->getDriverOptions()==[\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION]);
    }

    public function getHost()
    {
        return new Result($this->object->getHost()=="127.0.0.1");
    }

    public function getPort()
    {
        return new Result($this->object->getPort()==3306);
    }

    public function getUserName()
    {
        return new Result($this->object->getUserName()=="unit_test");
    }

    public function getPassword()
    {
        return new Result($this->object->getPassword()=="test");
    }

    public function getSchema()
    {
        return new Result($this->object->getSchema() == "unit_tests");
    }

    public function getCharset()
    {
        return new Result($this->object->getCharset() == "UTF8");
    }
}
