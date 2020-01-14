<?php
namespace Lucinda\SQL;

/**
 * Encapsulates data source detection (itself encapsulating database server settings) from an XML tag
 */
class DataSourceDetection
{
    protected $dataSource;

    /**
     * DataSourceDetection constructor.
     * @param \SimpleXMLElement $databaseInfo XML tag containing data source info.
     */
    public function __construct(\SimpleXMLElement $databaseInfo)
    {
        $this->setDataSource($databaseInfo);
    }

    /**
     * Detects data source (itself encapsulating database server settings) from an XML tag
     *
     * @param \SimpleXMLElement $databaseInfo
     * @return mixed
     */
    protected function setDataSource(\SimpleXMLElement $databaseInfo): void
    {
        $dataSource = new DataSource();
        $dataSource->setDriverName((string) $databaseInfo["driver"]);
        $dataSource->setDriverOptions(array()); // currently, setting driver options isn't possible
        $dataSource->setHost((string) $databaseInfo["host"]);
        $dataSource->setPort((int) $databaseInfo["port"]);
        $dataSource->setUserName((string) $databaseInfo["username"]);
        $dataSource->setPassword((string) $databaseInfo["password"]);
        $dataSource->setSchema((string) $databaseInfo["schema"]);
        $dataSource->setCharset((string) $databaseInfo["charset"]);
        $this->dataSource = $dataSource;
    }

    /**
     * Gets detected data source
     *
     * @return DataSource
     */
    public function getDataSource(): DataSource
    {
        return $this->dataSource;
    }
}
