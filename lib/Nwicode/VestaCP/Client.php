<?php

namespace Nwicode\VestaCP;

use \Goutte\Client as VestaClient;

/**
 * Class Client
 * @package Nwicode\VestaCP
 */
class Client extends VestaClient
{

    /**
     * @param $method
     * @param $endpoint
     * @param array $parameters
     * @param array $files
     * @param array $server
     * @param null $content
     * @param bool $changeHistory
     * @return \Symfony\Component\DomCrawler\Crawler
     */
    public function _request($method, $endpoint, array $parameters = [], array $files = [], array $server = [], $content = null, $changeHistory = true)
    {
        return $this->request($method, $endpoint, $parameters, $files, $server, $content, $changeHistory);
    }
}