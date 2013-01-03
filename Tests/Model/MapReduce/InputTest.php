<?php
namespace Kbrw\RiakBundle\Tests\Model\MapReduce;

use Kbrw\RiakBundle\Model\MapReduce\Input;
use Kbrw\RiakBundle\Tests\BaseTestCase;

class InputTest extends BaseTestCase
{
    /**
     * @test
     */
    public function singleBucketToArray()
    {
        $input = new Input("invoices");
        $inputArray = $input->toArray();
        $this->assertEquals(1, count($inputArray));
        $this->assertEquals("invoices", $inputArray[0]);
    }

    /**
     * @test
     */
    public function bucketAndKeyToArray()
    {
        $input = new Input("invoices", "p1");
        $inputArray = $input->toArray();
        $this->assertEquals(2, count($inputArray));
        $this->assertEquals("invoices", $inputArray[0]);
        $this->assertEquals("p1", $inputArray[1]);
    }

    /**
     * @test
     */
    public function bucketAndKeyAndDataToArray()
    {
        $input = new Input("invoices", "p1", '{"foo":"bar"}');
        $inputArray = $input->toArray();
        $this->assertEquals(3, count($inputArray));
        $this->assertEquals("invoices", $inputArray[0]);
        $this->assertEquals("p1", $inputArray[1]);
        $this->assertEquals('{"foo":"bar"}', $inputArray[2]);
    }
}
