<?php
namespace Lucinda\SQL;

/**
 * Exception thrown when SQL statement executed is invalid.
 */
class StatementException extends \Exception
{
    protected $query;
    
    /**
     * Gets value of sql statement that failed
     *
     * @param string $query
     */
    public function setQuery(string $query): void
    {
        return $this->query;
    }
    
    /**
     * Gets value of sql statement that failed
     *
     * @return string
     */
    public function getQuery(): string
    {
        return $this->query;
    }
}
