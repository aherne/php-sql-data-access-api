<?php

namespace Test\Lucinda\SQL;

use Lucinda\SQL\StatementException;
use Lucinda\UnitTest\Result;

class StatementExceptionTest
{
    private $object;

    public function __construct()
    {
        $this->object = new StatementException("query failed");
    }

    public function setQuery()
    {
        $this->object->setQuery("SELECT asd FROM users");
        return new Result(true);
    }


    public function getQuery()
    {
        return new Result($this->object->getQuery()=="SELECT asd FROM users");
    }
}
