<?php
namespace Kbrw\RiakBundle\Model\MapReduce;

use JMS\Serializer\Annotation as Ser;

/**
 * @Ser\AccessType("public_method")
 * @Ser\XmlRoot("phase")
 */
class MapReducePhase extends Phase
{
    /**
     * @var string
     * @Ser\Type("string")
     * @Ser\SerializedName("language")
     */
    protected $language;

    /**
     * @var string
     * @Ser\Type("string")
     * @Ser\SerializedName("source")
     */
    protected $source;

    /**
     * @var string
     * @Ser\Type("boolean")
     * @Ser\SerializedName("keep")
     */
    protected $keep;

    /**
     * @var string
     * @Ser\Type("string")
     * @Ser\SerializedName("bucket")
     */
    protected $bucket;

    /**
     * @var string
     * @Ser\Type("string")
     * @Ser\SerializedName("key")
     */
    protected $key;

    /**
     * @var string
     * @Ser\Type("string")
     * @Ser\SerializedName("name")
     */
    protected $name;

    /**
     * @var string
     * @Ser\Type("string")
     * @Ser\SerializedName("module")
     */
    protected $module;

    /**
     * @var string
     * @Ser\Type("string")
     * @Ser\SerializedName("function")
     */
    protected $function;

    /**
     * @var string
     * @Ser\SerializedName("arg")
     */
    protected $arg;

    public function __construct($language = "javascript", $source = null, $keep = null, $bucket = null, $key = null, $name = null, $module = null, $function = null, $arg = null)
    {
        $this->setLanguage($language);
        $this->setSource($source);
        $this->setKeep($keep);
        $this->setBucket($bucket);
        $this->setKey($key);
        $this->setName($name);
        $this->setModule($module);
        $this->setFunction($function);
        $this->setArg($arg);
    }

    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * @return \Kbrw\RiakBundle\Model\MapReduce\MapReducePhase
     */
    public function setLanguage($language)
    {
        $this->language = $language;

        return $this;
    }

    public function getSource()
    {
        return $this->source;
    }

    /**
     * @return \Kbrw\RiakBundle\Model\MapReduce\MapReducePhase
     */
    public function setSource($source)
    {
        $this->source = $source;

        return $this;
    }

    public function getKeep()
    {
        return $this->keep;
    }

    /**
     * @return \Kbrw\RiakBundle\Model\MapReduce\MapReducePhase
     */
    public function setKeep($keep)
    {
        $this->keep = $keep;

        return $this;
    }

    public function getBucket()
    {
        return $this->bucket;
    }

    /**
     * @return \Kbrw\RiakBundle\Model\MapReduce\MapReducePhase
     */
    public function setBucket($bucket)
    {
        $this->bucket = $bucket;

        return $this;
    }

    public function getKey()
    {
        return $this->key;
    }

    /**
     * @return \Kbrw\RiakBundle\Model\MapReduce\MapReducePhase
     */
    public function setKey($key)
    {
        $this->key = $key;

        return $this;
    }

    public function getName()
    {
        return $this->name;
    }

    /**
     * @return \Kbrw\RiakBundle\Model\MapReduce\MapReducePhase
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    public function getModule()
    {
        return $this->module;
    }

    /**
     * @return \Kbrw\RiakBundle\Model\MapReduce\MapReducePhase
     */
    public function setModule($module)
    {
        $this->module = $module;

        return $this;
    }

    public function getFunction()
    {
        return $this->function;
    }

    /**
     * @return \Kbrw\RiakBundle\Model\MapReduce\MapReducePhase
     */
    public function setFunction($function)
    {
        $this->function = $function;

        return $this;
    }

    public function getArg()
    {
        return $this->arg;
    }

    /**
     * @return \Kbrw\RiakBundle\Model\MapReduce\MapReducePhase
     */
    public function setArg($arg)
    {
        $this->arg = $arg;
        
        return $this;
    }
}
