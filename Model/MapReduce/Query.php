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

    public function __construct()
    {
        $inputs = new Inputs($this);
        $this->setInputs($inputs);
        $this->setPhases(array());
    }

    /**
     * @Ser\PreSerialize
     */
    public function preSerialize()
    {
        if ($this->inputs->isKeySelectionUsed()) {
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
     * @return \Kbrw\RiakBundle\Model\MapReduce\MapReducePhase
     */
    public function map($source = null)
    {
        $phase = $this->getPhase(self::PHASE_MAP);
        if (!isset($phase)) {
            $phase = new MapReducePhase();
            $phase->setQuery($this);
            $this->phases[] = new PhaseContainer\MapPhaseContainer($phase);
        }
        if (!empty($source)) $phase->setSource($source);
        return $phase;
    }

    /**
     * @return \Kbrw\RiakBundle\Model\MapReduce\MapReducePhase
     */
    public function reduce($source = null)
    {
        $phase = $this->getPhase(self::PHASE_REDUCE);
        if (!isset($phase)) {
            $phase = new MapReducePhase();
            $phase->setKeep(true)->setQuery($this);
            $this->phases[] = new PhaseContainer\ReducePhaseContainer($phase);
        }
        if (!empty($source)) $phase->setSource($source);
        return $phase;
    }

    /**
     * @return \Kbrw\RiakBundle\Model\MapReduce\LinkPhase
     */
    public function link()
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

    /**
     * @return \Kbrw\RiakBundle\Model\MapReduce\Query
     */
    public function setTimeout($timeout)
    {
        $this->timeout = $timeout;

        return $this;
    }
}
