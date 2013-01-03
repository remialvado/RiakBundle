<?php
namespace Kbrw\RiakBundle\Tests\Model\MapReduce\Predicate;

use Kbrw\RiakBundle\Model\MapReduce\Predicate\GreaterThanOrEquals;
use Kbrw\RiakBundle\Tests\Model\MapReduce\KeyFilterTestCase;

class GreaterThanOrEqualsTest extends KeyFilterTestCase
{
    public function getExpectations()
    {
        return array(
            array('[["greater_than_eq",2000]]', new GreaterThanOrEquals(null, 2000)),
        );
    }
}
