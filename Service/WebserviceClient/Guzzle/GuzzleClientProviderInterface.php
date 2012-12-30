<?php
namespace Kbrw\RiakBundle\Service\WebserviceClient\Guzzle;

/**
 * You can inject GuzzleClientProvider that will log requests, handle cache, ... using symfony EventDispatcher.
 * See Guzzle documentation for specific details.
 */
interface GuzzleClientProviderInterface
{

    /**
     * @return \Guzzle\Service\Client
     */
    public function getClient($baseUrl = null, $config = null);
}
