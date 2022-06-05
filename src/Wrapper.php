<?php

namespace Lucinda\SQL;

/**
 * Reads server tags from XML into DataSource objects and injects latter into ConnectionSingleton/ConnectionFactory
 * classes to be used in querying later on
 */
class Wrapper
{
    /**
     * Binds SQL Data Access API to XML based on development environment and sets DataSource for later querying
     *
     * @param  \SimpleXMLElement $xml
     * @param  string            $developmentEnvironment
     * @throws ConfigurationException If XML is improperly configured.
     */
    public function __construct(\SimpleXMLElement $xml, $developmentEnvironment)
    {
        $xml = $xml->sql->{$developmentEnvironment};
        if (!empty($xml)) {
            if (!$xml->server) {
                throw new ConfigurationException("Server not set for environment!");
            }

            $dataSources = $this->getDataSources($xml);
            foreach ($dataSources as $serverName=>$dataSource) {
                ConnectionFactory::setDataSource($serverName, $dataSource);
            }
        }
    }

    /**
     * Gets data sources to inject
     *
     * @param  \SimpleXMLElement $xml
     * @return array<string,DataSource>
     * @throws ConfigurationException
     */
    private function getDataSources(\SimpleXMLElement $xml): array
    {
        $output = [];
        $xml = (array) $xml;
        if (is_array($xml["server"])) {
            foreach ($xml["server"] as $element) {
                $name = (string) $element["name"];
                if (!$name) {
                    throw new ConfigurationException("Attribute 'name' is mandatory for 'server' tag");
                }
                $output[$name] = new DataSource($element);
            }
        } else {
            $output[""] = new DataSource($xml["server"]);
        }
        return $output;
    }
}
