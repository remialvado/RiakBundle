<?php
namespace Kbrw\RiakBundle\Model\MapReduce;

use JMS\Serializer\Annotation as Ser;

/**
 * @Ser\AccessType("public_method")
 * @Ser\XmlRoot("query")
 */
class Query
{

    const PHASE_MAP    = "map";
    const PHASE_REDUCE = "reduce";
    const PHASE_LINK   = "link";

    /**
     * @var \Kbrw\RiakBundle\Model\MapReduce\Inputs
     * @Ser\SerializedName("inputs")
     */
    protected $inputs;

    /**
     * @Ser\SerializedName("query")
     */
    protected $phases;

    /**
     * @var integer
     * @Ser\Type("integer")
     * @Ser\SerializedName("timeout")
     */
    protected $timeout;

    /**
     * @Ser\Exclude
     * @var string
     */
    protected $responseFullyQualifiedClassName;

    /**
     * @Ser\Exclude
     * @var \Kbrw\RiakBundle\Model\Cluster\Cluster
     */
    protected $cluster;

    public function __construct($cluster = null)
    {
        $inputs = new Inputs($this);
        $this->setInputs($inputs);
        $this->setPhases(array());
        $this->setCluster($cluster);
    }

    /**
     * @Ser\PreSerialize
     */
    public function preSerialize()
    {
        if ($this->inputs->isOnAFullBucket()) {
            $this->inputsBackup = $this->inputs;
            $this->inputs = $this->inputs->getMainBucket();
        } elseif ($this->inputs->isKeySelectionUsed()) {
            $this->inputsBackup = $this->inputs;
            $this->inputs = $this->inputs->getKeySelectionArray();
        }
    }

    /**
     * @Ser\PostSerialize
     */
    public function postSerialize()
    {
        if (isset($this->inputsBackup)) {
            $this->inputs = $this->inputsBackup;
            $this->inputsBackup = null;
        }
    }

    /**
     * @return \Kbrw\RiakBundle\Model\MapReduce\Inputs
     */
    public function on($bucket, $key = null, $data = null)
    {
        $this->inputs->on(new Input($bucket, $key, $data));

        return $this;
    }

    public function filter($bucket)
    {
        return $this->inputs->setBucket($bucket);
    }

    /**
     * @return \Kbrw\RiakBundle\Model\MapReduce\Query
     */
    public function map($source, $keep = null)
    {
        $this->configureMapPhase()->setSource($source)->setKeep($keep);

        return $this;
    }

    /**
     * @return \Kbrw\RiakBundle\Model\MapReduce\MapReducePhase
     */
    public function configureMapPhase()
    {
        $phase = $this->getPhase(self::PHASE_MAP);
        if (!isset($phase)) {
            $phase = $this->addNewMapPhase();
        }

        return $phase;
    }
    
    /**
     * @return \Kbrw\RiakBundle\Model\MapReduce\MapReducePhase
     */
    public function addNewMapPhase()
    {
        $phase = new MapReducePhase();
        $phase->setQuery($this);
        $this->phases[] = new PhaseContainer\MapPhaseContainer($phase);

        return $phase;
    }

    /**
     * @return \Kbrw\RiakBundle\Model\MapReduce\Query
     */
    public function reduce($source, $keep = null)
    {
        $this->configureReducePhase()->setSource($source)->setKeep($keep);

        return $this;
    }

    /**
     * @return \Kbrw\RiakBundle\Model\MapReduce\MapReducePhase
     */
    public function configureReducePhase()
    {
        $phase = $this->getPhase(self::PHASE_REDUCE);
        if (!isset($phase)) {
           $phase = $this->addNewReducePhase();
        }

        return $phase;
    }
    
    /**
     * @return \Kbrw\RiakBundle\Model\MapReduce\MapReducePhase
     */
    public function addNewReducePhase()
    {
        $phase = new MapReducePhase();
        $phase->setQuery($this);
        $this->phases[] = new PhaseContainer\ReducePhaseContainer($phase);

        return $phase;
    }

    /**
     * @return \Kbrw\RiakBundle\Model\MapReduce\Query
     */
    public function link($bucket = "_", $tag = null, $keep = null)
    {
        $this->configureLinkPhase()->setBucket($bucket)->setTag($tag)->setKeep($keep);

        return $this;
    }

    /**
     * @return \Kbrw\RiakBundle\Model\MapReduce\LinkPhase
     */
    public function configureLinkPhase()
    {
        $phase = $this->getPhase(self::PHASE_LINK);
        if (!isset($phase)) {
            $phase = new LinkPhase();
            $phase->setQuery($this);
            $this->phases[] = new PhaseContainer\LinkPhaseContainer($phase);
        }

        return $phase;
    }

    /**
     * @return mixed
     */
    public function send()
    {
        return $this->cluster->getRiakMapReduceServiceClient()->mapReduce($this->cluster, $this);
    }

    /**
     * @param  string                                 $key
     * @return \Kbrw\RiakBundle\Model\MapReduce\Phase
     */
    public function getPhase($key)
    {
        foreach ($this->phases as $phaseContainer) {
            if ($phaseContainer->getType() === $key) {
                return $phaseContainer->getPhase();
            }
        }

        return null;
    }

    /**
     * @return \Kbrw\RiakBundle\Model\MapReduce\Inputs
     */
    public function getInputs()
    {
        return $this->inputs;
    }

    /**
     * @param \Kbrw\RiakBundle\Model\MapReduce\Inputs $inputs
     */
    public function setInputs($inputs)
    {
        $this->inputs = $inputs;
    }

    /**
     * @return array<\Kbrw\RiakBundle\Model\MapReduce\PhaseContainer>
     */
    public function getPhases()
    {
        return $this->phases;
    }

    public function setPhases($phases)
    {
        $this->phases = $phases;
    }

    public function getTimeout()
    {
        return $this->timeout;
    }

    public function setTimeout($timeout)
    {
        $this->timeout = $timeout;
    }

    /**
     * @return \Kbrw\RiakBundle\Model\MapReduce\Query
     */
    public function timeout($timeout)
    {
        $this->setTimeout($timeout);

        return $this;
    }

    public function getResponseFullyQualifiedClassName()
    {
        return $this->responseFullyQualifiedClassName;
    }

    public function setResponseFullyQualifiedClassName($responseFullyQualifiedClassName)
    {
        $this->responseFullyQualifiedClassName = $responseFullyQualifiedClassName;
    }

    /**
     * @return \Kbrw\RiakBundle\Model\MapReduce\Query
     */
    public function responseShouldBe($responseFullyQualifiedClassName)
    {
        $this->setResponseFullyQualifiedClassName($responseFullyQualifiedClassName);

        return $this;
    }

    public function getCluster()
    {
        return $this->cluster;
    }

    public function setCluster($cluster)
    {
        $this->cluster = $cluster;
    }
}
