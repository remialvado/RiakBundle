<?php
namespace Kbrw\RiakBundle\Tests\Model\MapReduce\Operator;

use Kbrw\RiakBundle\Model\MapReduce\Operator\LogicalAnd;
use Kbrw\RiakBundle\Tests\Model\MapReduce\KeyFilterTestCase;
use Kbrw\RiakBundle\Model\MapReduce\Predicate\StartsWith;
use Kbrw\RiakBundle\Model\MapReduce\Predicate\EndsWith;

class LogicalAndTest extends KeyFilterTestCase
{
    public function getExpectations()
    {
        return array(
            array('["and",[["ends_with","0603"]],[["starts_with","basho"]]]', new LogicalAnd(null, array(new EndsWith(null, "0603"), new StartsWith(null, "basho")))),
        );
    }
}
