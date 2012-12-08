<?php
namespace Kbrw\RiakBundle\Tests\Model\Bucket;

use Kbrw\RiakBundle\Tests\Model\ModelTestCase;

class ErlangCallTest extends ModelTestCase
{
    protected $serializarionMethod          = "json";
    protected $systemUnderTestFullClassName = "Kbrw\RiakBundle\Model\Bucket\ErlangCall";
    protected $testedModels                 = array("regular");
    
    protected function getTestFileBasePath()
    {
        return dirname(__FILE__) . "/../../../Resources/tests/model/erlangCall";
    }
}