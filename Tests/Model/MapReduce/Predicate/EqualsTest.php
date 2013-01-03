<?php
namespace Kbrw\RiakBundle\Tests\Model\MapReduce\Predicate;

use Kbrw\RiakBundle\Model\MapReduce\Predicate\Equals;
use Kbrw\RiakBundle\Tests\Model\MapReduce\KeyFilterTestCase;

class EqualsTest extends KeyFilterTestCase
{
    public function getExpectations()
    {
        return array(
            array('[["eq","basho"]]', new Equals(null, "basho")),
        );
    }
}