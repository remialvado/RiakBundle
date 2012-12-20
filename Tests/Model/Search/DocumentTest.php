<?php
namespace Kbrw\RiakBundle\Tests\Model\Search;

use Kbrw\RiakBundle\Tests\Model\ModelTestCase;

class DocumentTest extends ModelTestCase
{

    protected $serializarionMethod          = "xml";
    protected $systemUnderTestFullClassName = "Kbrw\RiakBundle\Model\Search\Document";
    protected $testedModels                 = array("regular");
    
    protected function getTestFileBasePath()
    {
        return dirname(__FILE__) . "/../../../Resources/tests/model/document";
    }
}