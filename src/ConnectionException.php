<?php
namespace Lucinda\SQL;

/**
 * Exception thrown when connection to an SQL server fails
 */
class ConnectionException extends \Exception
{
    protected $hostName="";
        
    /**
     * Sets sql server host name in which error has occurred.
     *
     * @param string $hostName
     */
    public function setHostName(string $hostName): void
    {
        $this->hostName = $hostName;
    }
    
    /**
     * Gets sql server host name in which error has occurred.
     *
     * @return string
     */
    public function getHostName(): string
    {
        return $this->hostName;
    }
}
