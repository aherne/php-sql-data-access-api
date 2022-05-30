<?php

namespace Test\Lucinda\SQL;

use Lucinda\SQL\ConnectionException;
use Lucinda\UnitTest\Result;

class ConnectionExceptionTest
{
    private $object;

    public function __construct()
    {
        $this->object = new ConnectionException("connection failed");
    }

    public function setHostName()
    {
        $this->object->setHostName("localhost");
        return new Result(true);
    }


    public function getHostName()
    {
        return new Result($this->object->getHostName()=="localhost");
    }
}
