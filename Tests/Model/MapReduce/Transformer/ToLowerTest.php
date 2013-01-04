<?php
namespace Kbrw\RiakBundle\Tests\Model\MapReduce\Transformer;

use Kbrw\RiakBundle\Model\MapReduce\Transformer\ToLower;
use Kbrw\RiakBundle\Tests\Model\MapReduce\KeyFilterTestCase;

class ToLowerTest extends KeyFilterTestCase
{
    public function getExpectations()
    {
        return array(
            array('[["to_lower"]]', new ToLower()),
        );
    }
}
