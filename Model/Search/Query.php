<?php
namespace Kbrw\RiakBundle\Model\Search;

class Query
{
    protected $query;
    protected $fieldsList;
    protected $defaultField;
    protected $operation;
    protected $start;
    protected $rows;
    protected $sort;
    protected $wt;
    protected $filter;
    protected $additionalParameters;
    
    function __construct($query = null, $fieldsList = array(), $start = 0, $rows = 10, $defaultField = null, $operation = null, $sort = null, $wt = "xml", $filter = null, $additionalParameters = array())
    {
        $this->setQuery($query);
        if (!is_array($fieldsList)) $fieldsList = array($fieldsList);
        $this->setFieldsList($fieldsList);
        $this->setDefaultField($defaultField);
        $this->setOperation($operation);
        $this->setStart($start);
        $this->setRows($rows);
        $this->setSort($sort);
        $this->setWt($wt);
        $this->setFilter($filter);
        $this->setAdditionalParameters($additionalParameters);
    }
    
    public function getConfig()
    {
        $config = array();
        if(!empty($this->query))        $config["q"]      = $this->query;
        if(!empty($this->start))        $config["start"]  = $this->start;
        if(!empty($this->rows))         $config["rows"]   = $this->rows;
        if(!empty($this->defaultField)) $config["df"]     = $this->defaultField;
        if(!empty($this->operation))    $config["q.op"]   = $this->operation;
        if(!empty($this->sort))         $config["sort"]   = $this->sort;
        if(!empty($this->wt))           $config["wt"]     = $this->wt;
        if(!empty($this->filter))       $config["filter"] = $this->filter;
        if(count($this->fieldsList) > 0)           $config["fl"]                   = join(",", $this->fieldsList);
        if(count($this->additionalParameters) > 0) $config["additionalParameters"] = $this->additionalParameters;
        return $config;
    }
    
    public function getQuery()
    {
        return $this->query;
    }

    public function setQuery($query)
    {
        $this->query = $query;
    }
    
    public function getFieldsList()
    {
        return $this->fieldsList;
    }

    public function setFieldsList($fieldsList)
    {
        $this->fieldsList = $fieldsList;
    }
    
    public function addFieldInList($fl)
    {
        return $this->fieldsList[] = $fl;
    }
    
    public function getDefaultField()
    {
        return $this->defaultField;
    }

    public function setDefaultField($defaultField)
    {
        $this->defaultField = $defaultField;
    }

    public function getOperation()
    {
        return $this->operation;
    }

    public function setOperation($operation)
    {
        $this->operation = $operation;
    }

    public function getStart()
    {
        return $this->start;
    }

    public function setStart($start)
    {
        $this->start = $start;
    }

    public function getRows()
    {
        return $this->rows;
    }

    public function setRows($rows)
    {
        $this->rows = $rows;
    }

    public function getSort()
    {
        return $this->sort;
    }

    public function setSort($sort)
    {
        $this->sort = $sort;
    }

    public function getWt()
    {
        return $this->wt;
    }

    public function setWt($wt)
    {
        $this->wt = $wt;
    }

    public function getFilter()
    {
        return $this->filter;
    }

    public function setFilter($filter)
    {
        $this->filter = $filter;
    }

    public function getAdditionalParameters()
    {
        return $this->additionalParameters;
    }

    public function setAdditionalParameters($additionalParameters)
    {
        $this->additionalParameters = $additionalParameters;
    }
    
    public function addAdditionnalParameter($name, $value)
    {
        $this->additionalParameters[$name] = $value;
    }
}