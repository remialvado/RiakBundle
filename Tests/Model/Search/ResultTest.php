<?php
namespace Kbrw\RiakBundle\Tests\Model\Search;

use Kbrw\RiakBundle\Tests\Model\ModelTestCase;
use Kbrw\RiakBundle\Model\Search\Result;
use Kbrw\RiakBundle\Model\Search\Document;
use Kbrw\RiakBundle\Model\Search\String;

class ResultTest extends ModelTestCase
{

    protected $serializarionMethod          = "xml";
    protected $systemUnderTestFullClassName = "Kbrw\RiakBundle\Model\Search\Result";
    protected $testedModels                 = array("regular");
    
    protected function getTestFileBasePath()
    {
        return dirname(__FILE__) . "/../../../Resources/tests/model/result";
    }
    
    /**
     * @test
     */
    public function extract()
    {
        $result = new Result();
        $result->setDocs(array(
            new Document(array(
                new String("foo",  "bar"),
                new String("foo2", "bar2"),
            )),
            new Document(array(
                new String("foo",  "bat"),
                new String("foo2", "bat2"),
            )),
            new Document(array(
                new String("foo",  "baz"),
                new String("foo2", "baz2"),
            ))
        ));
        $expectedFoo = array("bar", "bat", "baz");
        $this->assertEquals($expectedFoo, $result->extract("foo"));
    }
}