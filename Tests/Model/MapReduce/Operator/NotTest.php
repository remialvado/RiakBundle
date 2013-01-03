<?php
namespace Kbrw\RiakBundle\Tests\Model\MapReduce\Operator;

use Kbrw\RiakBundle\Model\MapReduce\Operator\Not;
use Kbrw\RiakBundle\Tests\Model\MapReduce\KeyFilterTestCase;
use Kbrw\RiakBundle\Model\MapReduce\Predicate\Matches;

class NotTest extends KeyFilterTestCase
{
    public function getExpectations()
    {
        return array(
            array('["not",[["matches","solution"]]]', new Not(null, array(new Matches(null, "solution")))),
        );
    }
}
