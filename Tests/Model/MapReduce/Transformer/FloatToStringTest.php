<?php
namespace Kbrw\RiakBundle\Tests\Model\MapReduce\Transformer;

use Kbrw\RiakBundle\Model\MapReduce\Transformer\FloatToString;
use Kbrw\RiakBundle\Tests\Model\MapReduce\KeyFilterTestCase;

class FloatToStringTest extends KeyFilterTestCase
{
    public function getExpectations()
    {
        return $this->values = array(
            array('[["float_to_string"]]', new FloatToString()),
        );
    }
}
