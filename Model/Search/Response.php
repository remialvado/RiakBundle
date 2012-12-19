<?php
namespace Kbrw\RiakBundle\Model\Search;

use JMS\Serializer\Annotation as Ser;

/**  
 * @Ser\AccessType("public_method")
 * @Ser\XmlRoot("response")
 */
class Response
{
    
    /** 
     * @Ser\Type("Kbrw\RiakBundle\Model\Search\Result") 
     * @Ser\SerializedName("result")
     * @Ser\Since("1")
     */
    protected $result = null;
    
    /** 
     * @Ser\Type("array<Kbrw\RiakBundle\Model\Search\Lst>") 
     * @Ser\XmlList(inline = true, entry = "lst")
     * @Ser\Since("1")
     */
    protected $lsts = array();
    
    function __construct($result = null, $lsts = null)
    {
        $this->setResult($result);
        $this->setLsts($lsts);
    }

    /**
     * @return \Kbrw\RiakBundle\Model\Search\Result
     */
    public function getResult()
    {
        return $this->result;
    }

    public function setResult($result)
    {
        $this->result = $result;
    }
    
    /**
     * @return array<\Kbrw\RiakBundle\Model\Search\Lst>
     */
    public function getLsts()
    {
        return $this->lsts;
    }

    public function setLsts($lsts)
    {
        $this->lsts = $lsts;
    }
    
    /**
     * @return \Kbrw\RiakBundle\Model\Search\Lst
     */
    public function getListByName($name)
    {
        if (is_array($this->lsts)) {
            foreach($this->lsts as $lst) {
                if ($lst->getName() === $name) return $lst;
            }
        }
        return null;
    }
}