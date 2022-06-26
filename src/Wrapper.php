<?php
namespace Lucinda\SQL;

/**
 * Reads server tags from XML into DataSource objects and injects latter into ConnectionSingleton/ConnectionFactory classes
 * to be used in querying later on
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
        $xml = $xml->sql->{$developmentEnvironment};
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
                    ConnectionFactory::setDataSource((string) $element["name"], new DataSource($element));
                }
            } else {
                ConnectionFactory::setDataSource("", new DataSource($xml["server"]));
            }
        }
    }
}
