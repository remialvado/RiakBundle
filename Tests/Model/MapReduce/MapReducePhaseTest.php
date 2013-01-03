<?php
namespace Kbrw\RiakBundle\Tests\Model\MapReduce;

use Kbrw\RiakBundle\Tests\Model\ModelTestCase;

class MapReducePhaseTest extends ModelTestCase
{
    protected $serializarionMethod          = "json";
    protected $systemUnderTestFullClassName = "Kbrw\RiakBundle\Model\MapReduce\MapReducePhase";
    protected $testedModels                 = array("argument", "builtin", "regular", "storedFunction", "erlang");
    protected $isUnserializationTestable    = false; // no need to support that

    protected function getTestFileBasePath()
    {
        return dirname(__FILE__) . "/../../../Resources/tests/model/mapReducePhase";
    }
}
