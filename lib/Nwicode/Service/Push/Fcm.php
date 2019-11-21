<?php

namespace Nwicode\Service\Push;

use Nwicode\CloudMessaging\Sender\Fcm as Sender;

/**
 * Class Fcm
 */
class Fcm extends Sender
{
    /**
     * Nwicode_Service_Push_Fcm constructor.
     * @param string $key
     */
    public function __construct($key)
    {
        parent::__construct($key);
    }
}