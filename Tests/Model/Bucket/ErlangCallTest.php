<?php
namespace Kbrw\RiakBundle\Tests\Model\Bucket;

use Kbrw\RiakBundle\Tests\Model\ModelTestCase;
use Kbrw\RiakBundle\Model\Bucket\ErlangCall;

class ErlangCallTest extends ModelTestCase
{
    protected $serializarionMethod          = "json";
    protected $systemUnderTestFullClassName = "Kbrw\RiakBundle\Model\Bucket\ErlangCall";
    protected $testedModels                 = array("regular");

    protected function getTestFileBasePath()
    {
        return dirname(__FILE__) . "/../../../Resources/tests/model/erlangCall";
    }

    /**
     * @test
     */
    public function twoErlangCallsAreEquals()
    {
        $erlangCall = new ErlangCall("riak_search_kv_hook", "precommit");
        $this->assertTrue($erlangCall->equalTo(new ErlangCall("riak_search_kv_hook", "precommit")));
    }
}
