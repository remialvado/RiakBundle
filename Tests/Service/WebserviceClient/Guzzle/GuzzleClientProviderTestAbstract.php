<?php

namespace Kbrw\RiakBundle\Tests\Service\WebserviceClient\Guzzle;

/**
 * @author remi
 */
abstract class GuzzleClientProviderTestAbstract extends \PHPUnit_Framework_TestCase
{
    /**
     * @return \Kbrw\RiakBundle\Service\WebserviceClient\Guzzle\GuzzleClientProviderInterface
     */
    abstract function getGuzzleClientProvider();
    
    /**
     * @test
     */
    public function getClient()
    {
        $client = $this->getGuzzleClientProvider()->getClient("http://some.webservice.com/{foo}", array("foo" => "bar"));
        $this->assertTrue($client instanceof \Guzzle\Service\Client);
    }
}