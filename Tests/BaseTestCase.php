<?php
namespace Kbrw\RiakBundle\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

abstract class BaseTestCase extends WebTestCase {
    
    protected function setup() {
        parent::setup();
        self::$kernel = self::createKernel();
        self::$kernel->boot();
    }
    
    protected function getService($serviceId) {
        return static::$kernel->getContainer()->get($serviceId);
    }
}