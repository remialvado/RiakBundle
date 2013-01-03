<?php
namespace Kbrw\RiakBundle\Model\MapReduce;

use JMS\Serializer\Annotation as Ser;

/**
 * @Ser\AccessType("public_method")
 * @Ser\XmlRoot("input")
 */
class Inputs
{   
    /**
     * @Ser\Type("string")
     * @Ser\SerializedName("bucket")
     * @var string
     */
    protected $bucket;
    
    /**
     * @Ser\Type("array<mixed>")
     * @Ser\SerializedName("key_filters")
     * @var \Kbrw\RiakBundle\Model\MapReduce\Operator\Operator
     * As a matter of fact, keyFilters is an Operator (Root, And, Or or Not) but 
     * is replaced by an array before serialization and reverted to its original 
     * value after serialization.
     */
    protected $keyFilters;
    
    /**
     * @Ser\Exclude
     * @var \Kbrw\RiakBundle\Model\MapReduce\InputList
     */
    protected $inputList;
    
    /**
     * @Ser\Exclude
     * @var \Kbrw\RiakBundle\Model\MapReduce\Operator\Operator
     */
    protected $currentElement;
    
    /**
     * @Ser\Exclude
     * @var \Kbrw\RiakBundle\Model\MapReduce\Query 
     */
    protected $query;
    
    function __construct($query = null)
    {
        $this->setQuery($query);
        $this->inputList = new InputList();
        $this->keyFilters = $this->currentElement = new Operator\Root();
    }
    
    /**
     * @Ser\PreSerialize
     */
    public function preSerialize()
    {
        $this->keyFiltersBackup = clone $this->keyFilters;
        $this->keyFilters = $this->keyFilters->toArray();
    }
    
    /**
     * @Ser\PostSerialize
     */
    public function postSerialize()
    {
        $this->keyFilters = $this->keyFiltersBackup;
    }
    
    /**
     * @return \Kbrw\RiakBundle\Model\MapReduce\Inputs
     */
    public function on($input)
    {
        $this->inputList->addInput($input);
        return $this;
    }
    
    public function isKeySelectionUsed()
    {
        return $this->inputList->isDefined();
    }
    
    public function getKeySelectionArray()
    {
        return $this->inputList->toArray();
    }
    
    /**
     * @param \Kbrw\RiakBundle\Model\MapReduce\KeyFilter $keyFilter
     * @param boolean $defineAsCurrent
     * @return \Kbrw\RiakBundle\Model\MapReduce\Inputs
     */
    protected function addKeyFilter($keyFilter, $defineAsCurrent = false)
    {
        $this->currentElement->addKeyFilter($keyFilter);
        if ($defineAsCurrent) {
            $this->currentElement = $keyFilter;
        }
        return $this;
    }
    
    /**
     * @return \Kbrw\RiakBundle\Model\MapReduce\Inputs
     */
    public function intToString()
    {
        return $this->addKeyFilter(new Transformer\IntToString($this->currentElement));
    }
    
    /**
     * @return \Kbrw\RiakBundle\Model\MapReduce\Inputs
     */
    public function stringToInt()
    {
        return $this->addKeyFilter(new Transformer\StringToInt($this->currentElement));
    }
    
    /**
     * @return \Kbrw\RiakBundle\Model\MapReduce\Inputs
     */
    public function floatToString()
    {
        return $this->addKeyFilter(new Transformer\FloatToString($this->currentElement));
    }
    
    /**
     * @return \Kbrw\RiakBundle\Model\MapReduce\Inputs
     */
    public function stringToFloat()
    {
        return $this->addKeyFilter(new Transformer\StringToFloat($this->currentElement));
    }
    
    /**
     * @return \Kbrw\RiakBundle\Model\MapReduce\Inputs
     */
    public function toUpper()
    {
        return $this->addKeyFilter(new Transformer\ToUpper($this->currentElement));
    }
    
    /**
     * @return \Kbrw\RiakBundle\Model\MapReduce\Inputs
     */
    public function toLower()
    {
        return $this->addKeyFilter(new Transformer\ToLower($this->currentElement));
    }
    
    /**
     * @return \Kbrw\RiakBundle\Model\MapReduce\Inputs
     */
    public function tokenize($separator, $position)
    {
        return $this->addKeyFilter(new Transformer\Tokenize($this->currentElement, $separator, $position));
    }
    
    /**
     * @return \Kbrw\RiakBundle\Model\MapReduce\Inputs
     */
    public function urlDecode()
    {
        return $this->addKeyFilter(new Transformer\UrlDecode($this->currentElement));
    }
    
    /**
     * @return \Kbrw\RiakBundle\Model\MapReduce\Inputs
     */
    public function greaterThan($value)
    {
        return $this->addKeyFilter(new Predicate\GreaterThan($this->currentElement, $value));
    }
    
