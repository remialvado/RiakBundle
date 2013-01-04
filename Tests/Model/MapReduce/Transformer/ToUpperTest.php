<?php
namespace Kbrw\RiakBundle\Tests\Model\MapReduce\Transformer;

use Kbrw\RiakBundle\Model\MapReduce\Transformer\ToUpper;
use Kbrw\RiakBundle\Tests\Model\MapReduce\KeyFilterTestCase;

class ToUpperTest extends KeyFilterTestCase
{
    public function getExpectations()
    {
        return array(
            array('[["to_upper"]]', new ToUpper()),
        );
    }
}
