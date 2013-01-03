<?php
namespace Kbrw\RiakBundle\Tests\Model\MapReduce\Predicate;

use Kbrw\RiakBundle\Model\MapReduce\Predicate\Between;
use Kbrw\RiakBundle\Tests\Model\MapReduce\KeyFilterTestCase;

class BetweenTest extends KeyFilterTestCase
{
    public function getExpectations()
    {
        return array(
            array('[["between",10,20,false]]', new Between(null, 10, 20, false)),
        );
    }
}