    /**
     * @return \Kbrw\RiakBundle\Model\MapReduce\Inputs
     */
    public function lessThan($value)
    {
        return $this->addKeyFilter(new Predicate\LessThan($this->currentElement, $value));
    }
    
    /**
     * @return \Kbrw\RiakBundle\Model\MapReduce\Inputs
     */
    public function greaterThanEq($value)
    {
        return $this->addKeyFilter(new Predicate\GreaterThanEq($this->currentElement, $value));
    }
    
    /**
     * @return \Kbrw\RiakBundle\Model\MapReduce\Inputs
     */
    public function lessThanEq($value)
    {
        return $this->addKeyFilter(new Predicate\LessThanEq($this->currentElement, $value));
    }
    
    /**
     * @return \Kbrw\RiakBundle\Model\MapReduce\Inputs
     */
    public function between($min, $max, $inclusive = true)
    {
        return $this->addKeyFilter(new Predicate\Between($this->currentElement, $min, $max, $inclusive));
    }
    
    /**
     * @return \Kbrw\RiakBundle\Model\MapReduce\Inputs
     */
    public function matches($value)
    {
        return $this->addKeyFilter(new Predicate\Matches($this->currentElement, $value));
    }
    
    /**
     * @return \Kbrw\RiakBundle\Model\MapReduce\Inputs
     */
    public function notEquals($value)
    {
        return $this->addKeyFilter(new Predicate\NotEquals($this->currentElement, $value));
    }
    
    /**
     * @return \Kbrw\RiakBundle\Model\MapReduce\Inputs
     */
    public function equals($value)
    {
        return $this->addKeyFilter(new Predicate\Equals($this->currentElement, $value));
    }
    
    /**
     * @return \Kbrw\RiakBundle\Model\MapReduce\Inputs
     */
    public function setMember($set)
    {
        return $this->addKeyFilter(new Predicate\MemberOf($this->currentElement, $set));
    }
    
    /**
     * @return \Kbrw\RiakBundle\Model\MapReduce\Inputs
     */
    public function similarTo($value, $distance)
    {
        return $this->addKeyFilter(new Predicate\SimilarTo($this->currentElement, $value, $distance));
    }
    
    /**
     * @return \Kbrw\RiakBundle\Model\MapReduce\Inputs
     */
    public function startsWith($value)
    {
        return $this->addKeyFilter(new Predicate\StartsWith($this->currentElement, $value));
    }
    
    /**
     * @return \Kbrw\RiakBundle\Model\MapReduce\Inputs
     */
    public function endsWith($value)
    {
        return $this->addKeyFilter(new Predicate\EndsWith($this->currentElement, $value));
    }
    
    /**
     * @return \Kbrw\RiakBundle\Model\MapReduce\Inputs
     */
    public function logicalAnd()
    {
        return $this->addKeyFilter(new Operator\LogicalAnd($this->currentElement), true);
    }
    
    /**
     * @return \Kbrw\RiakBundle\Model\MapReduce\Inputs
     */
    public function logicalOr()
    {
        return $this->addKeyFilter(new Operator\LogicalOr($this->currentElement), true);
    }
    
    /**
     * @return \Kbrw\RiakBundle\Model\MapReduce\Inputs
     */
    public function not()
    {
        return $this->addKeyFilter(new Operator\Not($this->currentElement), true);
    }
    
    /**
     * @return \Kbrw\RiakBundle\Model\MapReduce\Inputs
     */
    public function end()
    {
        $this->currentElement = $this->currentElement->getParent();
        return $this;
    }
    
    /**
     * @return \Kbrw\RiakBundle\Model\MapReduce\Query 
     */
    public function done()
    {
        return $this->query;
    }

    public function getKeyFilters()
    {
        return $this->keyFilters;
    }

    public function setKeyFilters($keyFilters)
    {
        $this->keyFilters = $keyFilters;
        return $this;
    }
    
    public function getCurrentElement()
    {
        return $this->currentElement;
    }

    public function setCurrentElement($currentElement)
    {
        $this->currentElement = $currentElement;
        return $this;
    }
    
    public function getQuery()
    {
        return $this->query;
    }

    public function setQuery($query)
    {
        $this->query = $query;
        return $this;
    }
    
    public function getBucket()
    {
        return $this->bucket;
    }

    public function setBucket($bucket)
    {
        $this->bucket = $bucket;
        return $this;
    }

    public function getInputList()
    {
        return $this->inputList;
    }

    public function setInputList(\Kbrw\RiakBundle\Model\MapReduce\InputList $inputList)
    {
        $this->inputList = $inputList;
        return $this;
    }
}