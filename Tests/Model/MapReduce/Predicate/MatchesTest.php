<?php
namespace Kbrw\RiakBundle\Tests\Model\MapReduce\Predicate;

use Kbrw\RiakBundle\Model\MapReduce\Predicate\Matches;
use Kbrw\RiakBundle\Tests\Model\MapReduce\KeyFilterTestCase;

class MatchesTest extends KeyFilterTestCase
{
    public function getExpectations()
    {
        return $this->values = array(
            array('[["matches","solutions"]]', new Matches(null, "solutions")),
        );
    }
}
