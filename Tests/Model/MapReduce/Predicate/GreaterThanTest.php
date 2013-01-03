<?php
namespace Kbrw\RiakBundle\Tests\Model\MapReduce\Predicate;

use Kbrw\RiakBundle\Model\MapReduce\Predicate\GreaterThan;
use Kbrw\RiakBundle\Tests\Model\MapReduce\KeyFilterTestCase;

class GreaterThanTest extends KeyFilterTestCase
{
    public function getExpectations()
    {
        return array(
            array('[["greater_than",50]]', new GreaterThan(null, 50)),
        );
    }
}
