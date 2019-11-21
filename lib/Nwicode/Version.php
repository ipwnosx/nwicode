<?php

namespace Nwicode;

/**
 * Class \Nwicode\Version
 */
class Version
{
    const TYPE = 'SAE';
    const NAME = 'FREE';
    const VERSION = '1.2.9';
    const NATIVE_VERSION = '12';
    const API_VERSION = '4';

    /**
     * @param $type
     * @return bool
     */
    static function is($type)
    {
        if (is_array($type)) {
            foreach ($type as $t) {
                if (self::TYPE == strtoupper($t)) {
                    return true;
                }
            }
        }
        return self::TYPE == strtoupper($type);
    }
}
