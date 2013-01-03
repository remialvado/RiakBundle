<?php
namespace Kbrw\RiakBundle\Tests\Model\MapReduce\Predicate;

use Kbrw\RiakBundle\Model\MapReduce\Predicate\StartsWith;
use Kbrw\RiakBundle\Tests\Model\MapReduce\KeyFilterTestCase;

class StartsWithTest extends KeyFilterTestCase
{
    public function getExpectations()
    {
        return $this->values = array(
            array('[["starts_with","basho"]]', new StartsWith(null, "basho")),
        );
    }
}
