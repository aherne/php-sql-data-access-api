<?php
namespace Test\Lucinda\SQL;

use Lucinda\SQL\Wrapper;
use Lucinda\SQL\ConfigurationException;
use Lucinda\UnitTest\Result;
use Lucinda\SQL\ConnectionFactory;

class WrapperTest
{
    public function test()
    {
        $results = [];
        try {
            new Wrapper(\simplexml_load_file(dirname(__DIR__)."/unit-tests.xml"), "local");
            $results[] = new Result(true, "tested wrapping");
        } catch (ConfigurationException $e) {
            $results[] = new Result(false, "tested wrapping");
        }
        
        $connection = ConnectionFactory::getInstance("");
        $results[] = new Result(($connection->statement()->execute("SELECT first_name FROM users WHERE id=1")->toValue()=="John"), "tested binding");
        return $results;
    }
}
