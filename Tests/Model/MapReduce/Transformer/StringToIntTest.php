<?php
namespace Kbrw\RiakBundle\Tests\Model\MapReduce\Transformer;

use Kbrw\RiakBundle\Model\MapReduce\Transformer\StringToInt;
use Kbrw\RiakBundle\Tests\Model\MapReduce\KeyFilterTestCase;

class StringToIntTest extends KeyFilterTestCase
{
    public function getExpectations()
    {
        return $this->values = array(
            array('[["string_to_int"]]', new StringToInt()),
        );
    }
}
