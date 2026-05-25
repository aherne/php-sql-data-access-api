<?php
namespace Test\Lucinda\SQL;

use Lucinda\SQL\StatementException;
use Lucinda\UnitTest\Validator\Strings;

class StatementExceptionTest
{
    private StatementException $object;

    public function __construct()
    {
        $this->object = new StatementException();
        $this->object->setQuery("SELECT 1");
    }

    public function setQuery()
    {
        $exception = new StatementException();
        $exception->setQuery("SELECT id FROM users");
        return (new Strings($exception->getQuery()))->assertEquals("SELECT id FROM users");
    }

    public function getQuery()
    {
        return (new Strings($this->object->getQuery()))->assertEquals("SELECT 1");
    }
}
