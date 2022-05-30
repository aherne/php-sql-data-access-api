<?php

namespace Test\Lucinda\SQL;

use Lucinda\SQL\DataSource;
use Lucinda\SQL\Connection;
use Lucinda\UnitTest\Result;

class StatementTest
{
    private $object;

    public function __construct()
    {
        $connection = new Connection();
        $connection->connect(new DataSource(\simplexml_load_file(dirname(__DIR__)."/unit-tests.xml")->sql->local->server));
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
