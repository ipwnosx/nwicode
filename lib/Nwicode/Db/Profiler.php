<?php

/**
 * Class Nwicode_Db_Profiler
 */
class Nwicode_Db_Profiler extends Zend_Db_Profiler
{

    /**
     * counter of the total elapsed time
     * @var double
     */
    protected $_totalElapsedTime;

    /**
     * Nwicode_Db_Profiler constructor.
     * @param bool $enabled
     */
    public function __construct($enabled = false)
    {
        parent::__construct($enabled);
    }

    /**
     * @param int $queryId
     * @return string|void
     * @throws Zend_Db_Profiler_Exception
     */
    public function queryEnd($queryId)
    {
        $state = parent::queryEnd($queryId);

        if (!$this->getEnabled() || $state == self::IGNORED) {
            return;
        }

        # get profile of the current query
        $profile = $this->getQueryProfile($queryId);

        # update totalElapsedTime counter
        $this->_totalElapsedTime += $profile->getElapsedSecs();

        Nwicode_Debug::addProfile($profile);
    }
}