<?php
namespace Kbrw\RiakBundle\Tests\Model\MapReduce\Transformer;

use Kbrw\RiakBundle\Model\MapReduce\Transformer\IntToString;
use Kbrw\RiakBundle\Tests\Model\MapReduce\KeyFilterTestCase;

class IntToStringTest extends KeyFilterTestCase
{
    public function getExpectations()
    {
        return array(
            array('[["int_to_string"]]', new IntToString()),
        );
    }
}
