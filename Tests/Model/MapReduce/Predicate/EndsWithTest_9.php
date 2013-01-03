<?php
namespace Kbrw\RiakBundle\Tests\Model\MapReduce\Predicate;

use Kbrw\RiakBundle\Model\MapReduce\Predicate\EndsWith;
use Kbrw\RiakBundle\Tests\Model\MapReduce\KeyFilterTestCase;

class EndsWithTest extends KeyFilterTestCase
{
    public function getExpectations()
    {
        return $this->values = array(
            array('[["ends_with","0603"]]', new EndsWith(null, "0603")),
        );
    }
}
