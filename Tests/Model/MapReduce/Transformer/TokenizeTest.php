<?php
namespace Kbrw\RiakBundle\Tests\Model\MapReduce\Transformer;

use Kbrw\RiakBundle\Model\MapReduce\Transformer\Tokenize;
use Kbrw\RiakBundle\Tests\Model\MapReduce\KeyFilterTestCase;

class TokenizeTest extends KeyFilterTestCase
{
    public function getExpectations()
    {
        return array(
            array('[["tokenize","\/",4]]', new Tokenize(null, "/", 4)),
        );
    }
}
