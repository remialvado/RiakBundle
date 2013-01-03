<?php
namespace Kbrw\RiakBundle\Tests\Model\MapReduce\Predicate;

use Kbrw\RiakBundle\Model\MapReduce\Predicate\MemberOf;
use Kbrw\RiakBundle\Tests\Model\MapReduce\KeyFilterTestCase;

class MemberOfTest extends KeyFilterTestCase
{
    public function getExpectations()
    {
        return array(
            array('[["set_member","basho","google","yahoo"]]', new MemberOf(null, array("basho", "google", "yahoo"))),
        );
    }
}
