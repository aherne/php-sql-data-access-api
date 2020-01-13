<?php
namespace Lucinda\SQL;

/**
 * Binds SQL Data Access API with MVC STDOUT API (aka Servlets API) in order to detect a DataSource that will be automatically used later on when SQL server is queried
 */
class Wrapper
{
    /**
     * Binds SQL Data Access API to XML based on development environment and sets DataSource for later querying
     *
     * @param \SimpleXMLElement $xml
     * @param string $developmentEnvironment
     * @throws ConfigurationException If XML is improperly configured.
     */
    public function __construct(\SimpleXMLElement $xml, $developmentEnvironment)
    {
        $xml = $xml->servers->sql->{$developmentEnvironment};
        if (!empty($xml)) {
            if (!$xml->server) {
                throw new ConfigurationException("Server not set for environment!");
            }
            $xml = (array) $xml;
            if (is_array($xml["server"])) {
                foreach ($xml["server"] as $element) {
                    if (!isset($element["name"])) {
                        throw new ConfigurationException("Attribute 'name' is mandatory for 'server' tag");
                    }
                    $dsd = new DataSourceDetection($element);
                    ConnectionFactory::setDataSource((string) $element["name"], $dsd->getDataSource());
                }
            } else {
                $dsd = new DataSourceDetection($xml["server"]);
                ConnectionSingleton::setDataSource($dsd->getDataSource());
            }
        }
    }
}
