<?php

namespace Nwicode\Wrapper;

/**
 * Class Nwicode_Wrapper_Sqlite_Exception
 */
class SqliteException extends \Exception
{
    /**
     * Nwicode_Wrapper_Sqlite_Exception constructor.
     * @param $query
     * @param $outputMessage
     */
    public function __construct($query, $outputMessage)
    {
        $this->message = "Error with query '$query'\n" . implode("\n", $outputMessage);
    }
}
