<?php

namespace Kbrw\RiakBundle\Service\WebserviceClient\Guzzle;

use Guzzle\Service\Client;

/**
 * @author remi
 */
class SimpleGuzzleClientProvider implements GuzzleClientProviderInterface
{
    public function getClient($baseUrl = null, $config = null)
    {
        return new Client($baseUrl, $config);
    }
}