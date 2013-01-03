<?php
namespace Kbrw\RiakBundle\Tests\Model\MapReduce\Transformer;

use Kbrw\RiakBundle\Model\MapReduce\Transformer\StringToFloat;
use Kbrw\RiakBundle\Tests\Model\MapReduce\KeyFilterTestCase;

class StringToFloatTest extends KeyFilterTestCase
{
    public function getExpectations()
    {
        return $this->values = array(
            array('[["string_to_float"]]', new StringToFloat()),
        );
    }
}
