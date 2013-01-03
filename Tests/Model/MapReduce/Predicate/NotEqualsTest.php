<?php
namespace Kbrw\RiakBundle\Tests\Model\MapReduce\Predicate;

use Kbrw\RiakBundle\Model\MapReduce\Predicate\NotEquals;
use Kbrw\RiakBundle\Tests\Model\MapReduce\KeyFilterTestCase;

class NotEqualsTest extends KeyFilterTestCase
{
    public function getExpectations()
    {
        return $this->values = array(
            array('[["neq","foo"]]', new NotEquals(null, "foo")),
        );
    }
}
