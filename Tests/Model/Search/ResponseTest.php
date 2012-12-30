<?php
namespace Kbrw\RiakBundle\Tests\Model\Search;

use Kbrw\RiakBundle\Tests\Model\ModelTestCase;

class ResponseTest extends ModelTestCase
{

    protected $serializarionMethod          = "xml";
    protected $systemUnderTestFullClassName = "Kbrw\RiakBundle\Model\Search\Response";
    protected $testedModels                 = array("regular");

    protected function getTestFileBasePath()
    {
        return dirname(__FILE__) . "/../../../Resources/tests/model/response";
    }
}
