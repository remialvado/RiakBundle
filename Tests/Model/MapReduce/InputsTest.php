<?php
namespace Kbrw\RiakBundle\Tests\Model\MapReduce;

use Kbrw\RiakBundle\Model\MapReduce\Inputs;
use Kbrw\RiakBundle\Tests\Model\ModelTestCase;

class InputsTest extends ModelTestCase
{
    protected $serializarionMethod          = "json";
    protected $systemUnderTestFullClassName = "Kbrw\RiakBundle\Model\MapReduce\Inputs";
    protected $testedModels                 = array("basic", "and", "not", "complex");
    protected $isUnserializationTestable    = false; // no need to support that

    protected function getTestFileBasePath()
    {
        return dirname(__FILE__) . "/../../../Resources/tests/model/inputs";
    }
}
