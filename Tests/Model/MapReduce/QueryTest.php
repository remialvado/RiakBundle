<?php
namespace Kbrw\RiakBundle\Tests\Model\MapReduce;

use Kbrw\RiakBundle\Tests\Model\ModelTestCase;

class QueryTest extends ModelTestCase
{
    protected $serializarionMethod          = "json";
    protected $systemUnderTestFullClassName = "Kbrw\RiakBundle\Model\MapReduce\Query";
    protected $testedModels                 = array("basic", "keyFilter", "specialMap");
    protected $isUnserializationTestable    = false; // no need to support that

    protected function getTestFileBasePath()
    {
        return dirname(__FILE__) . "/../../../Resources/tests/model/query";
    }
}
