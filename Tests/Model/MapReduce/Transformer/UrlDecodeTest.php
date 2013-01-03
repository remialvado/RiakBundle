<?php
namespace Kbrw\RiakBundle\Tests\Model\MapReduce\Transformer;

use Kbrw\RiakBundle\Model\MapReduce\Transformer\UrlDecode;
use Kbrw\RiakBundle\Tests\Model\MapReduce\KeyFilterTestCase;

class UrlDecodeTest extends KeyFilterTestCase
{
    public function getExpectations()
    {
        return $this->values = array(
            array('[["urldecode"]]', new UrlDecode()),
        );
    }
}
