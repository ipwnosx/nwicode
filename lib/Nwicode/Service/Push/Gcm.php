<?php

namespace Nwicode\Service\Push;

use Nwicode\CloudMessaging\Sender\Gcm as Sender;

/**
 * Class Nwicode_Service_Push_Gcm
 */
class Gcm extends Sender
{
    /**
     * Nwicode_Service_Push_Gcm constructor.
     * @param string $key
     */
    public function __construct($key)
    {
        parent::__construct($key);
    }
}