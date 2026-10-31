<?php
namespace Test\Lucinda\SQL;

use Lucinda\SQL\DataSource;
use Lucinda\UnitTest\Validator\Arrays;
use Lucinda\UnitTest\Validator\Integers;
use Lucinda\UnitTest\Validator\Strings;

class DataSourceTest
{
    private DataSource $object;
    private DataSource $sqlite;

    public function __construct()
    {
        $xml = \simplexml_load_file(dirname(__DIR__)."/unit-tests.xml");
        $this->object = new DataSource($xml->sql->server);
        $this->sqlite = new DataSource(\simplexml_load_string(
            '<server driver="sqlite" host="localhost" username="unit_test" password="" file=":memory:"/>'
        ));
    }

    public function getDriverName()
    {
        return (new Strings($this->object->getDriverName()))->assertEquals("mysql");
    }

    public function getDriverOptions()
    {
        return (new Arrays($this->object->getDriverOptions()))->assertContainsKey(\PDO::ATTR_ERRMODE);
    }

    public function getHost()
    {
        return (new Strings($this->object->getHost()))->assertEquals("127.0.0.1");
    }

    public function getPort()
    {
        return (new Integers($this->object->getPort()))->assertEquals(3306);
    }

    public function getUserName()
    {
        return (new Strings($this->object->getUserName()))->assertEquals("unit_test");
    }

    public function getPassword()
    {
        return (new Strings($this->object->getPassword()))->assertEquals("T3st_Password!");
    }

    public function getSchema()
    {
        return (new Strings($this->object->getSchema()))->assertEquals("unit_tests");
    }

    public function getCharset()
    {
        return (new Strings($this->object->getCharset()))->assertEquals("UTF8");
    }

    public function getFile()
    {
        return (new Strings($this->sqlite->getFile()))->assertEquals(":memory:");
    }
}
