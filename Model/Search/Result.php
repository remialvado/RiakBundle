<?php
namespace Kbrw\RiakBundle\Model\Search;

use JMS\Serializer\Annotation as Ser;

/**  
 * @Ser\AccessType("public_method")
 * @Ser\XmlRoot("result")
 */
class Result
{
    
    /** 
     * @Ser\Type("string") 
     * @Ser\XmlAttribute
     * @Ser\SerializedName("name")
     * @Ser\Since("1")
     */
    protected $name = null;
    
    /** 
     * @Ser\Type("integer") 
     * @Ser\XmlAttribute
     * @Ser\SerializedName("numFound")
     * @Ser\Since("1")
     */
    protected $numFound = null;
    
    /** 
     * @Ser\Type("integer") 
     * @Ser\XmlAttribute
     * @Ser\SerializedName("start")
     * @Ser\Since("1")
     */
    protected $start = null;
    
    /** 
     * @Ser\Type("double") 
     * @Ser\XmlAttribute
     * @Ser\SerializedName("maxScore")
     * @Ser\Since("1")
     */
    protected $maxScore = null;
    
    
    /** 
     * @Ser\Type("array<Kbrw\RiakBundle\Model\Search\Document>") 
     * @Ser\XmlList(inline = true, entry = "doc")
     * @Ser\Since("1")
     */
    protected $docs = array();
    
    function __construct($name = null, $numFound = null, $start = null, $maxScore = null, $docs = null)
    {
        $this->setName($name);
        $this->setNumFound($numFound);
        $this->setStart($start);
        $this->setMaxScore($maxScore);
        $this->setDocs($docs);
    }
    
    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getNumFound()
    {
        return $this->numFound;
    }

    public function setNumFound($numFound)
    {
        $this->numFound = $numFound;
    }

    public function getStart()
    {
        return $this->start;
    }

    public function setStart($start)
    {
        $this->start = $start;
    }

    public function getMaxScore()
    {
        return $this->maxScore;
    }

    public function setMaxScore($maxScore)
    {
        $this->maxScore = $maxScore;
    }

    public function getDocs()
    {
        return $this->docs;
    }

    public function setDocs($docs)
    {
        $this->docs = $docs;
    }
}