<?php
namespace Kbrw\RiakBundle\Tests\Model\MapReduce\Predicate;

use Kbrw\RiakBundle\Model\MapReduce\Predicate\LessThan;
use Kbrw\RiakBundle\Tests\Model\MapReduce\KeyFilterTestCase;

class LessThanTest extends KeyFilterTestCase
{
    public function getExpectations()
    {
        return $this->values = array(
            array('[["less_than",10]]', new LessThan(null, 10)),
        );
    }
}
