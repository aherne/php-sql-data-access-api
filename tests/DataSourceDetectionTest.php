<?php
namespace Test\Lucinda\SQL;

use Lucinda\SQL\DataSourceDetection;
use Lucinda\UnitTest\Result;

class DataSourceDetectionTest
{
    public function getDataSource()
    {
        $detector = new DataSourceDetection(\simplexml_load_file(dirname(__DIR__)."/unit-tests.xml")->sql->local->server);
        $dataSource = $detector->getDataSource();
        return new Result($dataSource->getUserName()=="unit_test" && $dataSource->getPassword()=="test");
    }
}
