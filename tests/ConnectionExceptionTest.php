<?php
namespace Test\Lucinda\SQL;

use Lucinda\SQL\ConnectionException;
use Lucinda\UnitTest\Validator\Strings;

class ConnectionExceptionTest
{
    private ConnectionException $object;

    public function __construct()
    {
        $this->object = new ConnectionException();
        $this->object->setHostName("localhost");
    }

    public function setHostName()
    {
        $exception = new ConnectionException();
        $exception->setHostName("127.0.0.1");
        return (new Strings($exception->getHostName()))->assertEquals("127.0.0.1");
    }

    public function getHostName()
    {
        return (new Strings($this->object->getHostName()))->assertEquals("localhost");
    }
}
