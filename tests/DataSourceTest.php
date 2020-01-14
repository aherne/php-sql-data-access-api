<?php
namespace Test\Lucinda\SQL;

use Lucinda\SQL\DataSource;
use Lucinda\UnitTest\Result;

class DataSourceTest
{
    private $object;
    
    public function __construct()
    {
        $this->object = new DataSource();
    }

    public function setDriverName()
    {
        $this->object->setDriverName("mysql");
        return new Result(true);
    }
        

    public function getDriverName()
    {
        return new Result($this->object->getDriverName()=="mysql");
    }
        

    public function setDriverOptions()
    {
        $this->object->setDriverOptions([\PDO::ATTR_AUTOCOMMIT=>true]);
        return new Result(true);
    }
        

    public function getDriverOptions()
    {
        return new Result($this->object->getDriverOptions()==[\PDO::ATTR_AUTOCOMMIT=>true]);
    }
        

    public function setHost()
    {
        $this->object->setHost("127.0.0.1");
        return new Result(true);
    }
        

    public function getHost()
    {
        return new Result($this->object->getHost()=="127.0.0.1");
    }
        

    public function setPort()
    {
        $this->object->setPort(3306);
        return new Result(true);
    }
        

    public function getPort()
    {
        return new Result($this->object->getPort()==3306);
    }
        

    public function setUserName()
    {
        $this->object->setUserName("unit_test");
        return new Result(true);
    }
        

    public function getUserName()
    {
        return new Result($this->object->getUserName()=="unit_test");
    }
        

    public function setPassword()
    {
        $this->object->setPassword("test");
        return new Result(true);
    }
        

    public function getPassword()
    {
        return new Result($this->object->getPassword()=="test");
    }
        

    public function setSchema()
    {
        $this->object->setSchema("unit_tests");
        return new Result(true);
    }
        

    public function getSchema()
    {
        return new Result($this->object->getSchema() == "unit_tests");
    }
        

    public function setCharset()
    {
        $this->object->setCharset("UTF8");
        return new Result(true);
    }
        

    public function getCharset()
    {
        return new Result($this->object->getCharset() == "UTF8");
    }
}
