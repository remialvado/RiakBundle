<?php
namespace Kbrw\RiakBundle\Tests\Model\MapReduce;

use Kbrw\RiakBundle\Tests\Model\ModelTestCase;

class LinkPhaseTest extends ModelTestCase
{
    protected $serializarionMethod          = "json";
    protected $systemUnderTestFullClassName = "Kbrw\RiakBundle\Model\MapReduce\LinkPhase";
    protected $testedModels                 = array("regular");
    protected $isUnserializationTestable    = false; // no need to support that

    protected function getTestFileBasePath()
    {
        return dirname(__FILE__) . "/../../../Resources/tests/model/linkPhase";
    }
}
