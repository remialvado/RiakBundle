<?php
namespace Kbrw\RiakBundle\Tests\Service\WebserviceClient\Guzzle;

use Kbrw\RiakBundle\Service\WebserviceClient\Guzzle\SimpleGuzzleClientProvider;

/**
 * @author remi
 */
class SimpleGuzzleClientProviderTest extends GuzzleClientProviderTestAbstract
{
    public function getGuzzleClientProvider()
    {
        return new SimpleGuzzleClientProvider();
    }
}
