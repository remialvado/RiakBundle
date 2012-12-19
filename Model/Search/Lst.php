<?php
namespace Kbrw\RiakBundle\Model\Search;

use JMS\Serializer\Annotation as Ser;

/**  
 * @Ser\AccessType("public_method")
 * @Ser\XmlRoot("lst")
 */
class Lst
{
    
    /** 
     * @Ser\Type("string") 
     * @Ser\XmlAttribute
     * @Ser\SerializedName("name")
     * @Ser\Since("1")
     */
    protected $name = null;    
    
    /** 
     * @Ser\Type("array<Kbrw\RiakBundle\Model\Search\Integer>") 
     * @Ser\XmlList(inline = true, entry = "int")
     * @Ser\Since("1")
     */
    protected $integers = array();
    
    /** 
     * @Ser\Type("array<Kbrw\RiakBundle\Model\Search\String>") 
     * @Ser\XmlList(inline = true, entry = "str")
     * @Ser\Since("1")
     */
    protected $strings = array();
    
    /** 
     * @Ser\Type("array<Kbrw\RiakBundle\Model\Search\Lst>") 
     * @Ser\XmlList(inline = true, entry = "lst")
     * @Ser\Since("1")
     */
    protected $lsts = array();
    
    function __construct($name = null, $integers = null, $strings = null, $lsts = null)
    {
        $this->setName($name);
        $this->setIntegers($integers);
        $this->setStrings($strings);
        $this->setLsts($lsts);
    }
    
    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getIntegers()
    {
        return $this->integers;
    }

    public function setIntegers($integers)
    {
        $this->integers = $integers;
    }

    public function getStrings()
    {
        return $this->strings;
    }

    public function setStrings($strings)
    {
        $this->strings = $strings;
    }
    
    public function getLsts()
    {
        return $this->lsts;
    }

    public function setLsts($lsts)
    {
        $this->lsts = $lsts;
    }
    
    /**
     * @return \Kbrw\RiakBundle\Model\Search\SimpleType
     */
    public function getSimpleTypeByName($name)
    {
        if (is_array($this->integers)) {
            foreach($this->integers as $int) {
                if ($int->getName() == $name) return $int;
            }
        }
        if (is_array($this->strings)) {
            foreach($this->strings as $str) {
                if ($str->getName() == $name) return $str;
            }
        }
        if (is_array($this->lsts)) {
            foreach($this->lsts as $lst) {
                $simpleType = $lst->getSimpleTypeByName($name);
                if (isset($simpleType)) return $simpleType;
            }
        }
        return null;
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
    
    /**
     * @return array<Kbrw\RiakBundle\Model\Search\SimpleType>
     */
    public function getSimpleTypes()
    {
        $simpleTypes = array();
        if (is_array($this->integers)) $simpleTypes = array_merge($simpleTypes, $this->integers);
        if (is_array($this->strings)) $simpleTypes = array_merge($simpleTypes, $this->strings);
        return $simpleTypes;
    }
    
    /**
     * @return array<Kbrw\RiakBundle\Model\Search\SimpleType>
     */
    public function getSimpleTypesIn($listName = null)
    {
        if (empty($listName)) return $this->getSimpleTypes();
        if (strpos($listName, "->") === FALSE) {
            $list = $this->getListByName($listName);
            if (!isset($list)) return array();
            return $list->getSimpleTypes();
        }
        $listNames = explode("->", $listName);
        $list = $this->getListByName($listNames[0]);
        if (!isset($list)) return array();
        array_shift($listNames);
        return $list->getSimpleTypesIn(join("->", $listNames));
    }
}