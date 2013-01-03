<?php
namespace Kbrw\RiakBundle\Tests\Model\MapReduce\Predicate;

use Kbrw\RiakBundle\Model\MapReduce\Predicate\LessThanOrEquals;
use Kbrw\RiakBundle\Tests\Model\MapReduce\KeyFilterTestCase;

class LessThanOrEqualsTest extends KeyFilterTestCase
{
    public function getExpectations()
    {
        return array(
            array('[["less_than_eq",-2]]', new LessThanOrEquals(null, -2)),
        );
    }
}
