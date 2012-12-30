<?php
namespace Kbrw\RiakBundle\Tests\Model\Search;

use Kbrw\RiakBundle\Tests\Model\ModelTestCase;

class LstTest extends ModelTestCase
{

    protected $serializarionMethod          = "xml";
    protected $systemUnderTestFullClassName = "Kbrw\RiakBundle\Model\Search\Lst";
    protected $testedModels                 = array("regular");

    protected function getTestFileBasePath()
    {
        return dirname(__FILE__) . "/../../../Resources/tests/model/lst";
    }
}
