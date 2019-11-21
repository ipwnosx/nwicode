<?php

namespace Nwicode\Hook;

/**
 * Class Menu
 * @package Nwicode\Hook
 */
class Menu
{
    /**
     * @var array
     */
    public static $backoffice = [];

    /**
     * @var array
     */
    public static $editor = [];

    /**
     * @param $type
     * @param $payload
     * @param null $after
     */
    public static function addMenu ($type, $payload, $after = null)
    {
        // TBD!
    }
}